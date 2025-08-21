<?php

declare(strict_types=1);

namespace Tests\Media\Storage\Infrastructure\Adapter;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Exception\StorageException;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Adapter\LocalStorage;

class LocalStorageTest extends KernelTestCase
{
    private string $tmpDir;

    private StorageInterface $storage;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->tmpDir = self::getContainer()->getParameter('kernel.project_dir').'/var/test_storage';
        mkdir($this->tmpDir, 0777, true);

        $this->storage = new LocalStorage($this->tmpDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tmpDir)) {
            array_map('unlink', glob($this->tmpDir.'/*'));
            rmdir($this->tmpDir);
        }

        parent::tearDown();
    }

    public function testSaveAndReadFile(): void
    {
        $filename = 'test.txt';
        $content = 'test';

        $source = $this->tmpDir.'/source.txt';
        file_put_contents($source, $content);

        $this->storage->store($source, $filename);
        $filePath = $this->tmpDir.'/'.$filename;

        self::assertFileExists($filePath);
        self::assertSame($content, file_get_contents($filePath));
    }

    public function testStoreThrowsExceptionWhenSourceFileNotFound(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Unable to open source file:');

        $this->storage->store('/not-exist.txt', $this->tmpDir.'/source.txt');
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->storage->supports(StorageTypeEnum::LOCAL));
    }
}
