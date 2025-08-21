<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Application\Handler;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Application\Command\GenerateThumbnailCommand;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model\ThumbnailModel;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Service\ThumbnailGeneratorInterface;

final readonly class GenerateThumbnailHandler
{
    public function __construct(
        private ThumbnailGeneratorInterface $generator,
        private int $maxSize,
        private string $format,
    ) {
    }

    public function __invoke(GenerateThumbnailCommand $message): void
    {
        $command = new ThumbnailModel(
            $message->sourcePath,
            $message->storage,
            $this->maxSize,
            $this->format,
        );

        $this->generator->generate($command);
    }
}
