<?php


namespace Landesadel\easyBlog\http\Actions\Auth;


use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\Exceptions\AuthTokenNotFoundException;
use Landesadel\easyBlog\Exceptions\AuthTokensRepositoryException;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\AuthToken\AuthTokensRepositoryInterface;

class LogOut
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    /**
     * @throws \Landesadel\easyBlog\Exceptions\HttpException
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $request->header('Authorization');

        $this->authTokensRepository->logout($token);

        return new SuccessfulResponse([
            'token' => $token
        ]);
    }


}