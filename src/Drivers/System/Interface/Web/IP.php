<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4.5
     */

    self::setFn('Get', function ($Call)
    {
        return isset($Call['Substitute'][$_SERVER['REMOTE_ADDR']])? $Call['Substitute'][$_SERVER['REMOTE_ADDR']]: $_SERVER['REMOTE_ADDR'];
    });