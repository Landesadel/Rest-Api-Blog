<?php


namespace Landesadel\easyBlog\UnitTests\Actions;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\http\Actions\User\findByUsername;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Landesadel\easyBlog\http\SuccessfulResponse;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws /JsonException
     */

    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {

        $request = new Request([], [], '');


        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \JsonException
     */

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {

        $request = new Request(['username' => 'ivan'], [], '');
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \JsonException
     */

    public function testItReturnsSuccessfulResponse(): void

    {
        $request = new Request(['username' => 'ivan'], [], '');

        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Nikitin'),
                'ivan'
            ),
        ]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {

        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getUsername()) {
                        return $user;

                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}

