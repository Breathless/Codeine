<?php

    /* Codeine
     * @author BreathLess
     * @description Minifier 
     * @package Codeine
     * @version 7.x
     */

    self::setFn ('Process', function ($Call)
    {
//        $Call['Output'] = preg_replace ('/^\\s+|\\s+$/m', '', $Call['Output']);
        return $Call;
    });