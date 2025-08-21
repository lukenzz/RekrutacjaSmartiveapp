<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Application\Command;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;

readonly class GenerateThumbnailCommand
{
    public function __construct(
        public string $sourcePath,
        public StorageTypeEnum $storage,
    ) {
    }
}
