<?php


namespace Landesadel\easyBlog\http\Auth;


use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}