<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.2
     */

    self::setFn('Do', function ($Call)
    {
        // TODO Realize "Do" function

        $IO = F::loadOptions('IO');

        foreach ($IO['Storages'] as $Name => $Storage)
        {
            $Call['Output']['Content'][] =
                array(
                    'Type' => 'Template',
                    'Scope' => 'IO',
                    'Value' => 'Control/Short',
                    'Data' => F::Merge(array('Name' => $Name), $Storage)
                );

            $Call['Output']['Storage_'.$Name][] =
                array(
                    'Type' => 'Table',
                    'Headless' => true,
                    'Value' => $Info
                );
        }

        return $Call;
    });