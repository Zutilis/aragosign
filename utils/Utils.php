<?php

/**
 * Supprime toutes les occurrences des chaînes spécifiées dans la chaîne donnée.
 * 
 * @param string $str La chaîne de base
 * @param string ...$str_to_remove Chaînes à supprimer de la chaîne de base
 * @return string La chaîne après suppression des éléments spécifiés
 */
function remove($str, ...$str_to_remove)
{
    // Parcourt toutes les chaînes à supprimer et les remplace par une chaîne vide
    foreach ($str_to_remove as $tmp) {
        $str = str_replace($tmp, '', $str);
    }

    return $str;
}

/**
 * Tronque une chaîne si elle dépasse un certain nombre de caractères.
 * 
 * @param string $str La chaîne à tronquer
 * @param int $nb_chars Le nombre maximum de caractères autorisés
 * @return string La chaîne tronquée suivie de ".." si nécessaire
 */
function truncate($str, $nb_chars) 
{
    // Si la chaîne dépasse la longueur maximale, elle est tronquée et '..' est ajouté
    if (strlen($str) > $nb_chars) {
        return substr($str, 0, $nb_chars) . '..';
    }

    // Sinon, la chaîne est retournée telle quelle
    return $str;
}

/**
 * Convertit un numéro de mois (1 à 12) en son équivalent en français.
 * 
 * @param int $nbr_month Numéro du mois (1 pour janvier, 12 pour décembre)
 * @return string Le nom du mois en français ou "null" si le numéro est invalide
 */
function monthToFrench($nbr_month)
{
    // Tableau associatif pour la correspondance des mois
    $months = [
        1 => "Janvier",
        2 => "Février",
        3 => "Mars",
        4 => "Avril",
        5 => "Mai",
        6 => "Juin",
        7 => "Juillet",
        8 => "Août",
        9 => "Septembre",
        10 => "Octobre",
        11 => "Novembre",
        12 => "Décembre"
    ];

    // Vérification si le numéro du mois est valide, sinon retourne "null"
    return $months[$nbr_month] ?? "null";
}