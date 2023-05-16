<?php


function get_random_string($length)
{
    $characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $text = '';

    $length = rand(4, $length);

    for ($i = 0; $i < $length; $i++) {
        $random = rand(0, count($characters) - 1);
        $text .= $characters[$random];
    }

    return $text;
}
?>