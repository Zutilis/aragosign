<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/main.css">
</head>

<body>
	<div class="calendrier">
		<div class="calendrier-container" enctype="multipart/form-data">
			<h2>Générateur</h2>
			<div class="progress">
				<p id="progress-step"></p>
				<div class="progress-container">
					<div class="progress-bar" id="progress-bar" style="width: 0%;"></div>
				</div>
				<p id="progress-step-max"></p>
			</div>

			<?php
			session_start();
			$root = $_SERVER['DOCUMENT_ROOT'];

			if (isset($_POST['absenceStep'])) {

				require_once('utils/Utils.php');
				require_once('utils/FileUtils.php');
				require_once('events/SchoolEventManager.php');
				require_once('calendrier/CalendrierPDFGenerator.php');

				try {

					$planningFilePath = $_FILES["planningFile"]["tmp_name"];

					// Instancie le gestionnaire d'événements en charge de charger le fichier de planning
					$schoolManager = new SchoolEventManager($planningFilePath);
					$schoolManager->loadAll();  // Charge tous les événements du planning

					$pdfFilePath = generatePdfFileName($_POST['nom'], $_POST['prenom']);

					// // Génère le PDF à partir du fichier ICS
					$calendrierOutput = new CalendrierPDFGenerator($_POST, $schoolManager, $pdfFilePath);
					$calendrierOutput->generate();
				} catch (Exception $e) {
					// Gère les erreurs et affiche un message à l'utilisateur
					echo '
						<div class="form-error">
							<p class="form-error-title">Une erreur est survenue : ' . $e->getMessage() . '</p>
						</div>
					';
				}
			} else {
			?>
				<form id="multi-step-form" method="post" enctype="multipart/form-data">
					<!-- Étape 1 -->
					<div class="form-step" id="step1">
						<div class="form-group">
							<div class="obligatory">
								<label for="prenom">Entrer votre prénom</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?php echo isset($_POST['prenom']) ? $_POST['prenom'] : ''; ?>" required>
						</div>
						<div class="form-group">
							<div class="obligatory">
								<label for="nom">Entrer votre nom de famille</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="nom" id="nom" placeholder="Nom" value="<?php echo isset($_POST['nom']) ? $_POST['nom'] : ''; ?>" required>
						</div>
						<div class="form-nav">
							<div class="left"></div>
							<div class="right">
								<button type="button" class="form-nav-next">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>

					<!-- Étape 2 -->
					<div class="form-step" id="step2" style="display: none;">
						<div class="form-group">
							<div class="obligatory">
								<label for="entreprise">Entrer le nom de votre entreprise</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="entreprise" id="entreprise" placeholder="Entreprise" value="<?php echo isset($_POST['entreprise']) ? $_POST['entreprise'] : ''; ?>" required>
						</div>
						<div class="form-group">
							<div class="obligatory">
								<label for="classe">Entrer le nom de votre classe</label>
								<p class="obligatory-char">*</p>
							</div>
							<input type="text" name="classe" id="classe" placeholder="Classe" value="<?php echo isset($_POST['classe']) ? $_POST['classe'] : ''; ?>" required>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="form-nav-prev">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
							<div class="right">
								<button type="button" class="form-nav-next">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>

					<!-- Étape 3 -->
					<div class="form-step" id="step3" style="display: none;">
						<div class="form-group">
							<label for="planningFile">Sélectionner le planning à utiliser
								(<a class="font-size-m" href="help.html" target="__blank">Aide</a>)
							</label>
							<input type="file" name="planningFile" id="planningFile" class="file-input" accept=".ics" onchange="handleFileSelect(event, 'planningFilePreview', 'planningFileCancel')" required>
							<label for="planningFile" class="custom-file-label">Choisir un fichier (format .ics)</label>
							<div id="planningFilePreview" class="file-preview" style="display:none;">
								<div class="file-image">
									<img src="assets/svg/document.svg" alt="">
								</div>
								<div class="file-name-preview"></div>
								<button type="button" id="planningFileCancel" class="cancel-btn file-image" onclick="cancelFile('planningFile', 'planningFilePreview', 'planningFileCancel')">
									<img src="assets/svg/rubbish.svg" alt="">
								</button>
							</div>
						</div>
						<div class="form-group">
							<label for="month_planning">Selectionner un mois</label>
							<input type="month" id="month_planning" name="month_planning" min="2023-09" max="2025-07" value="<?php echo date('Y') . '-' . date('m'); ?>" required>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="form-nav-prev">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
							<div class="right">
								<button type="button" class="form-nav-next">
									<p class="form-nav-text">Suivant</p>
									<img src="assets/svg/navnext.svg" alt="">
								</button>
							</div>
						</div>
					</div>

					<!-- Étape 4 -->
					<div class="form-step" id="step4" style="display: none;">
						<div class="form-group">
							<h4 class="form-subtitle">Le planning est prêt à être téléchargé !</h4>
							<button type="submit" name="absenceStep" class="form-button">Télécharger</button>
						</div>
						<div class="form-nav">
							<div class="left">
								<button type="button" class="form-nav-prev">
									<img src="assets/svg/navprev.svg" alt="">
									<p class="form-nav-text">Précédent</p>
								</button>
							</div>
						</div>
					</div>
				</form>
			<?php
			}
			?>

		</div>
	</div>
	<script type="text/javascript" src="assets/js/stepForm.js"></script>
	<script type="text/javascript" src="assets/js/planningFile.js"></script>
</body>

</html>