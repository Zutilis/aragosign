<?php

class ICSParser {

    private             $keys = array('BEGIN', 'UID', 'DTSTAMP', 'DESCRIPTION', 
                                    'DTSTART', 'DTEND', 'LOCATION', 'SUMMARY', 'END');

    private             $filename;
    private             $calendar;

    public function     __construct($filename) 
    {
        $this->filename = $filename;
        $this->calendar = file_get_contents($filename);
    }

    private function    btw($str, $i_start, $i_end)
    {
        $key_start = $this->keys[$i_start] . ':';
        $key_end = $this->keys[$i_end] . ':';

        $arr = explode($key_start, $str);
        if (isset($arr[1])) 
        {
            $arr = explode($key_end, $arr[1]);
            return (trim($arr[0]));
        }
        return '';
    }

    public function     getValue($event, $key)
    {
        $i_start = array_search(strtoupper($key), $this->keys);

        if ($i_start < 0)
            return (null);

        $i_end = $i_start + 1 >= count($this->keys) ? 0 : $i_start + 1;

        return $this->btw($event, $i_start, $i_end);
    }

    public function     getEvents()
    {
        return (explode('BEGIN:VEVENT', $this->calendar));
    }

    public function     getFilename()       { return ($this->filename); }
    public function     getCalendar()       { return ($this->filename); }

}