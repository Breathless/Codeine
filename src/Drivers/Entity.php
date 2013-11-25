<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4
     */

    setFn('Load', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return null;
        }

        $Call = F::Hook('beforeEntityLoad', $Call);

            $Model = F::loadOptions($Call['Entity'].'.Entity');

            if (!empty($Model))
            {
                if (!isset($Model['EV']))
                    $Model['EV'] = 1;

                $Call = F::Merge($Model, $Call);
            }
            else
                F::Log('Model for '.$Call['Entity'].' not found', LOG_CRIT);

        $Call = F::Hook('afterEntityLoad', $Call);

        $Call['entity'] = strtolower($Call['Entity']); // Hm.

        return $Call;
    });

    setFn('Create', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return $Call;
        }

        if (!isset($Call['Data'][0]))
            $Call['Data'] = [$Call['Data']];

        $Call = F::Hook('beforeOperation', $Call);

            foreach ($Call['Data'] as $IX => $New)
            {
                $Call = F::Hook('beforeEntityWrite', $Call);

                    $Call = F::Hook('beforeEntityCreate', $Call);

                    if (!isset($Call['Failure']) or !$Call['Failure']) // FIXME Shit
                    {
                        $Data[$IX] = F::Run('IO', 'Write', $Call, ['Data!' => $New]);
                        $Call = F::Hook('afterEntityCreate', $Call);
                    }
                    else
                        $Data[$IX] = null;

                $Call = F::Hook('afterEntityWrite', $Call);
            }

        $Call = F::Hook('afterOperation', $Call);
        if (isset($Call['One']))
            $Data = array_shift($Data);

        return $Data;
    });

    setFn('Read', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return null;
        }

        $Call = F::Hook('beforeOperation', $Call);

            $Call = F::Hook('beforeEntityRead', $Call);

            $Call['Data'] = F::Run('IO', 'Read', $Call);

            if ($Call['Data'] !== null)
            {
                $Data = $Call['Data'];

                $Call['Data'] = null;

                foreach ($Data as &$cData)
                {
                    $Call['Data'] = $cData;
                    $Call = F::Hook('afterEntityRead', $Call);
                    $cData = $Call['Data'];
                }

                $Call['Data'] = $Data;
            }

        $Call = F::Hook('afterOperation', $Call);

        F::Log('*'.count($Call['Data']).'* '.$Call['Entity'].' readed', LOG_INFO);

        if (isset($Call['One']) && $Call['One'] && is_array($Call['Data']))
            return array_shift($Call['Data']);
        else
            return $Call['Data'];
    });

    setFn('Update', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return null;
        }

        $Call = F::Hook('beforeOperation', $Call);

        $Current = F::Run('Entity', 'Read', $Call, ['One' => false]);

        $Updates = $Call['Data'];

        if ($Current)
        {
            foreach ($Current as $IX => $Call['Current'])
            {
                if (isset($Updates[$IX]))
                    $Call['Data'] = F::Merge($Call['Current'], $Updates[$IX]);
                else
                    $Call['Data'] = F::Merge($Call['Current'], $Updates);

                if (isset($Call['Current']['ID']))
                {
                    $Call['Where'] = ['ID' => $Call['Current']['ID']];

                    $Call = F::Hook('beforeEntityWrite', $Call);

                        $Call = F::Hook('beforeEntityUpdate', $Call);

                            F::Run('IO', 'Write', $Call);

                        $Call = F::Hook('afterEntityUpdate', $Call);

                    $Call = F::Hook('afterEntityWrite', $Call);

                    $Current[$IX] = $Call['Data'];
                }
            }

            $Current = F::Run('Entity', 'Read', $Call, ['ReRead' => true, 'One' => false]);


            $Call = F::Hook('afterOperation', $Call);

            F::Log('*'.count($Call['Data']).'* '.$Call['Entity'].' updated', LOG_INFO);

            if (isset($Call['One']))
            {
                unset($Call['One']);

                if (is_array($Current))
                    $Current = array_shift($Current);
            }
        }

        return $Current;
    });

    setFn('Delete', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return null;
        }

        $Call = F::Hook('beforeOperation', $Call);

        $Current = F::Run('Entity', 'Read', $Call, ['One' => false]);

        if ($Current)
        {
            foreach ($Current as $IX => $Call['Current'])
            {
                $Call['Data'] = $Call['Current'];

                if (isset($Call['Current']['ID']))
                {
                    $Call['Where'] = ['ID' => $Call['Current']['ID']];

                    $Call = F::Hook('beforeEntityWrite', $Call);

                        $Call = F::Hook('beforeEntityDelete', $Call);

                    unset($Call['Data']);

                            F::Run('IO', 'Write', $Call);

                        $Call = F::Hook('afterEntityDelete', $Call);

                    $Call = F::Hook('afterEntityWrite', $Call);
                }
            }

            $Call = F::Hook('afterOperation', $Call);

            F::Log('*'.count($Current).'* '.$Call['Entity'].' removed', LOG_INFO);
        }

        return $Current;
    });

    setFn('Count', function ($Call)
    {
        if (!isset($Call['Entity']))
        {
            F::Log('Entity not defined.', LOG_ERR);
            return null;
        }

        $Call = F::Hook('beforeOperation', $Call);
            $Call = F::Hook('beforeEntityCount', $Call);

            $Call['Data'] = F::Run('IO', 'Execute', $Call,
            [
                  'Execute' => 'Count',
                  'Scope'   => $Call['Entity']
            ]);

            $Call = F::Hook('afterEntityCount', $Call);
        $Call = F::Hook('afterOperation', $Call);

        F::Log('*'.$Call['Data'].'* '.$Call['Entity'].' counted.', LOG_INFO);

        return $Call['Data'];
    });

    setFn('Far', function ($Call)
    {
        $Element = F::Run(null, 'Read', $Call, ['One' => true]);
        return isset($Element[$Call['Key']])? $Element[$Call['Key']]: null;
    });