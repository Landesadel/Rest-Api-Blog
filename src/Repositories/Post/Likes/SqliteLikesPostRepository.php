<?php


namespace Landesadel\easyBlog\Repositories\Post\Likes;


use Landesadel\easyBlog\Blog\likes\Posts_likes;
use Landesadel\easyBlog\Exceptions\likeAlreadyExist;
use Landesadel\easyBlog\Exceptions\LikeNotFoundException;
use Landesadel\easyBlog\Uuid;
use Psr\Log\LoggerInterface;

class SqliteLikesPostRepository implements LikesPostRepositoryInterface
{

    public function __construct(
        private  \PDO $connection,
        private LoggerInterface $logger,
    ){
    }

    public function save(Posts_likes $post_like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO  post_likes(uuid, uuid_user, uuid_post) 
                   VALUES (:uuid, :uuid_user, :uuid_post)'
        );

        $statement->execute([
            ':uuid'=> (string)$post_like->getId(),
            ':uuid_user'=> $post_like->getUserId()->getId(),
            ':uuid_post'=>$post_like->getPost()->getId()
        ]);
        $this->logger->info("Post created: {$post_like->getId()}");
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(Uuid $post_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * 
                    FROM post_likes 
                    WHERE post_likes.uuid_post = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$post_uuid,
        ]);

        $result = $statement->fetchAll();

        if(!$result) {
            $this->logger->warning("This post hasn't likes: {$post_uuid}");
            throw new LikeNotFoundException(
                "This post hasn't likes: {$post_uuid}"
            );
        }

        return $result;
    }

    /**
     * @throws likeAlreadyExist
     */
    public function checkUsersLikesForPost($post_uuid, $user_uuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * 
                    FROM post_likes 
                    WHERE post_likes.uuid_post = :uuid_post AND uuid_user = :uuid_user'
        );

        $statement->execute([
            ":uuid_post" => $post_uuid,
            ":uuid_user" => $user_uuid
        ]);

        $isExist = $statement->fetch();

        if($isExist) {
            $this->logger->warning("This user has already liked for this post");
            throw new likeAlreadyExist(
                "This user has already liked for this post"
            );
        }
    }

    public function delete(Uuid $uuid): void{
        $statement = $this->connection->prepare('DELETE FROM post_likes WHERE uuid = :uuid');
        $statement->execute([
            'uuid'=> (string)$uuid,
        ]);
        $this->logger->info("Like deleted: {$uuid}");
    }
}