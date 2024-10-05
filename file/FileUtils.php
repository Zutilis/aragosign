<?php
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