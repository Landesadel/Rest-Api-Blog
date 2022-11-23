<?php


namespace Landesadel\easyBlog\UnitTests\Repositories;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
use Landesadel\easyBlog\Repositories\User\SqliteUsersRepository;
use Landesadel\easyBlog\UnitTests\DummyLogger;
use Landesadel\easyBlog\Uuid;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionMock->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);


        $repository = new SqliteUsersRepository($connectionMock, new DummyLogger());
        $this->expectException(UserNotFoundException::class);
        $this->expectDeprecationMessage('Cannot find user: ivan');
        $repository->getUsername('ivan');
    }


    public function testItSavesUserToDatabase(): void
    {

        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
                ':username' => 'ivan123',
                ':password' => '123',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $repository->save(
            new User(
                new Uuid('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123',
                '123'
            )
        );
    }
}