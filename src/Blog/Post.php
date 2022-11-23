<?php

namespace Landesadel\easyBlog\Blog;

use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Uuid;

class Post
{

    public function __construct(
        private Uuid   $uuid,
        private User  $userId,
        private string $title,
        private string $text
    ){
    }

    public function __toString() {
        return $this->getUserId()->getUsername() . "\n" . $this->title . ': ' . $this->text;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return User
     */
    public function getUserId(): User
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


}