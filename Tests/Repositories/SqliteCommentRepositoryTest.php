<?php


namespace Landesadel\easyBlog\UnitTests\Repositories;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Comment\Comment;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Exceptions\CommentNotFoundException;
use Landesadel\easyBlog\Repositories\Comment\SqliteCommentRepository;
use Landesadel\easyBlog\UnitTests\DummyLogger;
use Landesadel\easyBlog\Uuid;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Landesadel\easyBlog\Exceptions\InvalidArgumentException;

class SqliteCommentRepositoryTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionMock->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);


        $repository = new SqliteCommentRepository($connectionMock, new DummyLogger());
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 122e4567-e89b-12d3-a456-426614174000');
        $repository->get(new Uuid('122e4567-e89b-12d3-a456-426614174000'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174111',
                ':post_uuid' => '123e4567-e89b-12d3-a456-426614174222',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174333',
                ':text' => 'Some text',
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentRepository($connectionStub, new DummyLogger());

        $user = new User(
            new Uuid('123e4567-e89b-12d3-a456-426614174333'),
            new Name('first', 'last'),
            'userName',
            '123'
        );

        $post = new Post(
            new Uuid('123e4567-e89b-12d3-a456-426614174222'),
            $user,
            'Some title',
            'Some text'
        );

        $repository->save(
            new Comment(
                new Uuid('123e4567-e89b-12d3-a456-426614174111'),
                $post,
                $user,
                'Some text',
            )
        );
    }
}