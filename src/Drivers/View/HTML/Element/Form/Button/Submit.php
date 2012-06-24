<?php

    /* Codeine
     * @author BreathLess
     * @description HTML Textfield Driver 
     * @package Codeine
     * @version 7.4.5
     */

     self::setFn('Make', function ($Call)
     {
         return F::Run ('View', 'LoadParsed', $Call,
                        array(
                             'Scope' => 'UI',
                             'ID'    => 'HTML/Form/Button/Submit',
                             'Data'  => $Call
                        ));
     });