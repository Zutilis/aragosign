<?php
function toDate($str)
{
    return (substr($str, 6, 2) . '/'
            . substr($str, 4, 2) . '/'
            . substr($str, 0, 4));
}

function toHour($str, $add_hour=0)
{
    return (intval(substr($str, 9, 2) + $add_hour));
}

function remove($str, ...$str_to_remove)
{
    foreach($str_to_remove as $tmp)
        $str = str_replace($tmp, '', $str);

    return ($str);
}