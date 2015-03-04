<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description HTML Textfield Driver 
     * @package Codeine
     * @version 8.x
     */

     setFn('Make', function ($Call)
     {
         $Options = [];

         if (isset($Call['Multiple']) && $Call['Multiple'])
             $Call['Name'] .= '[]';

         if ($Call['Options'] === null)
             ;
         else
         {
             if (isset($Call['Nullable']) && $Call['Nullable'])
                 $Call['Options'][] = [null, null];

             if (isset($Call['Flip Keys']))
                 $Call['Options'] = array_flip($Call['Options']);

             foreach ($Call['Options'] as $Key => $Option)
             {
                 if (is_array($Option))
                 {
                     $Key = array_pop($Option);
                     $Value = array_pop($Option);
                 }
                 else
                     $Value = $Option;

                 if (isset($Call['Localized']) && $Call['Localized'])
                 {
                     if (!isset($Call['Values Locale']))
                         $Call['Values Locale'] = $Call['Entity'].'.Entity:'.$Call['Key'];

                     $lValue = '<l>'.$Call['Values Locale'].'.'.$Value.'</l>';
                 }
                 else
                     $lValue = $Value;

                 if (isset($Call['Keys as values']) && $Call['Keys as values'])
                     $lValue = $Value;

                 if (($Value == $Call['Value']) || ($Key == $Call['Value']) || ((is_array($Call['Value']) && in_array ($Value, $Call['Value']))))
                     $Options[] = '<option value="'.$Key.'" selected>'.$lValue.'</option>';
                 else
                     $Options[] = '<option value="'.$Key.'">' . $lValue . '</option>';
             }
         }

         $Call ['Options'] = implode('', $Options);

         return $Call;
     });