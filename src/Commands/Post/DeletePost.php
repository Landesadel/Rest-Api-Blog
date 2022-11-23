<?php


namespace Landesadel\easyBlog\Commands\Post;


use Landesadel\easyBlog\Exceptions\PostNotFoundException;
use Landesadel\easyBlog\Uuid;
use Symfony\Component\Console\Command\Command;
use Landesadel\easyBlog\Repositories\Post\PostRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,

    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete'
            )
            ->addOption(

                'check-existence',
                'c',
                InputOption::VALUE_NONE,
                'Check if post actually exists',
            );

    }

    /**
     * @throws \Landesadel\easyBlog\Exceptions\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output,): int
    {
        $question = new ConfirmationQuestion(
            'Delete post [Y/n]? ',
            false
        );
        if (!$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {

            return Command::SUCCESS;
        }

        $uuid = new Uuid($input->getArgument('uuid'));

        if ($input->getOption('check-existence')) {
            try {
                $this->postsRepository->get($uuid);
            } catch (PostNotFoundException $e) {
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }


        $this->postsRepository->delete($uuid);
        $output->writeln("Post $uuid deleted");
        return Command::SUCCESS;
    }

    }