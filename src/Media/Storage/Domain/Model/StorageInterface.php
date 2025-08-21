<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;

interface StorageInterface
{
    public function store(string $sourcePath, string $destinationPath): void;
    public function supports(StorageTypeEnum $type): bool;
}
