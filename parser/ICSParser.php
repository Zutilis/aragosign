<?php

class ICSParser {

    private $keys = array('BEGIN', 'UID', 'DTSTAMP', 'DESCRIPTION', 
                          'DTSTART', 'DTEND', 'LOCATION', 'SUMMARY', 'END');

    private $filename;

    public function __construct($filename) 
    {
        $this->filename = $filename;
    }

    /**
     * Fonction principale pour lire et traiter les événements directement.
     * 
     * @param callable $callback Fonction de traitement à appeler pour chaque événement
     */
    public function parseEvents(callable $callback)
    {
        $currentEvent = '';
        $insideEvent = false;

        // Ouvre le fichier en lecture
        $handle = fopen($this->filename, "r");

        if ($handle) {
            // Parcourt le fichier ligne par ligne
            while (($line = fgets($handle)) !== false) {
                $trimmedLine = trim($line);

                // Détection du début d'un événement
                if (strpos($trimmedLine, 'BEGIN:VEVENT') !== false) {
                    $insideEvent = true;
                    $currentEvent = $trimmedLine . "\n"; // Commence un nouvel événement
                } 
                // Détection de la fin de l'événement
                elseif (strpos($trimmedLine, 'END:VEVENT') !== false) {
                    $currentEvent .= $trimmedLine . "\n"; // Ajoute la ligne de fin
                    $insideEvent = false;

                    // Appel de la fonction de callback pour traiter l'événement
                    $callback($currentEvent);

                    // Réinitialisation pour le prochain événement
                    $currentEvent = '';
                } 
                // Pendant que nous sommes dans un événement, on accumule les lignes
                elseif ($insideEvent) {
                    $currentEvent .= $trimmedLine . "\n";
                }
            }

            fclose($handle); // Ferme le fichier après traitement
        } else {
            throw new Exception("Impossible d'ouvrir le fichier ICS.");
        }
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
        $pattern = '/^' . preg_quote($key, '/') . ':(.*)$/m'; // Pattern pour trouver la clé
        if (preg_match($pattern, $event, $matches)) {
            return trim($matches[1]); // Retourne la valeur associée à la clé
        }

        return null;
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
}