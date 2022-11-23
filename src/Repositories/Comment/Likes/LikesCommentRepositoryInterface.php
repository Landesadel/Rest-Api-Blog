<?php


namespace Landesadel\easyBlog\Repositories\Comment\Likes;


use Landesadel\easyBlog\Blog\likes\Comments_likes;
use Landesadel\easyBlog\Uuid;

interface LikesCommentRepositoryInterface
{
    public function save(Comments_likes $comment_like): void;
    public function getByCommentUuid(Uuid $comment_uuid): array;
    public function checkUsersLikesForComment($comment_uuid, $user_uuid): void;
}