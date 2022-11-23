<?php


namespace Landesadel\easyBlog\Blog\likes;


use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Uuid;

class likes
{

    public function __construct(
        protected Uuid $uuid,
        protected User $uuid_user,
    )
    {
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }

    public function getUserId(): User
    {
        return $this->uuid_user;
    }

    public function __toString() {
        return $this->getUserId()->getUsername() . ": liked";
    }
}