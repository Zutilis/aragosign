<?php
require_once('SchoolEventManager.php');
require_once('calendrier/Calendrier.php');

class EventProcessor {
    
    private $planningFile;
    private $month;
    private $year;

    public function __construct($planningFile, $month, $year) 
    {
        $this->planningFile = $planningFile;
        $this->month = $month;
        $this->year = $year;
    }

    public function process() 
    {
        $manager = new SchoolEventManager($this->planningFile);
        $manager->loadAll();

        $calendrier = new Calendrier();
        $calendrier->addHeader();

        foreach ($manager->getSchoolEvents() as $date => $events) 
        {
            $morning_events = [];
            $afternoon_events = [];

            foreach ($events as $event) 
            {
                if ($event->getMonth() != $this->month || $event->getYear() != $this->year)
                    continue;

                for ($i = 0; $i < ceil($event->getDuration() / 2); $i++) {
                    $tmp = clone $event;
                    $tmp->setHourStart(intval($event->getHourStart()) + $i * 2);
                    $tmp->setHourEnd(intval($event->getHourStart()) + $i * 2 + 2);

                    if ($tmp->getHourEnd() > 18) {
                        $tmp->setHourEnd(18);
                    }

                    if ($tmp->isMorning()) {
                        array_push($morning_events, $tmp);
                    } else {
                        array_push($afternoon_events, $tmp);
                    }
                }
            }

            if (count($morning_events) > 0 || count($afternoon_events) > 0) {
                $calendrier->addDailyEvents($date, $morning_events, $afternoon_events);
            }
        }

        return ($calendrier);
    }
}