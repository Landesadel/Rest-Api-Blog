<?php


namespace Landesadel\easyBlog\http\Auth;


use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\Uuid;
use Landesadel\easyBlog\Author\User;

class JsonBodyUuidIdentification implements IdentificationInterface

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
            $userUuid = new Uuid($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }
        try {

            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {

            throw new AuthException($e->getMessage());
        }
    }

}