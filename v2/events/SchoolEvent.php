<?php
class SchoolEvent {

    const max_chars = 40;

    private             $hour_start;
    private             $hour_end;
    private             $teacher;
    private             $title;
    private             $month;
    private             $year;
    private             $day;

    public function     __construct($date, $hour_start, $hour_end,
            $title, $teacher) 
    {
        $this->hour_start = $hour_start;
        $this->hour_end = $hour_end;
        $this->teacher = $teacher;
        $this->title = $title;

        $splited_date = explode('/', $date);
        $this->day = intval($splited_date[0]);
        $this->month = intval($splited_date[1]);
        $this->year = intval($splited_date[2]);
    }

    public function     setHourStart($hour_start)
    {
        $this->hour_start = $hour_start;
    }

    public function     setHourEnd($hour_end)
    {
        $this->hour_end = $hour_end;
    }

    public function     isMorning()
    {
        return (intval($this->hour_end) < 13);
    }

    public function     getDuration()
    {
        return (intval($this->hour_end) - intval($this->hour_start));
    }

    public function     getTitle()
    {
        return ($this->title);
    }

    public function     __toString()
    {
        return '<br> Titre : ' . $this->getTitle()
            . '<br> Prof : ' . $this->getTeacher()
            . '<br> Date de début : ' . $this->getHourStart()
            . '<br> Date de fin : ' . $this->getHourStart() . '<br><br>';
    }

    public function     getHourStart()      { return ($this->hour_start); }
    public function     getHourEnd()        { return ($this->hour_end); }
    public function     getTeacher()        { return ($this->teacher); }
    public function     getMonth()          { return ($this->month); }
    public function     getYear()           { return ($this->year); }
    public function     getDay()            { return ($this->day); }
} 