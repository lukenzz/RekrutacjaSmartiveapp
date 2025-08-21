<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Enum;

enum StorageTypeEnum: string
{
    case LOCAL = 'local';
    case FTP = 'ftp';
}
