<?php


namespace Landesadel\easyBlog\Repositories\AuthToken;


use DateTimeInterface;
use Exception;
use Landesadel\easyBlog\AuthToken;
use Landesadel\easyBlog\Exceptions\AuthTokenNotFoundException;
use Landesadel\easyBlog\Exceptions\AuthTokensRepositoryException;
use Landesadel\easyBlog\Uuid;
use DateTimeImmutable;
use PDO;
use PDOException;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }


    /**
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                user_uuid,
                expires_on
            ) VALUES (
                :token,
                :user_uuid,
                :expires_on
            )
            ON CONFLICT (token) DO UPDATE SET expires_on = :expires_on
        SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->getToken(),
                ':user_uuid' => (string)$authToken->getUserUuid(),
                ':expires_on' => $authToken->getExpiresOn()->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

    }

    /**
     * @throws AuthTokensRepositoryException
     * @throws AuthTokenNotFoundException
     */
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        if ($result === false) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    public function logout(string $token): void
    {
        $expired = (new \DateTimeImmutable())->format(\DateTimeImmutable::ATOM);

        $statement = $this->connection->prepare(
            'UPDATE tokens
                SET expires_on = :expired
                WHERE token = :token'
        );

        $statement->execute(
            [
                ":expired" => $expired,
                ":token" => $token
            ]
        );
    }
}