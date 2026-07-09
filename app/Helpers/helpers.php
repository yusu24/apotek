<?php

if (!function_exists('formatDate')) {
    /**
     * Format a date to dd/mm/yyyy format
     *
     * @param string|Carbon\Carbon|null $date
     * @return string
     */
    function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format('d/m/Y');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a datetime to dd/mm/yyyy HH:mm format
     *
     * @param string|Carbon\Carbon|null $date
     * @return string
     */
    function formatDateTime($date)
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format('d/m/Y H:i');
    }
}

if (!function_exists('format_accounting_standard')) {
    /**
     * Format a number as accounting-style currency text (used across PDF reports).
     *
     * @param float|int $number
     * @return string
     */
    function format_accounting_standard($number)
    {
        $formatted = number_format(abs($number), 2, ',', '.');
        if ($formatted == '0,00') return '0';

        return ($number < 0 ? '-' : '') . $formatted;
    }
}

if (!function_exists('format_accounting')) {
    /**
     * Backward-compatible alias for format_accounting_standard().
     *
     * @param float|int $number
     * @return string
     */
    function format_accounting($number)
    {
        return format_accounting_standard($number);
    }
}

if (!function_exists('resizeAndStoreImage')) {
    /**
     * Resize an uploaded image and store it to the specified disk/directory.
     *
     * @param \Illuminate\Http\UploadedFile|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file
     * @param string $directory
     * @param string $disk
     * @param int $maxSize Max width or height
     * @param int $quality JPEG compression quality (0-100)
     * @return string|false The stored file path relative to disk root
     */
    function resizeAndStoreImage($file, $directory, $disk = 'public', $maxSize = 1000, $quality = 75)
    {
        $realPath = $file->getRealPath();
        $mimeType = $file->getMimeType();
        
        // Hanya memproses jika bertipe gambar
        if (!str_contains($mimeType, 'image/')) {
            return $file->store($directory, $disk);
        }

        // Dapatkan informasi gambar
        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            return $file->store($directory, $disk);
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Muat gambar ke memory menggunakan library GD PHP
        $srcImage = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($realPath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($realPath);
                break;
            case IMAGETYPE_GIF:
                $srcImage = @imagecreatefromgif($realPath);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = @imagecreatefromwebp($realPath);
                break;
            default:
                $imgData = @file_get_contents($realPath);
                if ($imgData) {
                    $srcImage = @imagecreatefromstring($imgData);
                }
                break;
        }

        if (!$srcImage) {
            return $file->store($directory, $disk);
        }

        // Rotasi otomatis jika ada metadata EXIF Orientation (khusus gambar JPG dari kamera ponsel)
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($realPath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $srcImage = imagerotate($srcImage, 180, 0);
                        break;
                    case 6:
                        $srcImage = imagerotate($srcImage, -90, 0);
                        $temp = $width;
                        $width = $height;
                        $height = $temp;
                        break;
                    case 8:
                        $srcImage = imagerotate($srcImage, 90, 0);
                        $temp = $width;
                        $width = $height;
                        $height = $temp;
                        break;
                }
            }
        }

        // Hitung dimensi baru dengan mempertahankan aspek rasio
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxSize || $height > $maxSize) {
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = (int)($height * ($maxSize / $width));
            } else {
                $newHeight = $maxSize;
                $newWidth = (int)($width * ($maxSize / $height));
            }
        }

        // Buat canvas baru
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        if (!$dstImage) {
            imagedestroy($srcImage);
            return $file->store($directory, $disk);
        }

        // Salin dan resize gambar ke canvas baru
        if (!imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
            imagedestroy($srcImage);
            imagedestroy($dstImage);
            return $file->store($directory, $disk);
        }

        // Simpan output ke file temporer sebagai JPG agar ukurannya seminimal mungkin
        $tempFile = tempnam(sys_get_temp_dir(), 'resized_img_');
        $targetExtension = 'jpg';
        $success = imagejpeg($dstImage, $tempFile, $quality);

        // Bersihkan resource gambar dari memory
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if (!$success) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
            return $file->store($directory, $disk);
        }

        // Generate nama berkas unik
        $filename = md5($file->getClientOriginalName() . microtime()) . '.' . $targetExtension;
        $finalPath = $directory . '/' . $filename;

        // Simpan berkas melalui facade Storage Laravel
        $fileContents = file_get_contents($tempFile);
        \Illuminate\Support\Facades\Storage::disk($disk)->put($finalPath, $fileContents);

        // Hapus berkas temporer
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }

        return $finalPath;
    }
}
