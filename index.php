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
		intval($_SESSION['month_planning'][0]), intval($_SESSION['month_planning'][1]));
	$currentStep = 4;  // Passer à l'étape suivante
}

// Gérer l'étape des absences et la génération du PDF
if ($currentStep == 4 && isset($_POST['generatePdf'])) {
	require_once('utils/Utils.php');
	require_once('utils/FileUtils.php');
	require_once('calendrier/CalendrierPDFGenerator.php');

	try {

		// print_r($_SESSION['school_events']);

		$absences = isset($_POST['absences']) ? $_POST['absences'] : [];
		$pdfFilePath = generatePdfFileName($_SESSION['nom'], $_SESSION['prenom']);
		$calendrierOutput = new CalendrierPDFGenerator($_SESSION, $pdfFilePath, $absences);
		$calendrierOutput->generate();
		echo '<p>PDF généré avec succès !</p>';
	} catch (Exception $e) {
		echo '<div class="form-error"><p class="form-error-title">Une erreur est survenue : ' . $e->getMessage() . '</p></div>';
	}
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Formulaire Multi-étapes</title>
	<link rel="stylesheet" href="styles/main.css">
	<link rel="stylesheet" href="styles/form.css">
	<link rel="stylesheet" href="styles/file.css">
</head>

<body>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<div class="calendrier">
		<div class="calendrier-container">
			<h2>Générateur attestation mensuelle</h2>
			<div class="progress">
				<div class="progress-container">
					<div class="progress-bar" id="progress-bar" 
						style="width: <?php echo ($currentStep / 4 * 100); ?>%"></div>
				</div>
				<p id="progress-step"><?php echo $currentStep; ?>/4</p>
			</div>

			<form id="multi-step-form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="step" value="<?php echo $currentStep; ?>">

				<!-- Étape 1 : Saisie du nom et prénom -->
				<?php if ($currentStep == 1) { ?>
					<div class="form-step" id="step1">
						<div class="form-group">
							<div class="obligatory">
								<label for="prenom">Entrer votre prénom</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?php echo isset($_SESSION['prenom']) ? $_SESSION['prenom'] : ''; ?>" required>
						</div>
						<div class="form-group">
							<div class="obligatory">
								<label for="nom">Entrer votre nom de famille</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="nom" id="nom" placeholder="Nom" value="<?php echo isset($_SESSION['nom']) ? $_SESSION['nom'] : ''; ?>" required>
						</div>
						<div class="form-nav">
							<div class="left"></div>
							<div class="right">
								<button type="submit" class="next-step">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Étape 2 : Saisie de l'entreprise et de la classe -->
				<?php if ($currentStep == 2) { ?>
					<div class="form-step" id="step2">
						<div class="form-group">
							<div class="obligatory">
								<label for="entreprise">Entrer le nom de votre entreprise</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="entreprise" id="entreprise" placeholder="Entreprise" value="<?php echo isset($_SESSION['entreprise']) ? $_SESSION['entreprise'] : ''; ?>" required>
						</div>
						<div class="form-group">
							<div class="obligatory">
								<label for="classe">Entrer le nom de votre classe</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="classe" id="classe" placeholder="Classe" value="<?php echo isset($_SESSION['classe']) ? $_SESSION['classe'] : ''; ?>" required>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="prev-step" name="step" value="1">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
							<div class="right">
								<button type="submit" class="next-step">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Étape 3 : Upload du fichier ICS -->
				<?php if ($currentStep == 3) { ?>
					<div class="form-step" id="step3">
						<div class="form-group">
							<div class="obligatory">
								<label for="planningFile">Sélectionner le planning à utiliser
									(<a class="font-size-m" href="help.html" target="__blank">Aide</a>)
								</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="file" name="planningFile" id="planningFile" class="file-input" accept=".ics" onchange="handleFileSelect(event, 'planningFilePreview', 'planningFileCancel')" required>
							<label for="planningFile" class="custom-file-label">Choisir un fichier (format .ics)</label>
							<div id="planningFilePreview" class="file-preview" style="display:none;">
								<div class="file-image">
									<img src="assets/svg/document.svg" alt="">
								</div>
								<div class="file-name-preview"></div>
								<button type="button" id="planningFileCancel" class="file-cancel file-image" onclick="cancelFile('planningFile', 'planningFilePreview', 'planningFileCancel')">
									<img src="assets/svg/rubbish.svg" alt="">
								</button>
							</div>
						</div>
						<div class="form-group">
							<div class="obligatory">
								<label for="month_planning">Sélectionner un mois</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="month" id="month_planning" name="month_planning" min="2023-09" max="2025-07" value="<?php echo date('Y') . '-' . date('m'); ?>" required>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="prev-step" name="step" value="2">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
							<div class="right">
								<button type="submit" class="next-step">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Étape 4 : Sélection des absences -->
				<?php if ($currentStep == 4 && json_encode($_SESSION['school_events'])) { ?>
					<div class="form-step" id="step4">
						<div class="form-group" id="absence-section">
							<label>Indiquer vos absences (facultatif)</label>
						</div>
						<div class="form-group">
							<button type="button" id="add-absence" class="form-button">Ajouter une absence</button>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="prev-step" name="step" value="3">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
							<div class="right">
								<button type="submit" name="generatePdf" class="next-step">
									<p class="form-nav-text">Générer PDF</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>

					<script>
						$(document).ready(function() {
							let absenceCount = 0;
							const schoolEvents = <?php echo json_encode($_SESSION['school_events']); ?>;

							// Fonction pour ajouter une absence
							function addAbsence() {
								absenceCount++;

								// Créer un conteneur pour la nouvelle absence
								const absenceContainer = $('<div class="absence-item"></div>');

								// Ajouter le choix entre journée entière ou événement spécifique
								const absenceType = $(`
									<select name="absence_type_${absenceCount}" required>
										<option value="">Sélectionner un type d'absence</option>
										<option value="jour_entier">Jour entier</option>
										<option value="cours">Cours</option>
									</select>
								`);

								// Créer un champ de date pour une absence complète (par défaut caché)
								const fullDayInput = $(`<input type="date" name="full_day_absence_${absenceCount}" style="display: none;" required>`);

								// Créer une liste d'événements scolaires (par défaut caché)
								const eventSelect = $(`
									<select name="event_absence_${absenceCount}" style="display: none;" required>
										<option value="">Sélectionner un cours</option>
									</select>
								`);

								// Ajouter les événements scolaires à la liste déroulante
								$.each(schoolEvents, function(date, events) {
									$.each(events, function(_, event) {
										eventSelect.append(`<option value="${event.id}">Le ${date} (${event.hourStart}h - ${event.hourEnd}h) : ${event.title}</option>`);
									});
								});

								// Gérer le changement de type d'absence (jour entier ou événement)
								absenceType.change(function() {
									const selectedValue = $(this).val();
									if (selectedValue === 'jour_entier') {
										fullDayInput.show();
										eventSelect.hide();
									} else if (selectedValue === 'cours') {
										fullDayInput.hide();
										eventSelect.show();
									} else {
										fullDayInput.hide();
										eventSelect.hide();
									}
								});

								// Ajouter un bouton pour supprimer l'absence
								const deleteButton = $('<button type="button" class="absence-cancel"><img src="assets/svg/absenceRubbish.svg"></button>');

								deleteButton.click(function() {
									absenceContainer.remove();
								});

								// Ajouter tous les éléments au conteneur de l'absence
								absenceContainer.append(absenceType, fullDayInput, eventSelect, deleteButton);

								// Ajouter l'absence au formulaire
								$('#absence-section').append(absenceContainer);
							}

							// Ajouter une absence au clic sur le bouton "Ajouter une absence"
							$('#add-absence').click(addAbsence);
						});
					</script>
				<?php } ?>
			</form>
		</div>
	</div>
	<script type="text/javascript" src="assets/js/stepForm.js"></script>
	<script type="text/javascript" src="assets/js/planningFile.js"></script>
</body>

</html>