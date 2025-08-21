<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Ui\Cli;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Application\Command\GenerateThumbnailCommand;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;

#[AsCommand(
    name: 'smartiveapp:generate_thumbnails',
    description: 'Generate thumbnails for a specific application',
)]
class GenerateThumbnailsCliCommand extends Command
{
    private array $storageCases;

    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
        $this->storageCases = array_map(fn ($case) => $case->value, StorageTypeEnum::cases());
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $storageOptions = implode(', ', $this->storageCases);
        $this
            ->setDescription('Generates a thumbnail for an image')
            ->addArgument('source', InputArgument::OPTIONAL, 'Source image path (start from ./)')
            ->addOption(
                'storage',
                's',
                InputOption::VALUE_OPTIONAL,
                "Storage type ({$storageOptions})",
            );
    }

    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $source = $input->getArgument('source');
        if ($this->validateSource($source)) {
            do {
                $question = new Question('Please provide source image path (start from ./): ');
                $source = $helper->ask($input, $output, $question);

                if ($this->validateSource($source)) {
                    $output->writeln('<error>Source image path cannot be empty or does not exist. Try again.</error>');
                    $source = null;
                }
            } while (!$source);

            $input->setArgument('source', $source);
        }

        $storage = $input->getOption('storage');
        if (!in_array($storage, $this->storageCases, true)) {
            $output->writeln("<error>Invalid storage type: {$storage}</error>");

            $question = new ChoiceQuestion(
                'Please select storage type:',
                $this->storageCases,
                StorageTypeEnum::LOCAL->value
            );

            $storage = $helper->ask($input, $output, $question);
            $input->setOption('storage', $storage);
        }
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $output->writeln('<bg=green;options=bold>Generating thumbnails...</>');

        $source = $input->getArgument('source');
        $storage = $input->getOption('storage');

        try {
            $command = new GenerateThumbnailCommand($source, StorageTypeEnum::from($storage));
            $this->commandBus->dispatch($command);
            $io->success('Thumbnail generation dispatched');
            return Command::SUCCESS;
        } catch (\Exception|ExceptionInterface $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function validateSource(string $source): bool
    {
        return empty($source) || !is_file($source) || !file_exists($source);
    }
}
