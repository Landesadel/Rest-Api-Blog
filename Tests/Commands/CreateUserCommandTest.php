<?php

namespace Landesadel\easyBlog\UnitTests\Commands;


use Symfony\Component\Console\Exception\RuntimeException;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Commands\Arguments;
use Landesadel\easyBlog\Commands\CreateUserCommand;
use Landesadel\easyBlog\Commands\Users\CreateUser;
use Landesadel\easyBlog\Exceptions\ArgumentsException;
use Landesadel\easyBlog\Exceptions\CommandException;

use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Repositories\User\DummyUsersRepository;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\UnitTests\DummyLogger;
use Landesadel\easyBlog\Uuid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{
    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {

        $command = new CreateUserCommand(
            new DummyUsersRepository(),
            new DummyLogger()
        );


        $this->expectException(CommandException::class);

        $this->expectExceptionMessage("User already exists: Ivan");

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123',
            ]));
    }

    /**
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password"'
        );

        $command->run(
            new ArrayInput([
            'username' => 'Ivan',
        ]),
            new NullOutput()
        );

    }


    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => '123',
                ]),
            new NullOutput()
        );
    }


    public function testItRequiresLastName(): void
    {

        $command = new CreateUser($this->makeUsersRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );
        $command->run(
            new ArrayInput([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'password' => '123',
        ]),
            new NullOutput()
        );
    }


    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface {

            private bool $called = false;
            public function save(User $user): void
            {

                $this->called = true;
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

        };

        $command = new CreateUserCommand($usersRepository, new DummyLogger());

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

}