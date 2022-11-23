<?php


namespace Landesadel\easyBlog\Author;



use Landesadel\easyBlog\Uuid;

class User
{

    public function __construct(
        private  Uuid  $uuid,
        private Name   $name,
        private string $username,
        private string $hashPassword
    )
    {
    }

    /**
     * @return string
     */
    public function getHashPassword(): string
    {
        return $this->hashPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }


    public function checkPassword(string $password): bool
    {
        return $this->hashPassword
            === self::hash($password, $this->uuid);

    }


    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $firstName = $this->name()->getFirstName();
        $lastName = $this->name()->getLastName();
        return $firstName . " " . $lastName . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function name(): Name
    {
        return $this->name;
    }


    public static function createFrom(
        Name $name,
        string $username,
        string $password,
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid)
        );

    }
}