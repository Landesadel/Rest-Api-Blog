<?php


namespace Landesadel\easyBlog\http\Actions\Post\Likes;


use Landesadel\easyBlog\Blog\likes\Posts_likes;
use Landesadel\easyBlog\Exceptions\AppException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\likeAlreadyExist;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Repositories\Post\Likes\LikesPostRepositoryInterface;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\http\ErrorResponse;

class CreatePostLikes implements ActionInterface
{

    public function __construct(
        private LikesPostRepositoryInterface $likesPostRepository,
        private TokenAuthenticationInterface $authentication,
        private PostRepositoryInterface $PostRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $uuid_post = new Uuid($request->jsonBodyField('uuid_post'));
            $post = $this->PostRepository->get($uuid_post);

            $user = $this->authentication->user($request);

            $newLikeUuid = Uuid::random();

        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesPostRepository->checkUsersLikesForPost($uuid_post, $uuid_user);
        }catch (likeAlreadyExist $e){
            return new ErrorResponse($e->getMessage());
        }

        $like = new Posts_likes(
            $newLikeUuid,
            $user,
            $post
        );


        $this->likesPostRepository->save($like);



        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}