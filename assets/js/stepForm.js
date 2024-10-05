document.addEventListener("DOMContentLoaded", function () {
	let currentStep = 0;
	const steps = document.querySelectorAll('.form-step');

	const progressStep = document.getElementById('progress-step');
	const progressStepMax = document.getElementById('progress-step-max');
	const progressbar = document.getElementById('progress-bar');

	const nexts = document.querySelectorAll('.form-nav-next');
	const prevs = document.querySelectorAll('.form-nav-prev');

	// Attacher les événements aux boutons 'Suivant'
	nexts.forEach(function (next) {
		next.addEventListener('click', function (event) {
			nextStep();
		});
	});

	// Attacher les événements aux boutons 'Précédent'
	prevs.forEach(function (prev) {
		prev.addEventListener('click', function (event) {
			prevStep();
		});
	});

	// Ajout de la fonctionnalité pour les touches "Entrée", "Flèche gauche" et "Flèche droite"
	document.addEventListener('keydown', function (event) {
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

	/**
	 * Affiche l'étape actuelle et ajuste la barre de progression.
	 */
	function showStep(stepIndex) {
		steps.forEach((step, index) => {
			step.style.display = index === stepIndex ? 'block' : 'none';
		});
		progressbar.style.width = Math.max(1, currentStep / (steps.length - 1) * 100) + '%';
		progressStep.innerHTML = '' + currentStep;
		progressStepMax.innerHTML = '-' + (steps.length - 1- currentStep);
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
	 */
	function validatePreviousSteps() {
		for (let i = 0; i <= currentStep; i++) {
			const inputs = steps[i].querySelectorAll('input[required], select[required], textarea[required]');
			for (let input of inputs) {
				if (!input.checkValidity()) {
					input.reportValidity();
					return false;
				}
			}
		}
		return true;
	}

	// Initialiser la première étape
	showStep(currentStep);
});