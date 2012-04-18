<?php

    /* Codeine
     * @author BreathLess
     * @description Pagination hooks 
     * @package Codeine
     * @version 7.2
     */

    self::setFn('beforeList', function ($Call)
    {
        if (!isset($Call['Page']))
            $Call['Page'] = 1;

        $Call['Front']['Count'] = F::Run('Entity', 'Count', $Call);
        $Call['Limit']['From']= ($Call['Page']-1)*$Call['EPP'];
        $Call['Limit']['To'] = $Call['EPP'];

        $Call['PageCount'] = ceil($Call['Front']['Count']/$Call['EPP']);

        return $Call;
    });

    self::setFn('afterList', function ($Call)
    {
        $Call['Output']['Content'][] = array(
                'Type'  => 'Paginator',
                'Total' => $Call['Front']['Count'],
                'EPP' => $Call['EPP'],
                'Page' => $Call['Page'],
                'PageURL' => $Call['PageURL'],
                'PageCount' => $Call['PageCount']
            );


        return $Call;
    });