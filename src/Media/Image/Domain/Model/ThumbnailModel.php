<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;

readonly class ThumbnailModel
{
    public function __construct(
        public string $sourcePath,
        public StorageTypeEnum $storage,
        public int $maxSize,
        public string $format,
    ) {
    }
}
