<?php


namespace Landesadel\easyBlog\Repositories\AuthToken;


use Landesadel\easyBlog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
    public function logout(string $token): void;

}