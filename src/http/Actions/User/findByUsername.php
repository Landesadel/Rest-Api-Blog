<?php


namespace Landesadel\easyBlog\http\Actions\User;


use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\SuccessfulResponse;

class findByUsername implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {

            $username = $request->query('username');
        } catch (HttpException $e) {

            return new ErrorResponse($e->getMessage());
        }
        try {

            $user = $this->usersRepository->getUsername($username);
        } catch (UserNotFoundException $e) {

            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->name()->getFirstName() . ' ' . $user->name()->getLastName(),
        ]);
    }

}