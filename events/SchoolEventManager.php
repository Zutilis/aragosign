<?php
require_once('parser/ICSParser.php');
require_once('SchoolEvent.php');

class SchoolEventManager {
    
    private $events;   // Tableau associatif des événements par date
    private $filename; // Nom du fichier ICS à parser
    private $parser;   // Instance de l'analyseur ICS pour extraire les événements

    /**
     * Constructeur de la classe SchoolEventManager.
     * Initialise le gestionnaire d'événements avec un fichier ICS et prépare le parser.
     * 
     * @param string $filename Chemin vers le fichier ICS à analyser
     */
    public function __construct($filename)
    {
        $this->events = [];  // Initialisation d'un tableau vide pour stocker les événements
        $this->filename = $filename;

        // Création d'une instance du parser ICS avec le fichier fourni
        $this->parser = new ICSParser($this->filename);
    }

    /**
     * Charge et crée tous les événements à partir du fichier ICS en analysant ses données.
     * 
     * @return void
     */
    public function loadAll()
    {
        // Récupère tous les événements présents dans le fichier ICS
        $events = $this->parser->getEvents();

        // Boucle pour traiter chaque événement (à partir du deuxième élément, index 1)
        for ($i = 1; $i < count($events); $i++) {
            $dstart = $this->parser->getValue($events[$i], 'DTSTART'); // Date et heure de début
            $dend = $this->parser->getValue($events[$i], 'DTEND');     // Date et heure de fin

            // Extraction des informations du champ 'SUMMARY' pour obtenir le titre et l'enseignant
            $summary = explode(' - ', $this->parser->getValue($events[$i], 'SUMMARY'));
            $title = remove($summary[0], '*', '\\');  // Supprime les caractères indésirables du titre
            $teacher = count($summary) > 1 ? $summary[1] : 'Inconnu';  // Récupère l'enseignant ou 'Inconnu'

            // Création de l'événement scolaire
            $this->create(
                $this->toDate($dstart),      // Conversion du champ 'DTSTART' en date
                $this->toHour($dstart, 1),   // Conversion de l'heure de début
                $this->toHour($dend, 1),     // Conversion de l'heure de fin
                $title,                      // Titre de l'événement
                $teacher                     // Enseignant
            );
        }
    }

    /**
     * Crée un événement scolaire et l'ajoute à la date correspondante.
     * 
     * @param string $date Date de l'événement au format jj/mm/aaaa
     * @param int $hour_start Heure de début de l'événement
     * @param int $hour_end Heure de fin de l'événement
     * @param string $title Titre de l'événement
     * @param string $teacher Enseignant responsable de l'événement
     * @return SchoolEvent L'événement scolaire créé
     */
    public function create($date, $hour_start, $hour_end, $title, $teacher)
    {
        // Si aucun événement n'existe pour cette date, initialise un tableau vide
        if (!array_key_exists($date, $this->events)) {
            $this->events[$date] = [];
        }

        // Ajoute l'événement à la date donnée et retourne l'événement ajouté
        return $this->add(
            $date,
            new SchoolEvent($date, $hour_start, $hour_end, $title, $teacher)
        );
    }

    /**
     * Ajoute un événement à la date spécifiée.
     * 
     * @param string $date Date de l'événement au format jj/mm/aaaa
     * @param SchoolEvent $school_event Instance de SchoolEvent à ajouter
     * @return SchoolEvent Retourne l'événement ajouté
     */
    public function add($date, SchoolEvent $school_event)
    {
        array_push($this->events[$date], $school_event);  // Ajoute l'événement au tableau des événements pour la date
        return $school_event;
    }

    /**
     * Récupère tous les événements scolaires organisés par date.
     * 
     * @return array Tableau associatif des événements avec les dates comme clés
     */
    public function getSchoolEvents()
    {
        return $this->events;
    }

    /**
     * Convertit une chaîne de caractères contenant une date et une heure en une heure (en ajoutant une heure si nécessaire).
     * 
     * @param string $str Chaîne contenant la date et l'heure
     * @param int $add_hour Heure à ajouter à l'heure extraite (par défaut : 0)
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
     * @return string Date convertie au format jj/mm/aaaa
     */
    private function toDate($str)
    {
        // Reforme la chaîne de date en changeant l'ordre des éléments
        return substr($str, 6, 2) . '/'
            . substr($str, 4, 2) . '/'
            . substr($str, 0, 4);
    }
}