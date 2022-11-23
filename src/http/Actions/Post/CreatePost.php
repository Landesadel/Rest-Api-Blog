<?php


namespace Landesadel\easyBlog\http\Actions\Post;



use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{


    /**
     * CreatePost constructor.
     */
    public function __construct(
        private PostRepositoryInterface $PostRepository,
        private LoggerInterface $logger,
        private TokenAuthenticationInterface $authentication,
    ){
    }

    public function handle(Request $request): Response
    {

            try {
                $user = $this->authentication->user($request);
            } catch (AuthException $e) {
                return new ErrorResponse($e->getMessage());
            }


            $newPostUuid = Uuid::random();


            try{
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text')
            );

            $this->PostRepository->save($post);

            $this->logger->info("Post created: $newPostUuid");

        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}