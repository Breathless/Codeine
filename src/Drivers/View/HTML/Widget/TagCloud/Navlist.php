<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call)
    {
        $Output = [];

        foreach ($Call['Value'] as $Value => $Count)
            {
                if ($Count >= $Call['Minimal'])
                    $Output[] = F::Run('View', 'Load',
                        [
                            'Scope' => $Call['Entity'],
                            'ID' => 'Catalog/'.$Call['Key'],
                            'Context'   => $Call['Context'],
                            'Data' =>
                                [
                                    'Count' => $Count,
                                    'Value' => $Value
                                ]
                        ]);
            }

        $Call['Value'] = implode(PHP_EOL, $Output);

        return $Call;
    });