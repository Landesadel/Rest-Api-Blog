<?php


use Landesadel\easyBlog\Commands\Arguments;
use Landesadel\easyBlog\Commands\CreateUserCommand;
use Landesadel\easyBlog\Commands\FakerData\PopulateDB;
use Landesadel\easyBlog\Commands\Post\DeletePost;
use Landesadel\easyBlog\Commands\Users\CreateUser;
use Landesadel\easyBlog\Commands\Users\UpdateUser;
use Landesadel\easyBlog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;


$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);
$logger = $container->get(LoggerInterface::class);

$application = new Application();
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();


try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
//    echo "{$e->getMessage()}\n";
    $logger->error($e->getMessage(), ['exception' => $e]);
}