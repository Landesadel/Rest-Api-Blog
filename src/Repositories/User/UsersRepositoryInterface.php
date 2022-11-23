<?php

namespace Landesadel\easyBlog\Repositories\User;

use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Uuid;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(Uuid $uuid): User;
    public function getUsername(string $username): User;

}