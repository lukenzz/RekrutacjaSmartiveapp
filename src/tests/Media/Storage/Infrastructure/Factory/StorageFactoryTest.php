<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Tests\Media\Storage\Infrastructure\Factory;

use PHPUnit\Framework\TestCase;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum\StorageTypeEnum;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Model\StorageInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Infrastructure\Factory\StorageFactory;

class StorageFactoryTest extends TestCase
{
    public function testCreateReturnsCorrectStorage(): void
    {
        $localMock = $this->createMock(StorageInterface::class);
        $localMock->method('supports')
            ->willReturnCallback(fn ($type) => StorageTypeEnum::LOCAL === $type);

        $ftpMock = $this->createMock(StorageInterface::class);
        $ftpMock->method('supports')
            ->willReturnCallback(fn ($type) => StorageTypeEnum::FTP === $type);

        $factory = new StorageFactory([$localMock, $ftpMock]);

        $this->assertSame($localMock, $factory->create(StorageTypeEnum::LOCAL));
        $this->assertSame($ftpMock, $factory->create(StorageTypeEnum::FTP));
    }
}
