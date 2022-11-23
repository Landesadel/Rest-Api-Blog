<?php

namespace Landesadel\easyBlog\Repositories\Post;

use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\Exceptions\PostNotFoundException;
use Landesadel\easyBlog\Exceptions\PostRepositoryException;
use Landesadel\easyBlog\Repositories\User\SqliteUsersRepository;
use Landesadel\easyBlog\Uuid;
use PDOException;
use Psr\Log\LoggerInterface;


class SqlitePostsRepository implements PostRepositoryInterface
{


    public function __construct(
        private  \PDO $connection,
        private LoggerInterface $logger,
    ){
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO  posts(uuid, author_uuid, title, text) 
                   VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid'=> (string)$post->getId(),
            ':author_uuid'=> $post->getUserId()->getId(),
            ':title'=> $post->getTitle(),
            ':text'=> $post->getText()
        ]);

        $this->logger->info("Post created: {$post->getId()}");
    }


    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(Uuid $post_uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$post_uuid,
        ]);

        return $this->getPost($statement, $post_uuid);
    }


    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(\PDOStatement $statement, string $postUuId): Post
    {
         $result = $statement->fetch(\PDO::FETCH_ASSOC);
         if ($result === false) {
             $message = "Cannot find post: $postUuId";
             $this->logger->warning($message);
             throw new PostNotFoundException($message);
         }

         $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
         $user = $userRepository->get(new Uuid($result['author_uuid']));

         return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
         );
    }

    /**
     * @throws PostRepositoryException
     */
    public function delete(Uuid $uuid): void{
        try {
            $statement = $this->connection->prepare('DELETE FROM posts WHERE uuid = :uuid');
            $statement->execute([
                'uuid' => (string)$uuid,
            ]);
        }catch (PDOException $e) {
            throw new PostRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
        $this->logger->info("Post deleted: {$uuid}");
    }
}