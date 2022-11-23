<?php


namespace Landesadel\easyBlog\Blog\likes;


use Landesadel\easyBlog\Blog\Post;

class Posts_likes extends likes
{

    public function __construct(
        $uuid,
        $user_uuid,
        private Post $uuid_post,
    )
    {
        parent::__construct($uuid, $user_uuid);
    }

    public function getPost(): Post
    {
        return $this->uuid_post;
    }
}