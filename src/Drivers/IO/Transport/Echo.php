<?php

    /* Codeine
     * @author BreathLess
     * @description: 
     * @package Codeine
     * @version 7.4.5
     * @date 27.28.11
     * @time 6:28
     */

    self::setFn ('Open', function ($Call)
        {
            return true;
        });

    self::setFn ('Send', function ($Call)
        {
            echo '<div><pre>'.$Call['Message'];
            var_dump($Call['Call']);
            echo '</pre></div>';

            return true;
        });
