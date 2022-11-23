<?php


namespace Landesadel\easyBlog\Repositories\Post\Likes;


use Landesadel\easyBlog\Blog\likes\Posts_likes;
use Landesadel\easyBlog\Uuid;

interface LikesPostRepositoryInterface
{
    public function save(Posts_likes $post_like): void;
    public function getByPostUuid(Uuid $post_uuid): array;
    public function checkUsersLikesForPost($post_uuid, $user_uuid): void;
}