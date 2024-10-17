<?php
require_once('events/SchoolEvent.php');
session_start();

// Gérer la progression à travers les étapes
$currentStep = 1;

if (isset($_POST['step'])) {
	$currentStep = intval($_POST['step']);
}

// Étape 1 : Stocker nom et prénom dans la session
if ($currentStep == 1 && isset($_POST['prenom']) && isset($_POST['nom'])) {
	$_SESSION['prenom'] = $_POST['prenom'];
	$_SESSION['nom'] = $_POST['nom'];
	$currentStep = 2;  // Passer à l'étape suivante
}

// Étape 2 : Stocker l'entreprise et la classe dans la session
if ($currentStep == 2 && isset($_POST['entreprise']) && isset($_POST['classe'])) {
	$_SESSION['entreprise'] = $_POST['entreprise'];
	$_SESSION['classe'] = $_POST['classe'];
	$currentStep = 3;  // Passer à l'étape suivante
}

// Étape 3 : Stocker le fichier ICS et charger les événements dans la session
if ($currentStep == 3 && isset($_FILES['planningFile'])) {
	$planningFilePath = $_FILES["planningFile"]["tmp_name"];
	require_once('events/SchoolEventManager.php');  // Charger la classe SchoolEventManager
	$schoolManager = new SchoolEventManager($planningFilePath);
	$schoolManager->loadAll();

	// Stocker l'objet sérialisé dans la session
	$_SESSION['month_planning'] = explode('-', $_POST['month_planning']);
	$_SESSION['school_events'] = $schoolManager->getSchoolEventsByDate(
		intval($_SESSION['month_planning'][0]),
		intval($_SESSION['month_planning'][1])
	);
	$currentStep = 4;  // Passer à l'étape suivante
}

// Gérer l'étape des absences et la génération du PDF
if ($currentStep == 4 && isset($_POST['generatePdf'])) {
	require_once('utils/Utils.php');
	require_once('utils/FileUtils.php');
	require_once('calendrier/CalendrierPDFGenerator.php');

	try {
		// Récupérer les absences des cours (event_absence_x) et des jours complets (full_day_absence_x)
		$absences = ['eventAbsences' => [], 'fullDayAbsences' => []];

		foreach ($_POST as $key => $value) {
			// Récupérer les absences liées aux cours
			if (strpos($key, 'event_absence_') === 0)
				$absences['eventAbsences'][] = $value;

			// Récupérer les absences liées aux journées complètes
			if (strpos($key, 'full_day_absence_') === 0)
				$absences['fullDayAbsences'][] = $value;
		}

		$pdfFilePath = generatePdfFileName($_SESSION['nom'], $_SESSION['prenom']);

		$calendrierOutput = new CalendrierPDFGenerator($_SESSION, $pdfFilePath, $absences);
		$calendrierOutput->generate();
	} catch (Exception $e) {
		echo '<div class="form-error"><p class="form-error-title">Une erreur est survenue : ' . $e->getMessage() . '</p></div>';
	}
}

// Retourne la progression actuelle pour l'afficher dans la vue HTML
return $currentStep;
