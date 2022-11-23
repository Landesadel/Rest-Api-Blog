<?php


namespace Landesadel\easyBlog\http\Actions\Comment\Likes;


use Landesadel\easyBlog\Exceptions\LikeNotFoundException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\Repositories\Comment\Likes\LikesCommentRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\SuccessfulResponse;

class DeleteCommentLikes implements ActionInterface
{

    public function __construct(
        private LikesCommentRepositoryInterface $likesCommentRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $commentLikeUuid = $request->query('uuid');
            $this->likesCommentRepository->getByCommentUuid(new Uuid($commentLikeUuid));

        }catch(LikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesCommentRepository->delete(new Uuid($commentLikeUuid));

        return new SuccessfulResponse([
            'uuid' => $commentLikeUuid
        ]);
    }
}