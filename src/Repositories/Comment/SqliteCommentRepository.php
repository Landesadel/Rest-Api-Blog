<?php

namespace Landesadel\easyBlog\Repositories\Comment;

use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Comment\Comment;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Exceptions\CommentNotFoundException;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\Repositories\Post\SqlitePostsRepository;
use Landesadel\easyBlog\Repositories\User\SqliteUsersRepository;
use Landesadel\easyBlog\Uuid;
use Psr\Log\LoggerInterface;

class SqliteCommentRepository implements CommentRepositoryInterface
{

    public function __construct(
        private  \PDO $connection,
        private LoggerInterface $logger,
    ){
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO  comments(uuid, post_uuid, author_uuid, text) 
                   VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid'=> (string)$comment->getId(),
            ':post_uuid'=> (string)$comment->getIdPost()->getId(),
            ':author_uuid'=> (string)$comment->getUserId()->getId(),
            ':text'=> $comment->getText(),
        ]);

        $this->logger->info("Comment created: {$comment->getId()}");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(Uuid $comment_uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$comment_uuid,
        ]);

        return $this->getComment($statement, $comment_uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    private function getComment(\PDOStatement $statement, string $commentUuId): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $this->logger->warning("Cannot find comment: $commentUuId");
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuId"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->get(new Uuid($result['author_uuid']));

        $postRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postRepository->get(new Uuid($result['post_uuid']));

        return new Comment(
            new Uuid($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }

    public function delete(Uuid $uuid): void{
        $statement = $this->connection->prepare('DELETE FROM comments WHERE uuid = :uuid');
        $statement->execute([
            'uuid'=> (string)$uuid,
        ]);
        $this->logger->info("Comment deleted: {$uuid}");
    }
}