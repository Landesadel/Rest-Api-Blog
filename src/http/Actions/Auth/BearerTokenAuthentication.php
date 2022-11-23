<?php


namespace Landesadel\easyBlog\http\Actions\Auth;


use DateTimeImmutable;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\Exceptions\AuthTokenNotFoundException;
use Landesadel\easyBlog\Exceptions\HttpException;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\Repositories\AuthToken\AuthTokensRepositoryInterface;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->getExpiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        $userUuid = $authToken->getUserUuid();

        return $this->usersRepository->get($userUuid);


    }

}