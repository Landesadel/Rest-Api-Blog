<?php


namespace Landesadel\easyBlog\http\Actions;


use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}