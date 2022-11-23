<?php


namespace Landesadel\easyBlog\http\Actions\Comment;


use Landesadel\easyBlog\Blog\Comment\Comment;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\Comment\CommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Uuid;

class CreateComment implements ActionInterface
{


    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private PostRepositoryInterface $PostRepository,
        private CommentRepositoryInterface $commentRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{

            $postUuid = new Uuid($request->jsonBodyField('post_uuid'));
            $post = $this->PostRepository->get($postUuid);

            $user = $this->authentication->user($request);

            $newCommentUuid = Uuid::random();

            $comment = new Comment(
                $newCommentUuid,
                $post,
                $user,
                $request->jsonBodyField('text')
            );

            $this->commentRepository->save($comment);

        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}