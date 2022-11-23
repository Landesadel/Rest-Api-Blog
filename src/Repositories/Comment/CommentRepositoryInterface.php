<?php

namespace Landesadel\easyBlog\Repositories\Comment;


use Landesadel\easyBlog\Blog\Comment\Comment;
use Landesadel\easyBlog\Uuid;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(Uuid $comment_uuid): Comment;
}

