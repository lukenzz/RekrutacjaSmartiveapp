<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Exception;

final class ThumbnailGeneratorException extends \RuntimeException
{
    public static function sourceFileNotFound(string $path): self
    {
        return new self("Source file does not exist: $path");
    }

    public static function unsupportedImageType(string $path): self
    {
        return new self("Unsupported image type: $path");
    }

    public static function unsupportedOutputFormat(string $format): self
    {
        return new self("Unsupported output format: $format");
    }

    public static function unableToGetImageInfo(string $path): self
    {
        return new self("Unable to get image info: $path");
    }
}
