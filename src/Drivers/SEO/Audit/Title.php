<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Length', function ($Call)
    {
        $Length = mb_strlen($Call['View']['HTML']['Title']);

        if ($Length > $Call['SEO']['Audit']['Title']['Length']['Maximum'])
        {
            $Call = F::Hook('SEO.Audit.Title.TooLong', $Call);
            F::Log('SEO Title is too long *'.$Length.' chars* over *'
                .$Call['SEO']['Audit']['Title']['Length']['Maximum'].'*', LOG_WARNING, 'Marketing');
        }
        elseif ($Length < $Call['SEO']['Audit']['Title']['Length']['Minimum'])
        {
            $Call = F::Hook('SEO.Audit.Title.TooShort', $Call);
            F::Log('SEO Title is too short *'.$Length.' chars* over *'
                .$Call['SEO']['Audit']['Title']['Length']['Minimum'].'*', LOG_WARNING, 'Marketing');
        }
        else
        {
            F::Log('SEO Title length is optimal ', LOG_GOOD, 'Marketing');
        }

        return $Call;
    });