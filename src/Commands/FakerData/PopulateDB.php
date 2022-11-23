<?php


namespace Landesadel\easyBlog\Commands\FakerData;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Blog\Comment\Comment;
use Landesadel\easyBlog\Blog\Post;
use Landesadel\easyBlog\Repositories\Comment\CommentRepositoryInterface;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_REQUIRED,
                'Users number',
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_REQUIRED,
                'Posts number',
            )
            ->addOption(
                'comments-number',
                'c',
                InputOption::VALUE_REQUIRED,
                'Comment number',
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output,): int
    {
        $usersNumber = $input->getOption('users-number');
        $postsNumber = $input->getOption('posts-number');
        $commentNumber = $input->getOption('comments-number');

        if (!is_numeric($usersNumber) || !is_numeric($postsNumber) || !is_numeric($commentNumber)) {
            $output->writeln('Not a number!');
            return Command::SUCCESS;
        }

        $users = [];
        $posts = [];
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < $postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        foreach ($posts as $post) {
            for ($i = 0; $i < $commentNumber; $i++) {
                $comment = $this->createFakeComment($post);
                $output->writeln('Comment created: ' . $comment->getId());

            }
        }

        return Command::SUCCESS;

    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            ),
            $this->faker->userName,
            $this->faker->password,
        );

        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $user): Post
    {
        $post = new Post(
            Uuid::random(),
            $user,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(Post $post): Comment
    {
        $comment = new Comment(
            Uuid::random(),
            $post,
            $post->getUserId(),
            $this->faker->realText
        );
        $this->commentRepository->save($comment);
        return $comment;
    }


}