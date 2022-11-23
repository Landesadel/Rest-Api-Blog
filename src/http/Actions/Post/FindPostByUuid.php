<?php


namespace Landesadel\easyBlog\http\Actions\Post;


use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\PostNotFoundException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\SuccessfulResponse;


class FindPostByUuid implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,

    ){
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException $e) {

            return new ErrorResponse($e->getMessage());
        }
        try {

            $post = $this->postRepository->get(new Uuid($postUuid));

        } catch (PostNotFoundException $e) {

            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);
    }
}