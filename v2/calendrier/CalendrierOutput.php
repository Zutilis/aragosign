<?php

class CalendrierOutput {

    private $calendrier;
    private $outputPath;

    public function __construct($calendrier, $outputPath) 
    {
        $this->calendrier = $calendrier;
        $this->outputPath = $outputPath;
    }

    public function generateOutput() 
    {
        $this->calendrier->output($this->outputPath);
        $this->download($this->outputPath);
    }

    private function download($file) 
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}