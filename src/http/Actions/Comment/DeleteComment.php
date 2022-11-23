<?php


namespace Landesadel\easyBlog\http\Actions\Comment;


use Landesadel\easyBlog\Exceptions\CommentNotFoundException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Repositories\Comment\CommentRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\SuccessfulResponse;

class DeleteComment
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ){
    }

    /**
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try{
            $commentUuid = $request->query('uuid');
            $this->commentRepository->get(new Uuid($commentUuid));

        }catch(CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentRepository->delete(new Uuid($commentUuid));

        return new SuccessfulResponse([
            'uuid' => $commentUuid
        ]);
    }
}