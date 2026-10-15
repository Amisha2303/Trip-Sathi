<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Store catalogue cover images on the public disk and prune replaced files.
 *
 * Uploaded files land in `storage/app/public/catalogue/…`, which the
 * `public/storage` symlink serves. We persist the *relative* path (e.g.
 * `catalogue/abc.jpg`) so `BaseModel::imageUrl()` can rebuild the URL.
 */
trait HandlesImageUpload
{
    /** @var array<int, string> subdirectories already written this request */
    protected array $uploadedImages = [];

    /** Persist an uploaded file (or an external URL) and return the stored path. */
    protected function storeImage(mixed $file, string $subdirectory = 'catalogue'): ?string
    {
        if ($file instanceof UploadedFile) {
            $name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension() ?: $file->extension();
            $safe = Str::slug(pathinfo($name, PATHINFO_FILENAME)) ?: 'image';

            $path = $file->storeAs(
                $subdirectory,
                $safe . '-' . Str::random(8) . ($ext ? '.' . strtolower($ext) : ''),
                'public'
            );

            if ($path !== false) {
                $this->uploadedImages[] = $path;

                return $path;
            }
        }

        if (is_string($file) && trim($file) !== '') {
            return trim($file);
        }

        return null;
    }

    /**
     * Swap the stored image for a model. Keeps the old path when no new file
     * was uploaded; deletes the old file when replaced; clears it when the
     * admin unticks "remove image".
     */
    protected function replaceImage(object $model, mixed $file, bool $remove, string $subdirectory = 'catalogue'): ?string
    {
        $current = $model->main_image ?? null;

        if ($remove) {
            $this->deleteImageFile($current);

            return null;
        }

        $new = $this->storeImage($file, $subdirectory);

        if ($new !== null && $new !== $current) {
            $this->deleteImageFile($current);
        }

        return $new ?? $current;
    }

    protected function deleteImageFile(?string $path): void
    {
        if ($path === null || $path === '' || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
