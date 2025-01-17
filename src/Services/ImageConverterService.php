<?php

namespace App\Services;

use Imagick;

class ImageConverter
{
    public function convertHeicToJpeg(string $inputPath, string $outputPath): bool
    {
        try {
            $imagick = new Imagick($inputPath);
            $imagick->setImageFormat('jpeg');
            $imagick->writeImage($outputPath);
            $imagick->clear();
            $imagick->destroy();
            return true;
        } catch (\Exception $e) {
// Handle errors (log, rethrow, etc.)
            return false;
        }
    }
}
