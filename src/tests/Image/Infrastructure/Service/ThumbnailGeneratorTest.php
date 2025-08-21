<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Tests\Media\Image\Infrastructure\Service;

use PHPUnit\Framework\TestCase;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model\ThumbnailModel;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Infrastructure\Service\ThumbnailGenerator;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Service\StorageFactoryInterface;

final class ThumbnailGeneratorTest extends TestCase
{
    private $storageFactory;
    private $storage;
    private $generator;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);
        $this->storageFactory = $this->createMock(StorageFactoryInterface::class);
        $this->storageFactory
            ->method('create')
            ->willReturn($this->storage);

        $this->generator = new ThumbnailGenerator($this->storageFactory);
    }

    public function testGenerateJpgThumbnail(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test').'.jpg';
        imagejpeg(imagecreatetruecolor(100, 100), $tmpFile);

        $model = new ThumbnailModel(
            sourcePath: $tmpFile,
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'jpg',
        );

        $this->storage
            ->expects($this->once())
            ->method('store')
            ->with(
                $this->callback(fn ($arg) => is_string($arg)),
                $this->stringContains('.jpg')
            );

        $this->generator->generate($model);

        unlink($tmpFile);
    }

    public function testGeneratePngThumbnail(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test').'.png';
        imagepng(imagecreatetruecolor(100, 100), $tmpFile);

        $model = new ThumbnailModel(
            sourcePath: $tmpFile,
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'png',
        );

        $this->storage
            ->expects($this->once())
            ->method('store')
            ->with(
                $this->callback(fn ($arg) => is_string($arg)),
                $this->stringContains('.png')
            );

        $this->generator->generate($model);

        unlink($tmpFile);
    }

    public function testGenerateGifThumbnail(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test').'.gif';
        imagegif(imagecreatetruecolor(100, 100), $tmpFile);

        $model = new ThumbnailModel(
            sourcePath: $tmpFile,
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'gif',
        );

        $this->storage
            ->expects($this->once())
            ->method('store')
            ->with(
                $this->callback(fn ($arg) => is_string($arg)),
                $this->stringContains('.gif')
            );

        $this->generator->generate($model);

        unlink($tmpFile);
    }

    public function testThrowsExceptionWhenSourceFileMissing(): void
    {
        $model = new ThumbnailModel(
            sourcePath: 'notexist.jpg',
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'jpg',
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Source file does not exist');

        $this->generator->generate($model);
    }

    public function testThrowsExceptionForUnsupportedOutputFormat(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test').'.jpg';
        imagejpeg(imagecreatetruecolor(100, 100), $tmpFile);

        $model = new ThumbnailModel(
            sourcePath: $tmpFile,
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'bmp',
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported output format');

        $this->generator->generate($model);

        unlink($tmpFile);
    }
}
