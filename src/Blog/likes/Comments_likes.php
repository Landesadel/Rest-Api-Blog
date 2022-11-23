<?php


namespace Landesadel\easyBlog\Blog\likes;


use Landesadel\easyBlog\Blog\Comment\Comment;

class Comments_likes extends likes
{

    public function __construct(
        $uuid,
        $user_uuid,
        private Comment $uuid_comment,
    )
    {
        parent::__construct($uuid, $user_uuid);
    }

    public function getComment(): Comment
    {
        return $this->uuid_comment;
    }
}