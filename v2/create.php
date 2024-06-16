<?php
require_once('utils/Utils.php');
require_once('file/FileUploader.php');
require_once('events/EventProcessor.php');
require_once('calendrier/CalendrierOutput.php');

$root = $_SERVER['DOCUMENT_ROOT'];
$plannings_path = 'plannings/';
$outputPath = 'uploads/output.pdf';

if (isset($_FILES["planningFile"]) && isset($_POST['month_planning']))
{
    $year = intval(explode('-', $_POST['month_planning'])[0]);
    $month = intval(explode('-', $_POST['month_planning'])[1]);

    if ($month <= 0 || $month > 12) {
        echo "Sorry, the month is invalid";
        return;
    }

    $planningFile = $_FILES["planningFile"];

    $planningFilePath = $plannings_path . basename($planningFile["name"]);

    try {

        $planningUploader = new FileUploader($_FILES["planningFile"], $planningFilePath);
        $planningUploader->upload();

        $eventProcessor = new EventProcessor($planningFilePath, $month, $year);
        $calendrier = $eventProcessor->process();

        $calendrierOutput = new CalendrierOutput($calendrier, $outputPath);
        $calendrierOutput->generateOutput();

    } catch (Exception $e) {
        echo $e->getMessage();
    }

    unlink($planningFilePath);
    unlink($signFilePath);
    unlink($outputPath);
}
?>