<?php

require_once('CalendrierPDFBuilder.php');

class CalendrierPDFGenerator {
    
    private $planningFilePath;   // Chemin du fichier de planning (fichier .ics)
    private $school_manager;     // Instance du gestionnaire d'événements
    private $pdfFilePath;        // Chemin du fichier PDF de sortie
    private $calendrier;         // Instance du générateur de calendrier PDF
    private $data;               // Données utilisateur extraites du formulaire

    /**
     * Constructeur : initialise les propriétés et lance l'extraction des données.
     * 
     * @param string $planningFilePath Chemin du fichier ICS
     * @param string $pdfFilePath Chemin du fichier PDF à générer
     * @param array $data Données soumises par l'utilisateur (nom, prénom, etc.)
     */
    public function __construct($planningFilePath, $pdfFilePath, $data)
    {
        $this->planningFilePath = $planningFilePath;
        $this->pdfFilePath = $pdfFilePath;

        // Extraction des données du formulaire POST
        $this->_extractPost($data);
        
        // Initialisation du gestionnaire d'événements et du calendrier PDF
        $this->_init();
    }

    /**
     * Génère et télécharge le PDF en traitant tous les événements du planning.
     * 
     * @return void
     */
    public function generate()
    {
        // Parcourt chaque événement récupéré par le gestionnaire
        foreach ($this->school_manager->getSchoolEvents() as $date => $events) {
            $this->_processDailyEvents($events, $date);  // Traite les événements journaliers
        }

        // Génère et télécharge le fichier PDF à l'emplacement spécifié
        $this->calendrier->output($this->pdfFilePath);
    }

    /**
     * Initialisation : crée le gestionnaire d'événements et génère l'entête du PDF.
     * 
     * @return void
     */
    private function _init()
    {
        // Instancie le gestionnaire d'événements en charge de charger le fichier de planning
        $this->school_manager = new SchoolEventManager($this->planningFilePath);
        $this->school_manager->loadAll();  // Charge tous les événements du planning

        // Instancie et prépare le calendrier PDF
        $this->calendrier = new Calendrier($this->data);
        $this->calendrier->addHeader();  // Ajoute l'entête au PDF (titre, infos utilisateur, etc.)
    }

    /**
     * Traite les événements d'une journée et les ajoute au PDF.
     * 
     * @param array $events Liste des événements de la journée
     * @param string $date Date des événements
     * @return void
     */
    private function _processDailyEvents($events, $date)
    {
        // Sépare les événements entre matin et après-midi
        $morningEvents = [];
        $afternoonEvents = [];

        // Pour chaque événement, on vérifie s'il appartient au mois et à l'année sélectionnés
        foreach ($events as $event) {
            if ($event->getMonth() == $this->data['month'] && $event->getYear() == $this->data['year']) {
                $this->_splitEventByTime($event, $morningEvents, $afternoonEvents);  // Répartit les événements par heure
            }
        }

        // Ajoute les événements de cette journée au calendrier PDF
        if (!empty($morningEvents) || !empty($afternoonEvents)) {
            $this->calendrier->addDailyEvents($date, $morningEvents, $afternoonEvents);
        }
    }

    /**
     * Répartit les événements en fonction de l'heure (matin ou après-midi).
     * 
     * @param object $event L'événement à traiter
     * @param array &$morningEvents Référence à la liste des événements du matin
     * @param array &$afternoonEvents Référence à la liste des événements de l'après-midi
     * @return void
     */
    private function _splitEventByTime($event, &$morningEvents, &$afternoonEvents)
    {
        // Calcul du nombre de demi-journées nécessaires en fonction de la durée de l'événement
        $halfDays = ceil($event->getDuration() / 2);

        // On clone l'événement et ajuste les heures pour chaque demi-journée
        for ($i = 0; $i < $halfDays; $i++) {
            $tmp = clone $event;  // Clonage de l'événement pour le modifier sans affecter l'original
            $tmp->setHourStart(intval($tmp->getHourStart()) + $i * 2);  // Ajuste l'heure de début
            $tmp->setHourEnd(min(intval($tmp->getHourStart()) + 2, 18));  // Ajuste l'heure de fin, ne dépasse pas 18h

            // Classe l'événement dans la liste du matin ou de l'après-midi
            if ($event->isMorning()) {
                $morningEvents[] = $tmp;
            } else {
                $afternoonEvents[] = $tmp;
            }
        }
    }

    /**
     * Extrait les informations du formulaire POST et les formate pour être utilisées dans le PDF.
     * 
     * @param array $data Données du formulaire POST
     * @return void
     */
    private function _extractPost($data)
    {
        // Sépare l'année et le mois à partir du champ 'month_planning'
        $month_planning = explode('-', $data['month_planning']);

        // Remplit les informations utilisateur dans la propriété $this->data
        $this->data = [
            'year' => intval($month_planning[0]),   // Année
            'month' => intval($month_planning[1]),  // Mois
            'prenom' => $data['prenom'],            // Prénom de l'utilisateur
            'nom' => $data['nom'],                  // Nom de l'utilisateur
            'entreprise' => $data['entreprise'],    // Entreprise de l'utilisateur
            'classe' => $data['classe'],            // Classe de l'utilisateur
        ];
    }
}