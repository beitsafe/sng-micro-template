<?php

if (!function_exists('is_json')) {
    function is_json($string)
    {
        if (!function_exists('json_decode')) {
            return false;
        }

        json_decode($string);

        return JSON_ERROR_NONE === json_last_error();
    }
}
