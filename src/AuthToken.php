<?php


namespace Landesadel\easyBlog;




use DateTimeImmutable;

class AuthToken
{

    /**
     * AuthToken constructor.
     */
    public function __construct(
        private string $token,
        private Uuid $userUuid,
        private DateTimeImmutable $expiresOn
    )
    {
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return Uuid
     */
    public function getUserUuid(): Uuid
    {
        return $this->userUuid;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }


}