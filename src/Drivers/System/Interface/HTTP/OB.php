<?php

    /* Codeine
     * @author BreathLess
     * @description Output Buffering
     * @package Codeine
     * @version 7.x
     */

    setFn('Start', function ($Call)
    {
        ob_start();
        return $Call;
    });

    setFn('Finish', function ($Call)
    {
        if (ob_get_level() > 0)
            ob_end_flush();

        return $Call;
    });