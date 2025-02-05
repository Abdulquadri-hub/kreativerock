<?php

/**
 * Sanitize inputs
 *
 * @param string $string
 * @return string
 */
function escape($string)
{
    return stripslashes(htmlentities($string, ENT_QUOTES));
};
