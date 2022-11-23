<?php

namespace Landesadel\easyBlog\Repositories\User;

use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Uuid;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];


    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function get(Uuid $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");

    }
}