<?php
require_once ('parser/ICSParser.php');
require_once ('SchoolEvent.php');

class SchoolEventManager {

    private                 $events;
    private                 $filename;
    private                 $parser;

    public function         __construct($filename) {
        $this->events = [];
        $this->filename = $filename;
        
        $this->parser = new ICSParser($this->filename);
    }

    public function         loadAll()
    {
        $events = $this->parser->getEvents();
        
        for ($i = 1; $i < count($events); $i++) 
        {
            $dstart = $this->parser->getValue($events[$i], 'DTSTART');
            $dend = $this->parser->getValue($events[$i], 'DTEND'); 
        
            $summary = explode(' - ', $this->parser->getValue($events[$i], 'SUMMARY'));
            $title = remove($summary[0], '*', '\\');
            $teacher = count($summary) > 1 ? $summary[1] : 'Inconnu';
            
            $this->create(toDate($dstart), toHour($dstart, 1), toHour($dend, 1), $title, $teacher);
        }
    }

    public function         create($date, $hour_start, $hour_end, 
            $title, $teacher)
    {   
        if (!array_key_exists($date, $this->events))
            $this->events[$date] = array();
        
        return ($this->add(
            $date, new SchoolEvent($date, $hour_start, $hour_end, $title, $teacher)
        ));
    }

    public function         add($date, SchoolEvent $school_event)
    {
        array_push($this->events[$date], $school_event);
        return ($school_event);
    }

    public function         getSchoolEvents()        { return ($this->events); }
}