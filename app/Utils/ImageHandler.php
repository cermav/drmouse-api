<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class ImageHandler
{
    public static function saveEncodedFile(string $name, string $type, string $encodedContent)
    {
        // prepare file name
        $fileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', strtolower($name));
        $fileName = mb_ereg_replace("([\.]{2,})", '', $fileName) . '.' . self::getExtensionByType($type);

        // save file to local storage
        Storage::put($fileName, base64_decode($encodedContent));

        // return new file name
        return $fileName;
    }

    private static function getExtensionByType(string $type)
    {
        if (strpos($type, 'svg') !== false) {
            return 'svg';
        } elseif (strpos($type, 'png') !== false) {
            return 'png';
        } elseif (strpos($type, 'gif') !== false) {
            return 'gif';
        }
        return 'jpg';
    }
}
