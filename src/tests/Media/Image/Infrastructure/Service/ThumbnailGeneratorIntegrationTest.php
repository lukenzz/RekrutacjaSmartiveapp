<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Tests\Media\Image\Infrastructure\Service;

use PHPUnit\Framework\TestCase;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model\ThumbnailModel;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Infrastructure\Service\ThumbnailGenerator;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Service\StorageFactoryInterface;

final class ThumbnailGeneratorIntegrationTest extends TestCase
{
    public function testGenerateThumbnailAndStore(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'thumb').'.jpg';
        imagejpeg(imagecreatetruecolor(100, 100), $tmpFile);

        $storageMock = $this->createMock(StorageInterface::class);
        $storageMock->expects($this->once())
            ->method('store')
            ->with(
                $this->callback(fn ($arg) => is_string($arg)),
                $this->stringContains('.jpg')
            );

        $factoryMock = $this->createMock(StorageFactoryInterface::class);
        $factoryMock->method('create')->willReturn($storageMock);

        $generator = new ThumbnailGenerator($factoryMock);

        $model = new ThumbnailModel(
            sourcePath: $tmpFile,
            storage: StorageTypeEnum::LOCAL,
            maxSize: 50,
            format: 'jpg'
        );

        $generator->generate($model);

        unlink($tmpFile);
    }
}
