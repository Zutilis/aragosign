<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/main.css">
</head>

<body>
	<div class="calendrier-container">
		<div class="container" enctype="multipart/form-data">
			<h2>Calendrier</h2>
			<div class="progress-container">
				<div class="progress-bar" id="progress-bar" style="width: 0;"></div>
			</div>

			<?php
			session_start();
			$root = $_SERVER['DOCUMENT_ROOT'];

			if (isset($_POST['download'])) {

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
							<button type="button" class="form-nav-next" onclick="nextStep()">
								<p class="form-nav-text">Suivant</p>
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff" transform="rotate(180)">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
							</button>
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
							<button type="button" class="form-nav-prev" onclick="prevStep()">
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
								<p class="form-nav-text">Précédent</p>
							</button>
							<button type="button" class="form-nav-next" onclick="nextStep()">
								<p class="form-nav-text">Suivant</p>
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff" transform="rotate(180)">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
							</button>
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
									<img src="assets/document.svg" alt="">
								</div>
								<div class="file-name-preview"></div>
								<button type="button" id="planningFileCancel" class="cancel-btn file-image" onclick="cancelFile('planningFile', 'planningFilePreview', 'planningFileCancel')">
									<img src="assets/rubbish.svg" alt="">
								</button>
							</div>
						</div>
						<div class="form-group">
							<label for="month_planning">Selectionner un mois</label>
							<input type="month" id="month_planning" name="month_planning" min="2023-09" max="2025-07" value="<?php echo date('Y') . '-' . date('m'); ?>" required>
						</div>
						<div class="form-nav">
							<button type="button" class="form-nav-prev" onclick="prevStep()">
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
								<p class="form-nav-text">Précédent</p>
							</button>
							<button type="button" class="form-nav-next" onclick="nextStep()">
								<p class="form-nav-text">Suivant</p>
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff" transform="rotate(180)">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
							</button>
						</div>
					</div>

					<!-- Étape 4 -->
					<div class="form-step" id="step4" style="display: none;">
						<div class="form-group">
							<h4 class="form-subtitle">Le planning est prêt à être téléchargé !</h4>
							<button type="submit" name="download" class="form-button">Télécharger</button>
						</div>
						<div class="form-nav">
							<button type="button" class="form-nav-prev" onclick="prevStep()">
								<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#007bff">
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
									<g id="SVGRepo_iconCarrier">
										<g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<g id="ic_fluent_ios_arrow_left_24_regular" fill="#007bff" fill-rule="nonzero">
												<path d="M4.29642509,11.9999996 L12.7875444,3.27301622 C13.0763983,2.97613862 13.0698938,2.50130943 12.7730162,2.21245555 C12.4761386,1.92360167 12.0013094,1.93010618 11.7124556,2.22698378 L2.71245555,11.4769838 C2.42918148,11.7681266 2.42918148,12.2318734 2.71245555,12.5230162 L11.7124556,21.7730162 C12.0013094,22.0698938 12.4761386,22.0763983 12.7730162,21.7875444 C13.0698938,21.4986906 13.0763983,21.0238614 12.7875444,20.7269838 L4.29642509,11.9999996 Z" id="🎨-Color"> </path>
											</g>
										</g>
									</g>
								</svg>
								<p class="form-nav-text">Précédent</p>
							</button>
						</div>
					</div>
				</form>
			<?php
			}
			?>

		</div>
	</div>

	<script>
		let currentStep = 0;
		const steps = document.querySelectorAll('.form-step');
		const progressbar = document.getElementById('progress-bar');

		/**
		 * Affiche l'étape actuelle et ajuste la barre de progression.
		 * 
		 * @param {number} stepIndex L'index de l'étape à afficher.
		 */
		function showStep(stepIndex) {
			steps.forEach((step, index) => {
				step.style.display = index === stepIndex ? 'block' : 'none';
			});
			progressbar.style.width = (currentStep / (steps.length - 1) * 100) + '%';
		}

		/**
		 * Passe à l'étape suivante du formulaire, si possible.
		 */
		function nextStep() {
			if (validatePreviousSteps()) {
				if (currentStep < steps.length - 1) {
					currentStep++;
					showStep(currentStep);
				}
			}
		}

		/**
		 * Revient à l'étape précédente du formulaire, si possible.
		 */
		function prevStep() {
			if (currentStep > 0) {
				currentStep--;
				showStep(currentStep);
			}
		}

		/**
		 * Valide tous les champs obligatoires des étapes précédentes avant de permettre la navigation.
		 * 
		 * @return {boolean} true si tous les champs requis sont remplis, sinon false.
		 */
		function validatePreviousSteps() {
			// Sélectionne tous les champs des étapes précédentes
			for (let i = 0; i <= currentStep; i++) {
				const inputs = steps[i].querySelectorAll('input[required], select[required], textarea[required]');
				for (let input of inputs) {
					if (!input.checkValidity()) {
						// Affiche une erreur pour l'utilisateur si un champ requis est vide
						input.reportValidity();
						return false;
					}
				}
			}
			return true;
		}

		// Initialisation de la première étape
		showStep(currentStep);
	</script>

	<script>
		// Ajout de la fonctionnalité pour les touches "Entrée", "Flèche gauche" et "Flèche droite"
		document.addEventListener('keydown', function(event) {
			if (event.key === 'Enter' || event.key === 'ArrowRight') {
				if (currentStep < steps.length - 1) {
					event.preventDefault();
					nextStep();
				}
			} else if (event.key === 'ArrowLeft') {
				event.preventDefault(); // Empêche le comportement par défaut de la touche Flèche gauche
				prevStep();
			}
		});
	</script>

	<script>
		/**
		 * Formate la taille des fichiers en unités lisibles.
		 * 
		 * @param {number} bytes Taille du fichier en octets.
		 * @param {number} decimals Nombre de décimales à afficher.
		 * @return {string} Taille formatée (ex: '1.23 MB').
		 */
		function formatBytes(bytes, decimals = 2) {
			if (!+bytes) return '0 B';

			const k = 1024;
			const dm = decimals < 0 ? 0 : decimals;
			const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
			const i = Math.floor(Math.log(bytes) / Math.log(k));

			return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
		}

		/**
		 * Gère la sélection d'un fichier, affiche son aperçu et la taille.
		 * 
		 * @param {Event} event L'événement déclenché lors de la sélection du fichier.
		 * @param {string} previewId L'ID de l'élément de prévisualisation.
		 * @param {string} cancelBtnId L'ID du bouton d'annulation de fichier.
		 */
		function handleFileSelect(event, previewId, cancelBtnId) {
			const file = event.target.files[0];
			const filePreview = document.getElementById(previewId);
			const fileName = filePreview.getElementsByClassName('file-name-preview')[0];

			if (file) {
				// Affiche le nom du fichier et sa taille formatée
				fileName.innerHTML = `<p>${file.name} | ${formatBytes(file.size)} </p>`;
				filePreview.style.display = 'flex'; // Affiche la prévisualisation
			} else {
				fileName.innerHTML = '';
				filePreview.style.display = 'none'; // Masque la prévisualisation si aucun fichier
			}
		}

		/**
		 * Annule la sélection du fichier et réinitialise la prévisualisation.
		 * 
		 * @param {string} inputId L'ID de l'élément input (fichier).
		 * @param {string} previewId L'ID de l'élément de prévisualisation.
		 * @param {string} cancelBtnId L'ID du bouton d'annulation de fichier.
		 */
		function cancelFile(inputId, previewId, cancelBtnId) {
			const input = document.getElementById(inputId);
			const filePreview = document.getElementById(previewId);
			const fileName = filePreview.getElementsByClassName('file-name-preview')[0];

			input.value = ''; // Réinitialise la sélection de fichier
			fileName.innerHTML = ''; // Vide le nom du fichier dans la prévisualisation
			filePreview.style.display = 'none'; // Masque la prévisualisation
		}
	</script>

</body>

</html>