<?php
/**
 * Evolvcode CMS - File Upload Handler
 * 
 * Securely handles file uploads with validation.
 */

class FileUpload
{
    private array $errors = [];
    private ?string $uploadedPath = null;
    
    /**
     * Upload an image file
     */
    public function uploadImage(array $file, string $directory = 'images'): ?string
    {
        // Validate file
        if (!$this->validateFile($file)) {
            return null;
        }
        
        // Validate image type
        if (!$this->validateImageType($file)) {
            return null;
        }
        
        // Generate unique filename
        $extension = $this->getExtension($file['name']);
        $filename = $this->generateFilename($extension);
        
        // Create full directory path (including date folders from filename)
        $targetPath = UPLOADS_PATH . '/' . $directory . '/' . $filename;
        $targetDir = dirname($targetPath);
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->errors[] = 'Failed to move uploaded file.';
            return null;
        }
        
        // Verify it's a valid image
        if (!$this->isValidImage($targetPath)) {
            unlink($targetPath);
            $this->errors[] = 'Invalid image file.';
            return null;
        }
        
        
        $this->uploadedPath = '/assets/uploads/' . $directory . '/' . $filename;
        return $this->uploadedPath;
    }
    
    /**
     * Upload any allowed file
     */
    public function uploadFile(array $file, string $directory = 'files', array $allowedTypes = []): ?string
    {
        // Validate file
        if (!$this->validateFile($file)) {
            return null;
        }
        
        // Validate file type if specified
        if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
            $this->errors[] = 'File type not allowed.';
            return null;
        }
        
        // Generate unique filename
        $extension = $this->getExtension($file['name']);
        $filename = $this->generateFilename($extension);
        
        // Create full directory path (including date folders from filename)
        $targetPath = UPLOADS_PATH . '/' . $directory . '/' . $filename;
        $targetDir = dirname($targetPath);
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->errors[] = 'Failed to move uploaded file.';
            return null;
        }
        
        
        $this->uploadedPath = '/assets/uploads/' . $directory . '/' . $filename;
        return $this->uploadedPath;
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile(array $file): bool
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Check file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->errors[] = 'File is too large. Maximum size is ' . $this->formatFileSize(MAX_UPLOAD_SIZE) . '.';
            return false;
        }
        
        // Check if file is empty
        if ($file['size'] === 0) {
            $this->errors[] = 'File is empty.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate image MIME type
     */
    private function validateImageType(array $file): bool
    {
        // Check MIME type from file info
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            $this->errors[] = 'Invalid image type. Allowed types: JPEG, PNG, GIF, WebP.';
            return false;
        }
        
        // Check extension
        $extension = strtolower($this->getExtension($file['name']));
        if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
            $this->errors[] = 'Invalid file extension.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Verify the file is a valid image
     */
    private function isValidImage(string $path): bool
    {
        $imageInfo = @getimagesize($path);
        return $imageInfo !== false;
    }
    
    /**
     * Get file extension
     */
    private function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
    
    /**
     * Generate unique filename
     */
    private function generateFilename(string $extension): string
    {
        return date('Y/m/') . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    }
    
    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds maximum upload size.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds maximum form size.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by PHP extension.'
        ];
        
        return $messages[$errorCode] ?? 'Unknown upload error.';
    }
    
    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        
        return round($bytes, 2) . ' ' . $units[$index];
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get first error
     */
    public function getError(): ?string
    {
        return $this->errors[0] ?? null;
    }
    
    /**
     * Check if there are errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Get uploaded file path
     */
    public function getUploadedPath(): ?string
    {
        return $this->uploadedPath;
    }
    
    /**
     * Delete a file
     */
    public static function delete(string $path): bool
    {
        $fullPath = ROOT_PATH . $path;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Create image thumbnail
     */
    public function createThumbnail(string $sourcePath, int $width = 300, int $height = 300): ?string
    {
        $fullSourcePath = ROOT_PATH . $sourcePath;
        
        if (!file_exists($fullSourcePath)) {
            return null;
        }
        
        $imageInfo = getimagesize($fullSourcePath);
        if ($imageInfo === false) {
            return null;
        }
        
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create source image resource
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($fullSourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($fullSourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($fullSourcePath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($fullSourcePath);
                break;
            default:
                return null;
        }
        
        if (!$sourceImage) {
            return null;
        }
        
        // Calculate thumbnail dimensions (maintain aspect ratio)
        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $thumbWidth = (int) ($sourceWidth * $ratio);
        $thumbHeight = (int) ($sourceHeight * $ratio);
        
        // Create thumbnail image
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
        
        // Resize
        imagecopyresampled(
            $thumbnail, $sourceImage,
            0, 0, 0, 0,
            $thumbWidth, $thumbHeight,
            $sourceWidth, $sourceHeight
        );
        
        // Generate thumbnail path
        $pathInfo = pathinfo($sourcePath);
        $thumbPath = $pathInfo['dirname'] . '/thumbs/' . $pathInfo['basename'];
        $fullThumbPath = ROOT_PATH . $thumbPath;
        
        // Create thumbs directory
        $thumbDir = dirname($fullThumbPath);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }
        
        // Save thumbnail
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $fullThumbPath, 85);
                break;
            case 'image/png':
                imagepng($thumbnail, $fullThumbPath, 8);
                break;
            case 'image/gif':
                imagegif($thumbnail, $fullThumbPath);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $fullThumbPath, 85);
                break;
        }
        
        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
        
        return $thumbPath;
    }
}
