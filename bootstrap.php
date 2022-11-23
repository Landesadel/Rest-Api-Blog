<?php


use Faker\Provider\ru_RU\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use Landesadel\easyBlog\Container\DIContainer;
use Landesadel\easyBlog\http\Actions\Auth\BearerTokenAuthentication;
use Landesadel\easyBlog\http\Auth\AuthenticationInterface;
use Landesadel\easyBlog\http\Auth\IdentificationInterface;
use Landesadel\easyBlog\http\Auth\JsonBodyUsernameIdentification;
use Landesadel\easyBlog\http\Auth\JsonBodyUuidIdentification;
use Landesadel\easyBlog\http\Auth\PasswordAuthentication;
use Landesadel\easyBlog\http\Auth\PasswordAuthenticationInterface;
use Landesadel\easyBlog\http\Auth\TokenAuthenticationInterface;
use Landesadel\easyBlog\Repositories\AuthToken\AuthTokensRepositoryInterface;
use Landesadel\easyBlog\Repositories\AuthToken\SqliteAuthTokensRepository;
use Landesadel\easyBlog\Repositories\Comment\CommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\Comment\Likes\LikesCommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\Comment\Likes\SqliteLikesCommentRepository;
use Landesadel\easyBlog\Repositories\Comment\SqliteCommentRepository;
use Landesadel\easyBlog\Repositories\Post\Likes\LikesPostRepositoryInterface;
use Landesadel\easyBlog\Repositories\Post\Likes\SqliteLikesPostRepository;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Repositories\Post\SqlitePostsRepository;
use Landesadel\easyBlog\Repositories\User\SqliteUsersRepository;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;
use Faker\Generator;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();


$container = new DIContainer();

$faker = new Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));



$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);

$logger = (new Logger('blog'));

if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ($_SERVER['LOG_TO_CONSOLE'] === 'no') {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

$container->bind(
    \Faker\Generator::class,
    $faker
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);


$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);


$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PostRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentRepositoryInterface::class,
    SqliteCommentRepository::class
);

$container->bind(
    LikesCommentRepositoryInterface::class,
    SqliteLikesCommentRepository::class
);

$container->bind(
    LikesPostRepositoryInterface::class,
    SqliteLikesPostRepository::class
);

return $container;
