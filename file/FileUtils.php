<?php

/**
 * Génère un nom unique pour un fichier ICS basé sur le nom, le prénom et le fichier existant.
 * Si un fichier avec le même nom existe déjà, un suffixe incrémental est ajouté.
 * 
 * @param string $dir Répertoire de destination
 * @param string $filename Nom du fichier ICS original
 * @param string $nom Nom de la personne
 * @param string $prenom Prénom de la personne
 * @param int $i Suffixe incrémental pour garantir l'unicité (par défaut : 0)
 * @return string Nom unique généré pour le fichier ICS
 */
function generateIcsFileName($dir, $filename, $nom, $prenom, $i = 0) 
{
    // Génération du chemin complet du fichier ICS avec ou sans suffixe
    $ret = $dir . '/plannings/' 
            . str_replace('.ics', '', $filename)  // Supprime l'extension .ics si présente dans le nom de fichier
            . '_' . $nom 
            . '_' . $prenom 
            . ($i == 0 ? '' : '_' . $i)  // Ajoute le suffixe incrémental si nécessaire
            . '.ics';

    // Si un fichier avec ce nom existe déjà, on appelle récursivement la fonction avec un suffixe incrémenté
    if (file_exists($ret)) {
        return generateIcsFileName($dir, $filename, $nom, $prenom, ++$i);
    }

    return $ret;
}

/**
 * Génère un nom de fichier PDF basé sur le nom et le prénom.
 * 
 * @param string $nom Nom de la personne
 * @param string $prenom Prénom de la personne
 * @return string Nom généré pour le fichier PDF
 */
function generatePdfFileName($nom, $prenom)
{
    return 'Attestation_' . $nom . '_' . $prenom . '.pdf';
}