<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    self::setFn('Do', function ($Call)
    {
        if (!isset($Call['Bundle']))
            $Call['Bundle'] = isset($Call['Start'])? $Call['Start']: 'Codeine';

        if (!isset($Call['Option']))
            $Call['Option'] = 'Do';


        $Call = F::Run($Call['Bundle'].'.Control', $Call['Option'], $Call, ['Context' => 'app']);

        $Call['Layouts'][] = array(
            'Scope' => $Call['Bundle'],
            'ID' => 'Control',
            'Context' => $Call['Context']
        );

        $Call['Layouts'][] = array(
            'Scope' => $Call['Bundle'],
            'ID' => 'Control/'.$Call['Option'],
            'Context' => $Call['Context']
        );


        foreach($Call['Bundles'] as $Group => $Bundles)
        {
            $Call['Options'][] = $Group;

            if (in_array($Call['Bundle'], $Bundles))
                $Call['Group'] = $Group;

            foreach ($Bundles as $Bundle)
            {
                $Options = ['ID' => $Bundle, 'Group' => $Group];

                if (($BundleOptions = F::Run($Bundle.'.Control', 'Menu', $Call)) !== null)
                    $Options = F::Merge($Options, $BundleOptions);

                $Call['Options'][] = $Options;
            }
        }

        foreach ($Call['Sidebar'] as &$Sidebar)
            $Pills[] = ['ID' => $Sidebar, 'URL' => '/control/'.$Call['Bundle'].'/'.$Sidebar, 'Title' => $Call['Bundle'].'.Control:Options.'.$Sidebar];

        $Call['Output']['Sidebar'][] = array(
                'Type' => 'Navpills',
                'Options' => $Pills,
                'Value' => $Call['Option']
            );

        $Call['Output']['Navigation'][] = array(
            'Type' => 'Navlist',
            'Scope' => 'Control',
            'Options' => $Call['Options'],
            'Value' => $Call['Bundle']
        );

        return $Call;
     });