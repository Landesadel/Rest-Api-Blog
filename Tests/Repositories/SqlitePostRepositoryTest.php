<?php


namespace Landesadel\easyBlog\UnitTests\Repositories;



use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Exceptions\PostNotFoundException;
use Landesadel\easyBlog\Repositories\Post\SqlitePostsRepository;
use Landesadel\easyBlog\UnitTests\DummyLogger;
use Landesadel\easyBlog\Uuid;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;
use Landesadel\easyBlog\Exceptions\UserNotFoundException;
class SqlitePostRepositoryTest extends TestCase
{

    /**
     * @throws InvalidArgumentException|UserNotFoundException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionMock->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);


        $repository = new SqlitePostsRepository($connectionMock, new DummyLogger());
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 122e4567-e89b-12d3-a456-426614174000');

        $repository->get(new Uuid('122e4567-e89b-12d3-a456-426614174000'));
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-15d3-a456-429014174340',
                ':title' => 'Some title',
                ':text' => 'Some text',
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new Uuid('123e4567-e89b-15d3-a456-429014174340'),
            new Name('first', 'last'),
            'userName',
            '123'
        );

        $repository->save(
            new Post(
                new Uuid('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Some title',
                'Some text'
            )
        );
    }

    public function testItGetPostByUuid()
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'author_uuid' => '123e4567-e89b-15d3-a456-429014174340',
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username',
            'password' => '123',
            'title' => 'Some title',
            'text' => 'Some text'
        ]);

        $postRepository = new SqlitePostsRepository($connectionStub, new DummyLogger());
        $post = $postRepository->get(new Uuid('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string)$post->getId());
    }

}