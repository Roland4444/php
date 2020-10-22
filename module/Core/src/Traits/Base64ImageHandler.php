<?php


namespace Core\Traits;

trait Base64ImageHandler
{
    private function saveImage(string $fileName, string $imageDir, $base64Image)
    {
        if (! file_exists($imageDir) && ! empty($base64Image)) {
            mkdir($imageDir, 0777, true);
        }

        $decodedFile = base64_decode($base64Image);
        $ifp = fopen($fileName, 'wb');
        fwrite($ifp, $decodedFile);
        fclose($ifp);
    }
}
