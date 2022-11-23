<?php


namespace Landesadel\easyBlog\http\Actions\Comment\Likes;


use Landesadel\easyBlog\Blog\likes\Comments_likes;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\likeAlreadyExist;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Repositories\Comment\CommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\Comment\Likes\LikesCommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\http\ErrorResponse;

class CreateCommentLikes implements ActionInterface
{

    public function __construct(
        private LikesCommentRepositoryInterface $likesCommentRepository,
        private TokenAuthenticationInterface $authentication,
        private CommentRepositoryInterface $commentRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
             $user = $this->authentication->user($request);

              $uuid_comment = new Uuid($request->jsonBodyField('uuid_comment'));
              $comment = $this->commentRepository->get($uuid_comment);

              $newLikeUuid = Uuid::random();

        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesCommentRepository->checkUsersLikesForComment($uuid_comment,$uuid_user);
        }catch (likeAlreadyExist $e){
            return new ErrorResponse($e->getMessage());
        }

        $like = new Comments_likes(
            $newLikeUuid,
            $user,
            $comment
        );

        $this->likesCommentRepository->save($like);



        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}