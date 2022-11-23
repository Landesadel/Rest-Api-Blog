<?php


namespace Landesadel\easyBlog\http\Actions\Post\Likes;


use Landesadel\easyBlog\Exceptions\LikeNotFoundException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Repositories\Post\Likes\LikesPostRepositoryInterface;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\SuccessfulResponse;

class DeletePostLikes implements ActionInterface
{

    public function __construct(
        private LikesPostRepositoryInterface $likesPostRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $postLikeUuid = $request->query('uuid');
            $this->likesPostRepository->getByPostUuid(new Uuid($postLikeUuid));

        }catch(LikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesPostRepository->delete(new Uuid($postLikeUuid));

        return new SuccessfulResponse([
            'uuid' => $postLikeUuid
        ]);
    }
}