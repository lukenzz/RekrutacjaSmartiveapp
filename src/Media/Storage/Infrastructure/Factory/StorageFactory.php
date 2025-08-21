<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Factory;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Service\StorageFactoryInterface;

readonly class StorageFactory implements StorageFactoryInterface
{
    /** @var iterable<StorageInterface> */
    private iterable $storages;

    /**
     * @param iterable<StorageInterface> $storages
     */
    public function __construct(iterable $storages)
    {
        $this->storages = $storages;
    }

    final public function create(StorageTypeEnum $type): StorageInterface
    {
        foreach ($this->storages as $storage) {
            if ($storage->supports($type)) {
                return $storage;
            }
        }

        throw new \InvalidArgumentException("Unsupported storage type: {$type->value}");
    }
}
