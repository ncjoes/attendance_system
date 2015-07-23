<?php

require_once 'constants.php';
require_once 'Utility.php';
require_once 'User.php';

//ini_set("date.timezone","Africa/Lagos");
date_default_timezone_set('Africa/Lagos');

///global variables
    //Check for post request
    $array = filter_input_array(INPUT_POST);
    if ($array !== FALSE && $array !== null) {
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                foreach ($array[$key] as $subkey => $subvalue) {
					//$subvalue[$subkey] = html_entity_decode($subvalue[$subkey]);
                    $array[$key][$subkey] = html_entity_decode($array[$key][$subkey]);
                }
            } else {
                $array[$key] = html_entity_decode($array[$key]);
            }
        }
        //Further processing is done in the page to which the request was directed to
    }
