<?php


namespace Landesadel\easyBlog\Commands\Users;


use Landesadel\easyBlog\Author\Name;
use Landesadel\easyBlog\Author\User;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use Landesadel\easyBlog\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                'first-name',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );
    }

    /**
     * @throws \Landesadel\easyBlog\Exceptions\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output,): int
    {
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $uuid = new Uuid($input->getArgument('uuid'));
        $user = $this->usersRepository->get($uuid);

        $updatedName = new Name(
            firstName: empty($firstName)
            ? $user->name()->getFirstName() : $firstName,
            lastName: empty($lastName)
            ? $user->name()->getLastName() : $lastName,
        );

        $updatedUser = new User(
            uuid: $uuid,
            name: $updatedName,
            username: $user->getUsername(),
            hashPassword: $user->getHashPassword(),
        );

        $this->usersRepository->save($updatedUser);
        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;



    }
}