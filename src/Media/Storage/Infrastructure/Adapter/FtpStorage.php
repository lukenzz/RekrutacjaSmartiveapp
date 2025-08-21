<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Adapter;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Exception\StorageException;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;

class FtpStorage implements StorageInterface
{
    private Filesystem $filesystem;

    public function __construct(
        string $host,
        string $username,
        string $password,
        string $root = '/',
        int $port = 21,
        bool $ssl = false,
        bool $passive = true,
        int $timeout = 90,
    ) {
        $options = FtpConnectionOptions::fromArray([
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'root' => $root,
            'port' => $port,
            'ssl' => $ssl,
            'passive' => $passive,
            'timeout' => $timeout,
        ]);

        $adapter = new FtpAdapter($options);
        $this->filesystem = new Filesystem($adapter);
    }

    public function store(string $sourcePath, string $destinationPath): void
    {
        try {
            $stream = @fopen($sourcePath, 'r');
            if (false === $stream) {
                throw new StorageException(StorageTypeEnum::FTP->value.': Unable to open source file: '.$sourcePath);
            }

            $this->filesystem->writeStream($destinationPath, $stream);
        } catch (FilesystemException $e) {
            throw new StorageException(StorageTypeEnum::FTP->value.': Unable to write file: '.$e->getMessage());
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function supports(StorageTypeEnum $type): bool
    {
        return StorageTypeEnum::FTP === $type;
    }
}
