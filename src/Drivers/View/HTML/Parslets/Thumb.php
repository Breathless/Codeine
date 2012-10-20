<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.0
     */

    self::setFn('Parse', function ($Call)
    {
        foreach ($Call['Parsed'][2] as $Ix => $Match)
        {
            $Thumb = json_decode(json_encode(simplexml_load_string(html_entity_decode('<thumb>'.$Match.'</thumb>'))),true); // FIXME Абстрагировать этот пиздец

            if (isset($Thumb['Default']))
                $Thumb['Default'] = F::Live($Thumb['Default']);
            else
                $Thumb['Default'] = $Call['Default'];

            if (is_string($Thumb['URL']))
            {
                if (preg_match('/^http.*/', $Thumb['URL']))
                {
                    $Filename = $Thumb['URL'];
                }
                else
                {
                    $Filename = Root . '/' . $Thumb['URL'];

                    if (!is_file($Filename) or is_dir($Filename))
                    {
                        if (preg_match('/^http.*/', $Thumb['Default']))
                            $Filename = $Thumb['Default'];
                        else
                            $Filename = F::findFile($Thumb['Default']);
                    } // FIXME Конфиг
                }
            }

            //crop and resize the image

            $ThumbURL = '/cache/thumbs/'.$Thumb['Width'].'_'.sha1($Filename) . '.jpg'; // FIXME Абстрагировать

            if (!F::file_exists(Root.'/Public'.$ThumbURL))
            {
                F::Log('Thumbnail created');

                try
                {
                    if (getimagesize($Filename))
                    {
                        $Blob = file_get_contents($Filename);
                        $Image = new Gmagick();
                        $Image->readimageblob($Blob);
                        $Image->cropThumbnailImage($Thumb['Width'], $Thumb['Width']);
                        $Image->writeImage(Root.'/Public'.$ThumbURL);
                    }
                }
                catch (Exception $e)
                {
// Let it fails
                }
            }

            $Call['Output'] = str_replace($Call['Parsed'][0][$Ix],
                F::Run('View', 'LoadParsed',
                    array(
                         'Scope' => 'Default',
                         'ID' => 'UI/Thumbs/'.(isset($Thumb['Template'])? $Thumb['Template']: 'Normal'),
                         'Data' =>
                            F::Merge($Thumb,
                             array(
                                 'Width' => $Thumb['Width'],
                                 'Height' => $Thumb['Width'],
                                 'URL' => $ThumbURL
                             ))

                    )), $Call['Output']);
        }

        return $Call;
     });