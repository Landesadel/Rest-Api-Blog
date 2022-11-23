<?php

namespace Landesadel\easyBlog\Blog\Comment;

use JetBrains\PhpStorm\Pure;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Uuid;

class Comment
{

    public function __construct(
        private Uuid   $comment_uuid,
        private Post   $post_uuid,
        private User   $author_uuid,
        private string $text
    ){
    }

   public function __toString() {
        return $this->getUserId()->getUsername() . ' пишет: ' . $this->text;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->comment_uuid;
    }

    /**
     * @return User
     */
    public function getUserId(): User
    {
        return $this->author_uuid;
    }

    /**
     * @return Post
     */
    public function getIdPost(): Post
    {
        return $this->post_uuid;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


}