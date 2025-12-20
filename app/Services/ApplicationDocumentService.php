<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApplicationDocumentService
{
    /**
     * Upload an application document.
     *
     * @param  string  $documentType  (degree_certificate, transcripts, cv, english_test)
     * @return string The storage path
     */
    public function upload(UploadedFile $file, string $referenceNumber, string $documentType): string
    {
        // Create directory structure: applications/{reference_number}/
        $directory = "applications/{$referenceNumber}";

        // Generate filename: {document_type}.{extension}
        $extension = $file->getClientOriginalExtension();
        $filename = "{$documentType}.{$extension}";

        // Store file in private storage
        $path = $file->storeAs($directory, $filename, 'local');

        return $path;
    }

    /**
     * Delete an application document.
     */
    public function delete(string $path): bool
    {
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->delete($path);
        }

        return false;
    }

    /**
     * Delete all documents for an application.
     */
    public function deleteAll(string $referenceNumber): bool
    {
        $directory = "applications/{$referenceNumber}";

        if (Storage::disk('local')->exists($directory)) {
            return Storage::disk('local')->deleteDirectory($directory);
        }

        return false;
    }

    /**
     * Generate a temporary download URL for an admin.
     */
    public function getDownloadUrl(string $path): string
    {
        // For now, return a route that will handle the download
        // This will be implemented in the admin controller
        return route('admin.applications.download', ['path' => base64_encode($path)]);
    }

    /**
     * Get the full path for a document.
     */
    public function getFullPath(string $path): string
    {
        return Storage::disk('local')->path($path);
    }

    /**
     * Check if a document exists.
     */
    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }
}
