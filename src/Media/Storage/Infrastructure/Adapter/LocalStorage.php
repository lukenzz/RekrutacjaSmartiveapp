<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Adapter;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Exception\StorageException;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;

class LocalStorage implements StorageInterface
{
    private Filesystem $filesystem;

    public function __construct(private string $root = '/tmp')
    {
        $adapter = new LocalFilesystemAdapter($this->root);
        $this->filesystem = new Filesystem($adapter);
    }

    public function store(string $sourcePath, string $destinationPath): void
    {
        try {
            $dir = dirname($destinationPath);

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }
            }

            $stream = @fopen($sourcePath, 'r');
            if (false === $stream) {
                throw new StorageException(StorageTypeEnum::LOCAL->value.': Unable to open source file: '.$sourcePath);
            }
            $this->filesystem->writeStream($destinationPath, $stream);
        } catch (\Throwable $e) {
            throw new StorageException(StorageTypeEnum::LOCAL->value.': Unable to write file: '.$e->getMessage());
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function supports(StorageTypeEnum $type): bool
    {
        return StorageTypeEnum::LOCAL === $type;
    }
}
