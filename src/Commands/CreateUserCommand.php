<?php

namespace Landesadel\easyBlog\Commands;

use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\ArgumentsException;
use Landesadel\easyBlog\Exceptions\CommandException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;


class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,

    ){
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }

        $user = User::createFrom(
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'),
            ),
            $username,
            $arguments->get('password'),
        );
        $this->usersRepository->save($user);

        $this->logger->info('User created: ' . $user->getId());

    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}