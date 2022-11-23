<?php


namespace Landesadel\easyBlog\http\Auth;


use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }


        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;

    }
}