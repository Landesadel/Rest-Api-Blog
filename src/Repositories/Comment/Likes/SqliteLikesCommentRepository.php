<?php


namespace Landesadel\easyBlog\Repositories\Comment\Likes;


use Landesadel\easyBlog\Blog\likes\Comments_likes;
use Landesadel\easyBlog\Exceptions\likeAlreadyExist;
use Landesadel\easyBlog\Exceptions\LikeNotFoundException;
use Landesadel\easyBlog\Uuid;
use Psr\Log\LoggerInterface;

class SqliteLikesCommentRepository implements LikesCommentRepositoryInterface
{

    public function __construct(
        private  \PDO $connection,
        private LoggerInterface $logger,
    ){
    }

    public function save(Comments_likes $comment_like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO  comment_likes(uuid, uuid_user, uuid_comment) 
                   VALUES (:uuid, :uuid_user, :uuid_comment)'
        );

        $statement->execute([
            ':uuid'=> (string)$comment_like->getId(),
            ':uuid_user'=> $comment_like->getUserId()->getId(),
            ':uuid_comment'=>$comment_like->getComment()->getId()
        ]);

        $this->logger->info("Comment like created: {$comment_like->getId()}");
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByCommentUuid(Uuid $comment_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * 
                    FROM comment_likes 
                    WHERE comment_likes.uuid_comment = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$comment_uuid,
        ]);

        $result = $statement->fetchAll();

        if(!$result) {
            $this->logger->warning("This comment hasn't likes: {$comment_uuid}");
            throw new LikeNotFoundException(
                "This comment hasn't likes: {$comment_uuid}"
            );
        }

        return $result;
    }

    /**
     * @throws likeAlreadyExist
     */
    public function checkUsersLikesForComment($comment_uuid, $user_uuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * 
                    FROM comment_likes 
                    WHERE uuid_comment = :uuid_comment AND uuid_user = :uuid_user'
        );

        $statement->execute([
            ":uuid_comment" => $comment_uuid,
            ":uuid_user" => $user_uuid
        ]);

        $isExist = $statement->fetch();

        if ($isExist) {
            $this->logger->warning("This user has already liked for this comment");
            throw new likeAlreadyExist(
                "This user has already liked for this comment"
            );
        }
    }

    public function delete(Uuid $uuid): void{
        $statement = $this->connection->prepare('DELETE FROM comment_likes WHERE uuid = :uuid');
        $statement->execute([
            'uuid'=> (string)$uuid,
        ]);

        $this->logger->info("Comment like deleted: {$uuid}");
    }
}