<?php

require_once('fpdf/fpdf.php');

class CalendrierPDFBuilder {
    
    const W_SIGNATURE = 20; // Largeur de la colonne Signature
    const W_FORMATEUR = 20; // Largeur de la colonne Formateur
    const W_MATIERE = 35;   // Largeur de la colonne Matière
    const W_HEURE = 10;     // Largeur de la colonne Heures
    const W_DATE = 15;      // Largeur de la colonne Date

    // Largeur totale d'une période (matin ou après-midi)
    const W_TYPE_HEURE = self::W_SIGNATURE + self::W_FORMATEUR + self::W_MATIERE + self::W_HEURE;

    // Largeur totale du tableau
    const W_MAX = self::W_TYPE_HEURE * 2 + self::W_DATE;

    // Largeur du formulaire utilisateur dans l'entête
    const W_HEADER_FORM = self::W_MAX / 2;

    const H_HEADER = 10;           // Hauteur pour l'entête du document
    const H_USER_INFO_HEADER = 8;  // Hauteur pour les informations utilisateur
    const H_TABLE_HEADER = 18;     // Hauteur pour l'entête du tableau
    const H_DAILY_EVENT = 18;      // Hauteur pour les événements journaliers

    private $left_margin;
    private $data;
    private $pdf;

    /**
     * Constructeur qui initialise les données du calendrier et prépare le PDF.
     * @param array $data Données pour remplir le calendrier
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->_init(); // Initialisation de la page PDF
    }

    /**
     * Ajoute l'entête du calendrier (titre, informations utilisateur, logo).
     * @return void
     */
    public function addHeader()
    {
        // Positionnement à gauche
        $this->pdf->SetX($this->left_margin);

        // Titre du document
        $this->_setFontSize(10);
        $this->pdf->Cell(self::W_MAX, self::H_HEADER, 'ATTESTATION DE PRESENCE DU MOIS DE '
            . strtoupper(monthToFrench($this->data['month'])) . ' 2024', 0, 0, 'C');
        $this->_ln();

        // Ajout du logo
        $this->pdf->Image('assets/arago.jpeg', $this->left_margin, 10, 20, 20, 'JPEG');

        // Informations utilisateur (nom, prénom, entreprise, classe)
        $this->_setFontSize(7);
        $this->_addUserInfoRow('Nom', $this->data['nom'], 'Entreprise', $this->data['entreprise']);
        $this->_addUserInfoRow('Prénom', $this->data['prenom'], 'Classe', $this->data['classe']);
        $this->_ln();

        // Ajout de l'entête du tableau des événements
        $this->_addTableHeader();
    }

    /**
     * Ajoute les événements journaliers (matin et après-midi).
     * @param string $date Date de l'événement
     * @param array $morning_events Liste des événements du matin
     * @param array $afternoon_events Liste des événements de l'après-midi
     * @return void
     */
    public function addDailyEvents($date, $morning_events, $afternoon_events)
    {
        $this->_setFontSize(7);
        $this->pdf->Cell(self::W_DATE, self::H_DAILY_EVENT, $date, 1, 0, 'C');

        // Calcul du nombre de lignes nécessaires pour afficher les événements
        $row_count = max(count($morning_events), count($afternoon_events));
        $h_event = self::H_DAILY_EVENT / $row_count;

        // Affichage des lignes d'événements
        for ($i = 0; $i < $row_count; $i++) {
            $this->_addEventRow($morning_events[$i] ?? null, $afternoon_events[$i] ?? null, $h_event);

            // Si ce n'est pas la dernière ligne, on ajoute une cellule vide pour l'alignement
            if ($i + 1 < $row_count)
                $this->pdf->Cell(self::W_DATE, $h_event, '', 0, 0, 'C');
        }
    }

    /**
     * Génère et télécharge le fichier PDF.
     * @param string $filename Nom du fichier PDF généré
     * @return void
     */
    public function output($filename)
    {
        ob_end_clean(); // Nettoyage du buffer de sortie pour éviter les erreurs
        $this->pdf->Output('D', $filename, true); // Téléchargement du PDF
    }

    /**
     * Initialise les paramètres de la page PDF.
     * @return void
     */
    private function _init()
    {
        // Création de l'instance PDF
        $this->pdf = new FPDF('P', 'mm', 'A4');
        $this->pdf->SetDisplayMode('fullpage', 'single');
        $this->pdf->SetTitle('Planning');
        $this->pdf->SetMargins($this->left_margin, 5);
        $this->pdf->AddPage();

        // Calcul de la marge gauche pour centrer le tableau
        $this->left_margin = ($this->pdf->GetPageWidth() - self::W_MAX) / 2;
    }

    /**
     * Ajoute une ligne avec deux informations (ex: Nom, Prénom, etc.).
     * @param string $label1 Premier libellé
     * @param string $value1 Première valeur
     * @param string $label2 Deuxième libellé
     * @param string $value2 Deuxième valeur
     * @return void
     */
    private function _addUserInfoRow($label1, $value1, $label2, $value2)
    {
        $this->pdf->Cell(self::W_HEADER_FORM, self::H_USER_INFO_HEADER, "$label1: $value1", 0, 0, 'C');
        $this->pdf->Cell(self::W_HEADER_FORM, self::H_USER_INFO_HEADER, "$label2: $value2", 0, 0, 'C');
        $this->_ln();
    }

    /**
     * Ajoute l'entête du tableau (les colonnes : DATES, MATIN, APRES-MIDI).
     * @return void
     */
    private function _addTableHeader()
    {
        $this->pdf->Cell(self::W_DATE, self::H_TABLE_HEADER, 'DATES', 1, 0, 'C');
        $this->_addTimePeriodHeader('MATIN');
        $this->_addTimePeriodHeader('APRES-MIDI');
        $this->_ln();

        // Sous-entêtes pour les heures, matière, formateur, signature
        $this->pdf->Cell(self::W_DATE, self::H_TABLE_HEADER * 2, '', 0, 0, 'C');
        for ($i = 0; $i < 2; $i++)
            $this->_addEventSubHeaders();

        $this->_ln();
    }

    /**
     * Ajoute une sous-entête pour une période (matin ou après-midi).
     * @param string $label Nom de la période
     * @return void
     */
    private function _addTimePeriodHeader($label)
    {
        $this->pdf->Cell(self::W_TYPE_HEURE, self::H_TABLE_HEADER / 2, $label, 1, 0, 'C');
    }

    /**
     * Ajoute les sous-colonnes pour les événements (Heures, Matière, Formateur, Signature).
     * @return void
     */
    private function _addEventSubHeaders()
    {
        $this->pdf->Cell(self::W_HEURE, self::H_TABLE_HEADER / 2, 'Heures', 1, 0, 'C');
        $this->pdf->Cell(self::W_MATIERE, self::H_TABLE_HEADER / 2, 'Matière', 1, 0, 'C');
        $this->pdf->Cell(self::W_FORMATEUR, self::H_TABLE_HEADER / 2, 'Formateur', 1, 0, 'C');
        $this->pdf->Cell(self::W_SIGNATURE, self::H_TABLE_HEADER / 2, 'Signature', 1, 0, 'C');
    }

    /**
     * Ajoute une ligne pour un événement (matin et après-midi).
     * @param object|null $morning_event Événement du matin
     * @param object|null $afternoon_event Événement de l'après-midi
     * @param float $h_event Hauteur de la cellule de l'événement
     * @return void
     */
    private function _addEventRow($morning_event, $afternoon_event, $h_event)
    {
        $this->_addHourEvent($morning_event, $h_event);
        $this->_addHourEvent($afternoon_event, $h_event);
        $this->_ln();
    }

    /**
     * Ajoute une cellule pour un événement spécifique (matin ou après-midi).
     * @param object|null $event L'événement à ajouter
     * @param float $h_event Hauteur de la cellule
     * @return void
     */
    private function _addHourEvent($event, $h_event)
    {
        $is_null = $event == null;

        $this->_setFontSize(6);
        $this->_hourEventCell(self::W_HEURE, $h_event, $is_null ? '' : strval($event->getDuration()), $is_null, 5);
        $this->_hourEventCell(self::W_MATIERE, $h_event, $is_null ? '' : $event->getTitle(), $is_null, 25);
        $this->_hourEventCell(self::W_FORMATEUR, $h_event, $is_null ? '' : $event->getTeacher(), $is_null, 15);
        $this->_hourEventCell(self::W_SIGNATURE, $h_event, ' ', false);
    }

    /**
     * Ajoute une cellule dans le tableau des événements.
     * @param float $w_cell Largeur de la cellule
     * @param float $h_cell Hauteur de la cellule
     * @param string $txt Texte à afficher dans la cellule
     * @param bool $fill Si la cellule doit être remplie ou non
     * @return void
     */
    private function _hourEventCell($w_cell, $h_cell, $txt, $fill = false, $truncate=25)
    {
        $this->pdf->Cell($w_cell, $h_cell, truncate($txt, $truncate), 1, 0, 'C', $fill, '');
    }

    /**
     * Ajoute une nouvelle ligne (saut de ligne dans le PDF).
     * @param int $repeat Nombre de lignes à ajouter (par défaut : 1)
     * @return void
     */
    private function _ln($repeat = 1)
    {
        for ($i = 0; $i < $repeat; $i++)
            $this->pdf->Ln();
        $this->pdf->SetX($this->left_margin);
    }

    /**
     * Définit la taille de la police pour le PDF.
     * @param int $font_size Taille de la police
     * @return void
     */
    private function _setFontSize($font_size)
    {
        $this->pdf->SetFont('Arial', '', $font_size);
    }
}