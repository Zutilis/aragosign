<?php
require_once('utils/Utils.php');
require_once('parser/ICSParser.php');
require_once('SchoolEvent.php');

class SchoolEventManager
{

    private $events;   // Tableau associatif des événements par date
    private $parser;   // Instance de l'analyseur ICS pour extraire les événements

    /**
     * Filtre et retourne les événements qui se déroulent le matin.
     * 
     * Cette fonction prend en entrée un tableau d'événements et retourne uniquement ceux qui se déroulent le matin.
     * Elle utilise la méthode `isMorning()` de la classe `SchoolEvent` pour filtrer les événements.
     * 
     * @param array $events Un tableau d'instances de `SchoolEvent`
     * 
     * @return array Un tableau réindexé contenant uniquement les événements du matin
     */
    public static function getMorningSchoolEvents($events)
    {
        // Filtrage des événements qui se déroulent le matin, puis réindexation
        return array_values(array_filter($events, function ($event) {
            return $event->isMorning();  // Filtre les événements matinaux
        }));
    }

    /**
     * Filtre et retourne les événements qui se déroulent l'après-midi.
     * 
     * Cette fonction prend en entrée un tableau d'événements et retourne uniquement ceux qui se déroulent l'après-midi.
     * Elle utilise la méthode `isMorning()` de la classe `SchoolEvent` pour filtrer les événements qui ne se déroulent pas le matin (donc l'après-midi).
     * 
     * @param array $events Un tableau d'instances de `SchoolEvent`
     * 
     * @return array Un tableau réindexé contenant uniquement les événements de l'après-midi
     */
    public static function getAfternoonSchoolEvents($events)
    {
        // Filtrage des événements qui se déroulent l'après-midi, puis réindexation
        return array_values(array_filter($events, function ($event) {
            return !$event->isMorning();  // Filtre les événements de l'après-midi
        }));
    }

    /**
     * Constructeur de la classe SchoolEventManager.
     * Initialise le gestionnaire d'événements avec un fichier ICS et prépare le parser.
     * 
     * @param string $filename Chemin vers le fichier ICS à analyser
     * 
     * @return void
     */
    public function __construct($filename)
    {
        $this->events = [];  // Initialisation d'un tableau vide pour stocker les événements

        // Création d'une instance du parser ICS avec le fichier fourni
        $this->parser = new ICSParser($filename);
    }

    /**
     * Charge et crée tous les événements à partir du fichier ICS en analysant ses données.
     * 
     * Cette fonction analyse le fichier ICS ligne par ligne, extrait les informations de chaque événement (titre, enseignant, 
     * heure de début et de fin), et crée plusieurs événements si l'événement occupe plusieurs demi-journées.
     * Chaque événement est ensuite ajouté au tableau d'événements.
     * 
     * @return void
     */
    public function loadAll()
    {
        $id = 0;
        $this->parser->parseLines(function ($line) use (&$id) {
            $i = 1;
            // echo $id;
            // Extraction des informations du champ 'SUMMARY' pour obtenir le titre et l'enseignant
            $summary = explode(' - ', $this->parser->getValue($line, 'SUMMARY'));
            $title = remove($summary[0], '*', '\\');  // Supprime les caractères indésirables du titre
            $teacher = count($summary) > 1 ? $summary[1] : '';  // Récupère l'enseignant ou 'Inconnu'

            $dstart = $this->parser->getValue($line, 'DTSTART'); // Date et heure de début
            $dend = $this->parser->getValue($line, 'DTEND');     // Date et heure de fin

            $hourStart = $this->toHour($dstart);
            $hourEnd = $this->toHour($dend);

            // Calcul du nombre de demi-journées nécessaires en fonction de la durée de l'événement
            $halfDays = ceil(($hourEnd - $hourStart) / 2);

            // On créer un événement en ajustant les heures pour chaque demi-journée
            for ($j = 0; $j < $halfDays; $j++) {

                $newHourStart = $hourStart + $j * 2; // Ajuste l'heure de début
                $newHourEnd = min($newHourStart + 2, 18); // Ajuste l'heure de fin, ne dépasse pas 18h

                // Création de l'événement scolaire
                $this->_createEvent(
                    $id,
                    $this->toDate($dstart),     // Conversion du champ 'DTSTART' en date
                    $newHourStart,              // Heure de début de l'événement
                    $newHourEnd,                // Heur de fin de l'événement
                    $title,                     // Titre de l'événement
                    $teacher                    // Enseignant
                );
                $id++;
            }
            $i++;
        });
    }

    /**
     * Crée un événement scolaire et l'ajoute à la date correspondante.
     * 
     * Cette fonction est responsable de créer une instance de `SchoolEvent` avec les informations fournies (ID, date, heures, 
     * titre, enseignant) et de l'ajouter à la liste des événements pour la date correspondante.
     * 
     * @param int $id ID unique de l'événement
     * @param string $date Date de l'événement au format jj/mm/aaaa
     * @param int $hour_start Heure de début de l'événement
     * @param int $hour_end Heure de fin de l'événement
     * @param string $title Titre de l'événement
     * @param string $teacher Enseignant responsable de l'événement
     * 
     * @return SchoolEvent L'événement scolaire créé
     */
    private function _createEvent($id, $date, $hour_start, $hour_end, $title, $teacher)
    {
        // Si aucun événement n'existe pour cette date, initialise un tableau vide
        if (!array_key_exists($date, $this->events)) {
            $this->events[$date] = [];
        }

        // Ajoute l'événement à la date donnée et retourne l'événement ajouté
        return $this->_addToArray(
            $date,
            new SchoolEvent($id, $date, $hour_start, $hour_end, $title, $teacher)
        );
    }

    /**
     * Ajoute un événement à la date spécifiée.
     * 
     * @param string $date Date de l'événement au format jj/mm/aaaa
     * @param SchoolEvent $school_event Instance de SchoolEvent à ajouter
     * 
     * @return SchoolEvent Retourne l'événement ajouté
     */
    private function _addToArray($date, SchoolEvent $school_event)
    {
        array_push($this->events[$date], $school_event);  // Ajoute l'événement au tableau des événements pour la date
        return $school_event;
    }

    /**
     * Convertit une chaîne de caractères contenant une date et une heure en une heure (en ajoutant une heure si nécessaire).
     * 
     * @param string $str Chaîne contenant la date et l'heure
     * @param int $add_hour Heure à ajouter à l'heure extraite (par défaut : 0)
     * 
     * @return int Heure extraite avec l'ajout éventuel d'une heure
     */
    private function toHour($str, $add_hour = 0)
    {
        // Récupère les deux caractères représentant l'heure et ajoute éventuellement une heure
        return intval(substr($str, 9, 2)) + $add_hour;
    }

    /**
     * Convertit une chaîne de caractères de date en format jj/mm/aaaa.
     * 
     * @param string $str Chaîne contenant la date au format aaaa/mm/jj
     * 
     * @return string Date convertie au format jj/mm/aaaa
     */
    private function toDate($str)
    {
        // Reforme la chaîne de date en changeant l'ordre des éléments
        return substr($str, 6, 2) . '/'
            . substr($str, 4, 2) . '/'
            . substr($str, 0, 4);
    }

    /**
     * Retourne les événements scolaires pour un mois et une année donnés.
     * 
     * Cette fonction filtre les événements par mois et par année, puis retourne un tableau des événements
     * se déroulant durant cette période spécifique.
     * 
     * @param int $year L'année des événements à récupérer
     * @param int $month Le mois des événements à récupérer
     * 
     * @return array Un tableau associatif où chaque clé est une date (jj/mm/aaaa) et la valeur est une liste d'événements pour cette date
     */
    public function getSchoolEventsByDate($year, $month)
    {
        $ret = [];
        foreach ($this->events as $date => $events) {
            foreach ($events as $event) {
                // Vérifie si l'événement appartient au mois et à l'année spécifiés
                if ($event->getMonth() == $month && $event->getYear() == $year) {
                    $ret[$date][] = $event;
                }
            }
        }

        return $ret;
    }

    /**
     * Retourne tous les événements scolaires.|
     * 
     * @return array Le tableau associatif des événements scolaires classés par date
     */
    public function getSchoolEvents()
    {
        return $this->events;
    }
}
