<?php


namespace Landesadel\easyBlog\http\Auth;


use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}