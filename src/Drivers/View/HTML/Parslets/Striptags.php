<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Exec Parslet 
     * @package Codeine
     * @version 6.0
     */

     setFn('Parse', function ($Call)
     {
         $Replaces = [];
         
         foreach ($Call['Parsed']['Value'] as $IX => $Match)
             $Replaces[$Call['Parsed']['Match'][$IX]] = strip_tags($Match);

        return $Replaces;
     });