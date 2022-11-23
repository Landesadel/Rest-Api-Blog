<?php

require_once __DIR__ . '/vendor/autoload.php';


use Landesadel\easyBlog\Exceptions\AppException;
use Landesadel\easyBlog\http\Actions\Auth\Login;
use Landesadel\easyBlog\http\Actions\Auth\LogOut;
use Landesadel\easyBlog\http\Actions\Comment\CreateComment;
use Landesadel\easyBlog\http\Actions\Comment\DeleteComment;
use Landesadel\easyBlog\http\Actions\Comment\Likes\CreateCommentLikes;
use Landesadel\easyBlog\http\Actions\Comment\Likes\DeleteCommentLikes;
use Landesadel\easyBlog\http\Actions\Post\CreatePost;
use Landesadel\easyBlog\http\Actions\Post\DeletePost;
use Landesadel\easyBlog\http\Actions\Post\FindPostByUuid;
use Landesadel\easyBlog\http\Actions\Post\Likes\CreatePostLikes;
use Landesadel\easyBlog\http\Actions\Post\Likes\DeletePostLikes;
use Landesadel\easyBlog\http\Actions\User\CreateUser;
use Landesadel\easyBlog\http\Actions\User\findByUsername;
use Landesadel\easyBlog\http\ErrorResponse;
use Landesadel\easyBlog\http\Request;
use Psr\Log\LoggerInterface;
use Landesadel\easyBlog\Exceptions\HttpException;

$container = require __DIR__ . '/bootstrap.php';


$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();

    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}





$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindPostByUuid::class,
    ],
    'POST' => [
        '/login' => Login::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/postslike/create' => CreatePostLikes::class,
        '/commentslike/create' => CreateCommentLikes::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
        '/postslike' => DeletePostLikes::class,
        '/commentslike' => DeleteCommentLikes::class,
    ]
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {

    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();


