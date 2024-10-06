$(document).ready(function() {
    // Intercepte le clic sur les boutons "Précédent"
    $('.prev-step').click(function(event) {
        var step = parseInt($(this).val()); // Récupère la valeur de l'étape précédente
        var $form = $('#multi-step-form');

        // Modifie l'étape et désactive la validation du formulaire pour revenir en arrière
        $form.find('input[name="step"]').val(step);
        $form.prop('novalidate', true); // Désactive temporairement la validation
        $form.submit(); // Soumet le formulaire
    });

    // Intercepter le clic sur le bouton "Suivant"
    $('.next-step').click(function(event) {
        var currentStep = $('input[name="step"]').val();  // Récupère l'étape actuelle
        var fileInput = $('#planningFile');
        var fileLabel = $('.custom-file-label');

        // Vérifier si nous sommes à l'étape 3 (ou que le champ fichier est requis)
        if (currentStep == 3) {
            // Si aucun fichier n'est sélectionné
            if (!fileInput.val()) {
                fileLabel.addClass('input-error');  // Ajouter une classe pour la mise en surbrillance en rouge
                event.preventDefault();  // Empêche la soumission du formulaire
            } else {
                fileLabel.removeClass('input-error');  // Si un fichier est sélectionné, retirer l'erreur
            }
        }
    });
});