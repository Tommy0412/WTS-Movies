<?php
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

function slugged($res)
{
    $res = preg_replace('/[^0-9]/', '', $res);
    if (empty($res)) {
        return 'n-a';
    }
    return $res;
}

function RemoveSpecialChar($str)
{ 
    $str = str_ireplace( array( '\'', '"', ';' ), ' ', $str);
    if (empty($str)) {
        return 'n-a';
    }
    return $str;
}

function Ratingtwo($str)
{
    $str = number_format((float)$str, 1, '.', '');
    if (empty($str)) {
        return 'N/A';
    }
    return $str;
}
?>