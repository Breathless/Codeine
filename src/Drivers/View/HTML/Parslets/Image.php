<?php

    /* Codeine
     * @author BreathLess
     * @description Image Parslet
     * @package Codeine
     * @version 7.x
     */

    setFn('Parse', function ($Call)
    {
        foreach ($Call['Parsed'][2] as $Ix => $Match)
        {
            $Match = json_decode(
                json_encode(
                    simplexml_load_string('<image>'.$Match.'</image>')), true); // I love PHP :(

              list($Asset, $ID) =
                  F::Run('View', 'Asset.Route', ['Value' => $Match['Source']]);

              if (isset($Match['Storage']))
                  $Call['Image']['Storage'] = $Match['Storage'];

              if (isset($Match['Scope']))
                  $Call['Image']['Scope'] = $Match['Scope'];
              else
                  $Call['Image']['Scope'] = [strtr($Asset, '.', '/'), 'img'];

              $Image = F::Run('IO', 'Execute',
                                           [
                                               'Execute' => 'Version',
                                               'Storage' => $Call['Image']['Storage'],
                                               'Scope'   => $Call['Image']['Scope'],
                                               'Where'   => $ID
                                           ]).'_'.strtr($Asset.'.'.$ID, '/','.');

              $Path = $Call['Image']['Pathname'].$Image;

              if (isset($Call['Image']['Host']) && !empty($Call['Image']['Host']))
                  $Host = $Call['Image']['Host'];
              else
                  $Host = $Call['RHost'];

              if (F::Run ('IO', 'Execute',
                            [
                                'Execute' => 'Exist',
                                'Storage' => 'Image Cache',
                                'Scope'   => [$Host, 'img'],
                                'Where'   => $Image
                            ]))
              {
                  F::Log('Image '.$Image.' cached', LOG_GOOD);
              }
              else
              {
                  F::Log('Image '.$Image.' missed', LOG_BAD);

                  $ImageData = F::Run('IO', 'Read',
                                           [
                                               'Storage' => $Call['Image']['Storage'],
                                               'Scope'   => $Call['Image']['Scope'],
                                               'Where'   => $ID
                                           ])[0];

                  if ($ImageData != null)
                  {
                      if (isset($Match['Width']) && isset($Match['Thumb']))
                      {
                            $GImage = new Gmagick();
                            $GImage->readimageblob($ImageData);
                            $GImage->setCompressionQuality(100);

                            if (!isset($Match['Height']))
                               $Match['Height'] =
                                   ($Match['Width']/$GImage->getimagewidth())*$GImage->getimageheight();

                            $GImage->resizeimage($Match['Width'], $Match['Height'], null, 1);

                            $ImageData = $GImage->getImageBlob();
                      }

                      F::Log('Thumbnail created', LOG_INFO);

                      if (F::Run ('IO', 'Write',
                              [
                                  'Storage' => 'Image Cache',
                                  'Scope'   => [$Host, 'img'],
                                  'Where'   => $Image,
                                  'Data' => [$ImageData]
                              ]
                      ))
                          F::Log('Image '.$Image.' writed', LOG_GOOD);
                      else
                          F::Log('Image '.$Image.' not writed', LOG_BAD);;
                  }
                  else
                  {
                      if (isset($Match['Default']))
                          list($Asset, $ID) = F::Run('View', 'Asset.Route', ['Value' => $Match['Default']]);
                      else
                      {
                          $Asset = 'Default';
                          $ID = 'default.png';
                      }

                      if (F::Run ('IO', 'Write',
                          [
                              'Storage' => 'Image Cache',
                              'Scope'   => [$Call['RHost'], 'img'],
                              'Where'   => $Image,
                              'Data' => F::Run('IO', 'Read',
                                  [
                                      'Storage' => 'Image',
                                      'One' => true,
                                      'Scope'   => [$Asset, 'img'],
                                      'Where'   => $ID
                                  ])
                          ]
                      ))
                          F::Log('Default image '.$Image.' writed', LOG_GOOD);
                      else
                          F::Log('Default image '.$Image.' not writed', LOG_BAD);
                  }
              }

              if (isset($Call['Image']['Host']))
                 $Path = $Call['Image']['Proto'].$Call['Image']['Host'].$Path;

                $HTML = '<img src="'.$Path.'" '
                  .(isset($Match['Class']) ? ' class="'.$Match['Class'].'"': '')
                  .(isset($Match['Width']) ? ' width="'.$Match['Width'].'"': '')
                  .(isset($Match['Height']) ? ' height="'.$Match['Height'].'"': '')
                  .(isset($Match['Alt']) ? ' alt="'.$Match['Alt'].'"': '')
                  .' />';


              $Call['Output'] = str_replace($Call['Parsed'][0][$Ix], $HTML, $Call['Output']);
            }


      return $Call;
    });