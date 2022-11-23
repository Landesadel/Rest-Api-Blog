<?php


namespace Landesadel\easyBlog\http\Actions\Auth;


use Landesadel\easyBlog\AuthToken;
use Landesadel\easyBlog\Exceptions\AuthException;
use Landesadel\easyBlog\http\Actions\ActionInterface;
use Landesadel\easyBlog\http\Auth\PasswordAuthenticationInterface;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\Response;
use Landesadel\easyBlog\Repositories\AuthToken\AuthTokensRepositoryInterface;
use Landesadel\easyBlog\http\ErrorResponse;
use DateTimeImmutable;
use Landesadel\easyBlog\http\SuccessfulResponse;

class Login implements ActionInterface
{

    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getId(),
            (new DateTimeImmutable())->modify('+1 day')
        );
        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
                'token' => $authToken->getToken(),
            ]);
    }
}