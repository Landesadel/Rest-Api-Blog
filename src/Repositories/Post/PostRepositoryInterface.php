<?php

namespace Landesadel\easyBlog\Repositories\Post;


use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Uuid;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(Uuid $post_uuid): Post;
    public function delete(Uuid $post_uuid): void;

}