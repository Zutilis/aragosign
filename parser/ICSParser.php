<?php

class ICSParser {

    private $filename;

    public function __construct($filename) 
    {
        $this->filename = $filename;
    }

    /**
     * Fonction principale pour lire et traiter les événements/lignes directement.
     * 
     * @param callable $callback Fonction de traitement à appeler pour chaque événement
     */
    public function parseLines(callable $callback)
    {
        $currentEvent = '';
        $insideEvent = false;

        // Ouvre le fichier en lecture
        $handle = fopen($this->filename, "r");

        if ($handle) {
            $previousLine = ''; // Pour stocker la ligne précédente lors du dépliage
            // Parcourt le fichier ligne par ligne
            while (($line = fgets($handle)) !== false) {
                // Si la ligne actuelle commence par un espace, elle fait partie de la ligne précédente (pliée)
                if (preg_match('/^\s/', $line)) {
                    // On enlève l'espace ou la tabulation du début et on concatène avec la ligne précédente
                    $previousLine .= trim($line);
                    continue;
                }

                // Si la ligne précédente contient des informations, on la traite
                if (!empty($previousLine)) {
                    $this->processLine($previousLine, $insideEvent, $currentEvent, $callback);
                }

                // Mise à jour de la ligne précédente
                $previousLine = trim($line);
            }

            // Traite la dernière ligne si elle contient quelque chose
            if (!empty($previousLine)) {
                $this->processLine($previousLine, $insideEvent, $currentEvent, $callback);
            }

            fclose($handle); // Ferme le fichier après traitement
        } else {
            throw new Exception("Impossible d'ouvrir le fichier ICS.");
        }
    }

    /**
     * Traite une ligne lue, met à jour l'événement actuel ou appelle le callback.
     *
     * @param string $line La ligne à traiter
     * @param bool &$insideEvent Référence pour savoir si on est dans un événement
     * @param string &$currentEvent Référence de l'événement en cours de construction
     * @param callable $callback Fonction à appeler lorsque l'événement est complet
     */
    private function processLine($line, &$insideEvent, &$currentEvent, callable $callback)
    {
        // Détection du début d'un événement
        if (strpos($line, 'BEGIN:VEVENT') !== false) {
            $insideEvent = true;
            $currentEvent = $line . "\n"; // Commence un nouvel événement
        } 
        // Détection de la fin de l'événement
        elseif (strpos($line, 'END:VEVENT') !== false) {
            $currentEvent .= $line . "\n"; // Ajoute la ligne de fin
            $insideEvent = false;

            // Appel de la fonction de callback pour traiter l'événement
            $callback($currentEvent);

            // Réinitialisation pour le prochain événement
            $currentEvent = '';
        } 
        // Pendant que nous sommes dans un événement, on accumule les lignes
        elseif ($insideEvent) {
            $currentEvent .= $line . "\n";
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