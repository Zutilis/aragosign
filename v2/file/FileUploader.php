<?php
class FileUploader {
    private $file;
    private $targetPath;

    public function __construct($file, $targetPath) {
        $this->file = $file;
        $this->targetPath = $targetPath;
    }

    public function upload() {
        if (file_exists($this->targetPath)) {
            throw new Exception("Sorry, file already exists.");
        }

        if ($this->file["size"] > 500000) {
            throw new Exception("Sorry, your file is too large.");
        }

        if (!move_uploaded_file($this->file["tmp_name"], $this->targetPath)) {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }
}