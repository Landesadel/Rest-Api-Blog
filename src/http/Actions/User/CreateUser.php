<?php


namespace Landesadel\easyBlog\http\Actions\User;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\http\ErrorResponse;

class CreateUser implements ActionInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try{
            $newUserUuid = Uuid::random();

            $user = new User(
                $newUserUuid,
                new Name(
                    $request->JsonBodyField('first_name'),
                    $request->JsonBodyField('last_name')
                ),
                $request->JsonBodyField('username'),
                $request->jsonBodyField('password')

            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}