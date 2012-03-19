<?php

    /* Codeine
     * @author BreathLess
     * @description: 
     * @package Codeine
     * @version 7.2
     * @date 31.08.11
     * @time 6:17
     */

    self::setFn('Route', function ($Call)
    {
        if (strpos($Call['Run'], '?'))
            list($Call['Run']) = explode('?', $Call['Run']);

        // TODO Error: Not found Regex Table

        foreach ($Call['Regex'] as $Rule)
            if (preg_match($Rule['Match'], $Call['Run'], $Matches))
            {
                foreach ($Rule['Call'] as $Key => $Value)
                    if (is_scalar($Value) && isset($Matches[$Value]))
                        $Rule['Call'][$Key] = $Matches[$Value];

                F::Run('IO', 'Write', array('Storage' => 'Developer', 'Level' => 'Info', 'Data' => 'Regex Router Match: '.$Rule['Match']));

                return $Rule;
            }

        return null;
    });
