<?php

namespace Landesadel\easyBlog\Repositories\User;

use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Uuid;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(Uuid $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getUsername(string $username): User
    {
        return new User(UUID::random(), new Name("first", "last"), "user123", 123);
    }
}