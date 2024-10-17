<?php
require_once('events/SchoolEvent.php');
session_start();

/**
 * Gère la progression des étapes et le stockage des données dans la session.
 * 
 * @param int $step L'étape actuelle
 * @param array $data Les données soumises dans $_POST ou $_FILES
 * @return int Retourne la prochaine étape
 */
function handleStepProgression($step, $data)
{
	switch ($step) {
		case 1:
			return handleStepOne($data);
		case 2:
			return handleStepTwo($data);
		case 3:
			return handleStepThree($data);
		case 4:
			return handleStepFour($data);
		default:
			return 1; // Par défaut, revient à l'étape 1
	}
}

/**
 * Gère l'étape 1 : Stocker nom et prénom dans la session.
 *
 * @param array $data Les données du formulaire
 * @return int Retourne la prochaine étape (2)
 */
function handleStepOne($data)
{
	if (isset($data['prenom']) && isset($data['nom'])) {
		$_SESSION['prenom'] = $data['prenom'];
		$_SESSION['nom'] = $data['nom'];
		return 2;
	}
	return 1;
}

/**
 * Gère l'étape 2 : Stocker l'entreprise et la classe dans la session.
 *
 * @param array $data Les données du formulaire
 * @return int Retourne la prochaine étape (3)
 */
function handleStepTwo($data)
{
	if (isset($data['entreprise']) && isset($data['classe'])) {
		$_SESSION['entreprise'] = $data['entreprise'];
		$_SESSION['classe'] = $data['classe'];
		return 3;
	}
	return 2;
}

/**
 * Gère l'étape 3 : Stocker le fichier ICS et charger les événements dans la session.
 *
 * @param array $data Les données du formulaire et des fichiers
 * @return int Retourne la prochaine étape (4)
 */
function handleStepThree($data)
{
	if (isset($data['planningFile']['tmp_name'])) {
		$planningFilePath = $data['planningFile']['tmp_name'];
		require_once('events/SchoolEventManager.php');

		$schoolManager = new SchoolEventManager($planningFilePath);
		$schoolManager->loadAll();

		$_SESSION['month_planning'] = explode('-', $data['month_planning']);
		$_SESSION['school_events'] = $schoolManager->getSchoolEventsByDate(
			intval($_SESSION['month_planning'][0]),
			intval($_SESSION['month_planning'][1])
		);
		return 4;
	}
	return 3;
}

/**
 * Gère l'étape 4 : Traitement des absences et génération du PDF.
 *
 * @param array $data Les données du formulaire
 * @return int Retourne la dernière étape (4)
 */
function handleStepFour($data)
{
	if (isset($data['generatePdf'])) {
		require_once('utils/Utils.php');
		require_once('utils/FileUtils.php');
		require_once('calendrier/CalendrierPDFGenerator.php');

		$absences = collectAbsences($data);
		$pdfFilePath = generatePdfFileName($_SESSION['nom'], $_SESSION['prenom']);

		try {
			$calendrierOutput = new CalendrierPDFGenerator($_SESSION, $pdfFilePath, $absences);
			$calendrierOutput->generate();
		} catch (Exception $e) {
			echo '<div class="form-error"><p class="form-error-title">Une erreur est survenue : ' . $e->getMessage() . '</p></div>';
		}
	}
	return 4;
}

/**
 * Récupère les absences des cours et des jours complets à partir des données soumises.
 *
 * @param array $data Les données du formulaire
 * @return array Les absences organisées sous deux catégories : eventAbsences et fullDayAbsences
 */
function collectAbsences($data)
{
	$absences = ['eventAbsences' => [], 'fullDayAbsences' => []];

	foreach ($data as $key => $value) {
		if (strpos($key, 'event_absence_') === 0) {
			$absences['eventAbsences'][] = $value;
		} elseif (strpos($key, 'full_day_absence_') === 0) {
			$absences['fullDayAbsences'][] = $value;
		}
	}

	return $absences;
}

// Déterminer l'étape actuelle et la traiter
$currentStep = isset($_POST['step']) ? intval($_POST['step']) : 1;
$currentStep = handleStepProgression($currentStep, $_POST + $_FILES);

// Retourne la progression actuelle pour l'afficher dans la vue HTML
return $currentStep;
