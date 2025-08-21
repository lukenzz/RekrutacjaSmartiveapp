<?php

declare(strict_types=1);

namespace Tests\Media\Storage\Infrastructure\Adapter;

use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Exception\StorageException;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Adapter\FtpStorage;

class FtpStorageTest extends TestCase
{
    private string $tmpFile;

    private StorageInterface $storage;

    protected function setUp(): void
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'ftp_test_');
        file_put_contents($this->tmpFile, 'test');
        $this->storage = new FtpStorage('host', 'user', 'pass');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->storage->supports(StorageTypeEnum::FTP));
    }

    public function testStoreThrowsExceptionWhenSourceFileNotFound(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Unable to open source file');

        $this->storage->store('/not-exist.txt', 'test.txt');
    }

    public function testStoreWritesFileUsingMockedFilesystem(): void
    {
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock
            ->expects($this->once())
            ->method('writeStream')
            ->with(
                'test.txt',
                $this->callback(function ($arg) {
                    $this->assertIsResource($arg);
                    return true;
                })
            );

        $reflection = new \ReflectionProperty(FtpStorage::class, 'filesystem');
        $reflection->setValue($this->storage, $filesystemMock);

        $this->storage->store($this->tmpFile, 'test.txt');
    }

    public function testStoreThrowsStorageExceptionWhenFilesystemFails(): void
    {
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock
            ->method('writeStream')
            ->willThrowException(new StorageException('Unable to write file'));

        $reflection = new \ReflectionProperty(FtpStorage::class, 'filesystem');
        $reflection->setValue($this->storage, $filesystemMock);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Unable to write file');

        $this->storage->store($this->tmpFile, 'test.txt');
    }
}
