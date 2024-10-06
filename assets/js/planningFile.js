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
 * Gère la sélection d'un fichier, affiche son aperçu et la taille en jQuery.
 * 
 * @param {Event} event L'événement déclenché lors de la sélection du fichier.
 * @param {string} previewId L'ID de l'élément de prévisualisation.
 * @param {string} cancelBtnId L'ID du bouton d'annulation de fichier.
 */
function handleFileSelect(event, previewId, cancelBtnId) {
    const file = event.target.files[0];
    const $filePreview = $('#' + previewId);
    const $fileName = $filePreview.find('.file-name-preview');

    if (file) {
        // Affiche le nom du fichier et sa taille formatée
        $fileName.html(`<p>${file.name} | ${formatBytes(file.size)} </p>`);
        $filePreview.css('display', 'flex'); // Affiche la prévisualisation
    } else {
        $fileName.html('');
        $filePreview.hide(); // Masque la prévisualisation si aucun fichier
    }
}

/**
 * Annule la sélection du fichier et réinitialise la prévisualisation en jQuery.
 * 
 * @param {string} inputId L'ID de l'élément input (fichier).
 * @param {string} previewId L'ID de l'élément de prévisualisation.
 * @param {string} cancelBtnId L'ID du bouton d'annulation de fichier.
 */
function cancelFile(inputId, previewId, cancelBtnId) {
    const $input = $('#' + inputId);
    const $filePreview = $('#' + previewId);
    const $fileName = $filePreview.find('.file-name-preview');

    $input.val(''); // Réinitialise la sélection de fichier
    $fileName.html(''); // Vide le nom du fichier dans la prévisualisation
    $filePreview.hide(); // Masque la prévisualisation
}