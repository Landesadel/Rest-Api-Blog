<?php


namespace Landesadel\easyBlog\http\Actions\Post;


use Landesadel\easyBlog\Exceptions\PostNotFoundException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Uuid;

class DeletePost implements ActionInterface
{


    public function __construct(
        private PostRepositoryInterface $postRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $postUuid = $request->query('uuid');
            $this->postRepository->get(new Uuid($postUuid));

        }catch(PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postRepository->delete(new Uuid($postUuid));

        return new SuccessfulResponse([
            'uuid' => $postUuid
        ]);
    }
}