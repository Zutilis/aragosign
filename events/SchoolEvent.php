<?php

class SchoolEvent {

    private $hour_start;  // Heure de début de l'événement (ex: 9 pour 9h00)
    private $hour_end;    // Heure de fin de l'événement (ex: 12 pour 12h00)
    private $teacher;     // Enseignant responsable de l'événement
    private $title;       // Titre de l'événement (ex: "Cours de mathématiques")
    private $month;       // Mois de l'événement (ex: 5 pour mai)
    private $year;        // Année de l'événement (ex: 2024)
    private $day;         // Jour de l'événement (ex: 12 pour le 12 du mois)

    /**
     * Constructeur de la classe SchoolEvent.
     * Initialise un nouvel événement scolaire à partir de la date, des heures, du titre et de l'enseignant.
     * 
     * @param string $date Date de l'événement au format 'jj/mm/aaaa'
     * @param int $hour_start Heure de début de l'événement
     * @param int $hour_end Heure de fin de l'événement
     * @param string $title Titre de l'événement
     * @param string $teacher Nom de l'enseignant responsable
     */
    public function __construct($date, $hour_start, $hour_end, $title, $teacher) 
    {
        $this->hour_start = $hour_start;
        $this->hour_end = $hour_end;
        $this->teacher = $teacher;
        $this->title = $title;

        // Décompose la date en jour, mois et année à partir du format 'jj/mm/aaaa'
        $splited_date = explode('/', $date);
        $this->day = intval($splited_date[0]);
        $this->month = intval($splited_date[1]);
        $this->year = intval($splited_date[2]);
    }

    /**
     * Définit l'heure de début de l'événement.
     * 
     * @param int $hour_start L'heure de début à définir
     * @return void
     */
    public function setHourStart($hour_start)
    {
        $this->hour_start = $hour_start;
    }

    /**
     * Définit l'heure de fin de l'événement.
     * 
     * @param int $hour_end L'heure de fin à définir
     * @return void
     */
    public function setHourEnd($hour_end)
    {
        $this->hour_end = $hour_end;
    }

    /**
     * Vérifie si l'événement a lieu le matin (avant 13h).
     * 
     * @return bool Retourne true si l'événement se termine avant 13h, sinon false
     */
    public function isMorning()
    {
        return (intval($this->hour_end) < 13);
    }

    /**
     * Calcule la durée de l'événement en heures.
     * 
     * @return int Durée de l'événement en heures
     */
    public function getDuration()
    {
        return (intval($this->hour_end) - intval($this->hour_start));
    }

    /**
     * Retourne le titre de l'événement.
     * 
     * @return string Titre de l'événement
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Représentation sous forme de chaîne de l'événement.
     * Fournit un résumé des informations de l'événement (titre, enseignant, heure de début et fin).
     * 
     * @return string Représentation de l'événement en HTML
     */
    public function __toString()
    {
        return '<br> Titre : ' . $this->getTitle()
            . '<br> Prof : ' . $this->getTeacher()
            . '<br> Heure de début : ' . $this->getHourStart()
            . '<br> Heure de fin : ' . $this->getHourEnd() . '<br><br>';
    }

    // Getters pour accéder aux propriétés privées
    public function getHourStart()  { return $this->hour_start; }  // Retourne l'heure de début
    public function getHourEnd()    { return $this->hour_end; }    // Retourne l'heure de fin
    public function getTeacher()    { return $this->teacher; }     // Retourne l'enseignant
    public function getMonth()      { return $this->month; }       // Retourne le mois de l'événement
    public function getYear()       { return $this->year; }        // Retourne l'année de l'événement
    public function getDay()        { return $this->day; }         // Retourne le jour de l'événement
}