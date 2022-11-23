<?php


namespace Landesadel\easyBlog\Repositories\User;

use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Uuid;
use Psr\Log\LoggerInterface;


class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private  \PDO $connection,
        private LoggerInterface $logger,
    ){
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name, password) 
                   VALUES (:uuid, :username, :first_name, :last_name, :password)
                   ON CONFLICT (uuid) DO UPDATE SET first_name = :first_name, last_name = :last_name'
        );
        $statement->execute([
            ':uuid' => (string)$user->getId(),
            ':username' => $user->getUsername(),
            ':first_name' => $user->name()->getFirstName(),
            ':last_name' => $user->name()->getLastName(),
            ':password' => $user->getHashPassword(),
        ]);
        $this->logger->info("User created: {$user->getId()}");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(Uuid $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot get user: $uuid");
            throw new UserNotFoundException(
                "Cannot get user: $uuid"
            );
        }

        return new User(
            new Uuid($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            $result['password']
        );
    }


    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);

    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(\PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $this->logger->warning("Cannot find user: $username");
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }
        return new User(
            new Uuid($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            $result['password']
        );
    }

}