<?php

namespace App\Services;

use Symfony\Component\Form\Form;

class FileUploader
{
    /**
     * @param Form $fileFormField the input form where we find the file
     * @return string|null The name of the file
     */
    public function saveFile(Form $fileFormField, string $targetDirectory): ?string
    {
        // UploadedFile received
        $file = $fileFormField->getData();

        if ($file == null) {
            return null;
        }
        
        // New name of the image received
        $newFileName = $this->createFileName($file->getClientOriginalExtension());

        // We move this file to his directory
        $file->move($targetDirectory, $newFileName);
        
        return $newFileName;
    }
    
    /**
     * @param string $extension extension of the file
     * @return string random name with his extension name
     */
    public function createFileName(string $extension)
    {
        // New name of the file
        $newFileName = preg_replace('/[+=\/]/', random_int(0, 9), base64_encode(random_bytes(6)));
        // Return of the file with his new name and his extension
        return $newFileName . '.' . $extension;;
    }
}