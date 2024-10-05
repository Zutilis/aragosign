<?php

require_once('CalendrierPDFBuilder.php');

class CalendrierPDFGenerator {
    
    private $schoolManager;     // Instance du gestionnaire d'événements
    private $pdfFilePath;        // Chemin du fichier PDF de sortie
    private $calendrier;         // Instance du générateur de calendrier PDF
    private $data;               // Données utilisateur extraites du formulaire

    /**
     * Constructeur : initialise les propriétés et lance l'extraction des données.
     * 
     * @param array $data Données soumises par l'utilisateur (nom, prénom, etc.)
     * @param string $pdfFilePath Chemin du fichier PDF à générer
     * @param string $planningFilePath Chemin du fichier ICS
     */
    public function __construct($data, $schoolManager, $pdfFilePath, )
    {
        $this->schoolManager = $schoolManager;
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
        $allEvents = $this->schoolManager->getSchoolEventsByDate($this->data['month'], $this->data['year']);

        foreach ($allEvents as $eventsPerDay) {
            $this->calendrier->addDailyEvents(
                $eventsPerDay[0]->getCompleteDate(), 
                $this->schoolManager->getMorningSchoolEvents($eventsPerDay), 
                $this->schoolManager->getAfternoonSchoolEvents($eventsPerDay)
            );
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
        // Instancie et prépare le calendrier PDF
        $this->calendrier = new CalendrierPDFBuilder($this->data);
        $this->calendrier->addHeader();  // Ajoute l'entête au PDF (titre, infos utilisateur, etc.)
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