<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Exception;

class StorageException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
