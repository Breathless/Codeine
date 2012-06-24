<?php

    /* Codeine
     * @author BreathLess
     * @description: Simple HTML Renderer
     * @package Codeine
     * @version 7.4.5
     */

    self::setFn('Render', function ($Call)
    {
        $XML = new XMLWriter();
        $XML->openMemory();
        $XML->startDocument('1.0', 'UTF-8');
        $XML->setIndent(true);

        $XML->startElement($Call['Output']['Root']);

        if ($Call['Namespace'])
        {
            $XML->startAttribute('xmlns');
                $XML->text($Call['Namespace']);
            $XML->endAttribute();
        }

        if ($Call['Attributes'])
            foreach ($Call['Attributes'] as $Namespace)
            {
                $XML->startAttributeNs($Namespace['Prefix'], $Namespace['Key'], null);
                    $XML->text($Namespace['Value']);
                $XML->endAttribute();
            }

        F::Map($Call['Output']['Content'],
           function ($Key, $Value) use ($XML)
           {
               if (is_numeric($Key))
               {
                   if ($Key > 0) // FIXME Костыль!
                       $XML->endElement();
               }
               else
                   $XML->startElement($Key);

               if(!is_array($Value))
               {
                   $XML->text($Value);
                   $XML->endElement();
               }

           }
       );

        $XML->endElement();
        $XML->endDocument();

        $Call['Output'] = $XML->outputMemory(true);
        
        return $Call;
    });
