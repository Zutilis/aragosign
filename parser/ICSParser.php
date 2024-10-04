<?php

class ICSParser {

    // Tableau des clés d'événements courantes dans un fichier ICS
    private $keys = array('BEGIN', 'UID', 'DTSTAMP', 'DESCRIPTION', 
                          'DTSTART', 'DTEND', 'LOCATION', 'SUMMARY', 'END');

    // Propriétés privées
    private $filename;  // Chemin du fichier ICS
    private $calendar;  // Contenu du fichier ICS

    /**
     * Constructeur de la classe ICSParser.
     * Lit le contenu du fichier ICS et initialise la propriété correspondante.
     * 
     * @param string $filename Chemin vers le fichier ICS
     */
    public function __construct($filename) 
    {
        $this->filename = $filename;

        // Récupère le contenu du fichier ICS
        $this->calendar = file_get_contents($filename);
    }

    /**
     * Extrait la valeur entre deux clés spécifiques dans un événement ICS.
     * 
     * @param string $str Chaîne représentant un événement ICS
     * @param int $i_start Index de la clé de début dans $keys
     * @param int $i_end Index de la clé de fin dans $keys
     * @return string Valeur extraite entre les deux clés, ou une chaîne vide si non trouvée
     */
    private function btw($str, $i_start, $i_end)
    {
        $key_start = $this->keys[$i_start] . ':';  // Marqueur de début
        $key_end = $this->keys[$i_end] . ':';      // Marqueur de fin

        // Divise la chaîne à partir du marqueur de début
        $arr = explode($key_start, $str);
        if (isset($arr[1])) {
            // Divise à partir du marqueur de fin pour extraire la valeur
            $arr = explode($key_end, $arr[1]);
            return trim($arr[0]);  // Supprime les espaces superflus et retourne la valeur
        }

        // Retourne une chaîne vide si le marqueur n'est pas trouvé
        return '';
    }

    /**
     * Récupère la valeur associée à une clé donnée dans un événement ICS.
     * 
     * @param string $event Chaîne représentant un événement ICS
     * @param string $key Clé dont on veut extraire la valeur (ex: 'DTSTART')
     * @return string|null La valeur associée à la clé, ou null si non trouvée
     */
    public function getValue($event, $key)
    {
        // Cherche l'index de la clé dans le tableau $keys
        $i_start = array_search(strtoupper($key), $this->keys);

        // Si la clé n'est pas trouvée, retourne null
        if ($i_start === false) {
            return null;
        }

        // Détermine l'index de la clé suivante, ou 0 si c'est la dernière clé
        $i_end = $i_start + 1 >= count($this->keys) ? 0 : $i_start + 1;

        // Retourne la valeur extraite entre les deux clés
        return $this->btw($event, $i_start, $i_end);
    }

    /**
     * Extrait tous les événements à partir du fichier ICS.
     * 
     * @return array Tableau de chaînes représentant chaque événement ICS
     */
    public function getEvents()
    {
        // Sépare les événements en utilisant 'BEGIN:VEVENT' comme délimiteur
        return explode('BEGIN:VEVENT', $this->calendar);
    }

    /**
     * Retourne le chemin du fichier ICS.
     * 
     * @return string Chemin du fichier ICS
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Retourne le contenu du fichier ICS.
     * 
     * @return string Contenu du fichier ICS
     */
    public function getCalendar()
    {
        return $this->calendar;
    }
}