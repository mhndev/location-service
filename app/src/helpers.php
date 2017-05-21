<?php

function getUri()
{
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === false ? 'http://' : 'https://';
    return $protocol . $_SERVER['HTTP_HOST'];
}
