<?php

declare(strict_types=1);

namespace SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Infrastructure\Service;

use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Model\ThumbnailModel;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Image\Domain\Service\ThumbnailGeneratorInterface;
use SzymonLukaszczykRekrutacjaSmartiveapp\Media\Storage\Domain\Service\StorageFactoryInterface;

final class ThumbnailGenerator implements ThumbnailGeneratorInterface
{
    public function __construct(
        private StorageFactoryInterface $storageFactory,
    ) {
    }

    public function generate(ThumbnailModel $model): void
    {
        if (!file_exists($model->sourcePath)) {
            throw new \RuntimeException("Source file does not exist: {$model->sourcePath}");
        }

        $info = getimagesize($model->sourcePath);
        if (false === $info) {
            throw new \RuntimeException("Unable to get image info: {$model->sourcePath}");
        }

        [$width, $height, $type] = $info;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($model->sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($model->sourcePath);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($model->sourcePath);
                break;
            default:
                throw new \RuntimeException("Unsupported image type: {$model->sourcePath}");
        }

        if ($width > $height) {
            $newWidth = $model->maxSize;
            $newHeight = (int) ($height * ($model->maxSize / $width));
        } else {
            $newHeight = $model->maxSize;
            $newWidth = (int) ($width * ($model->maxSize / $height));
        }

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        if (IMAGETYPE_PNG === $type || IMAGETYPE_GIF === $type) {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $tmpFile = tempnam(sys_get_temp_dir(), 'thumb_').'.'.$model->format;

        switch (strtolower($model->format)) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumb, $tmpFile);
                break;
            case 'png':
                imagepng($thumb, $tmpFile);
                break;
            case 'gif':
                imagegif($thumb, $tmpFile);
                break;
            default:
                imagedestroy($thumb);
                imagedestroy($src);
                throw new \RuntimeException("Unsupported output format: {$model->format}");
        }

        imagedestroy($thumb);
        imagedestroy($src);

        $storage = $this->storageFactory->create($model->storage);
        $storage->store($tmpFile, basename($model->sourcePath).'.'.$model->format);

        unlink($tmpFile);
    }
}
