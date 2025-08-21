<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Service;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model\ThumbnailModel;

interface ThumbnailGeneratorInterface
{
    public function generate(ThumbnailModel $command): void;
}
