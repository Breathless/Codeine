<?php

  /* Codeine
     * @author BreathLess
     * @description  MySQL Driver
     * @package Codeine
     * @version 7.x
     */

    setFn ('Open', function ($Call)
    {
        $Link = new mysqli($Call['Server'], $Call['User'], F::Live($Call['Password']));

        if (!$Link->ping())
            return null;

        $Link->select_db ($Call['Database']);
        $Link->set_charset ($Call['Charset']);
     //   $Link->autocommit ($Call['AutoCommit']);

        return $Link;
    });

    setFn('Operation', function ($Call)
    {
        $Call['MySQL Result'] = $Call['Link']->query($Call['Query']);

        if ($Call['Link']->errno != 0)
        {
            F::Log($Call['Query'], LOG_ERR);
            F::Log($Call['Link']->errno.':'.$Call['Link']->error, LOG_ERR);
            $Call = F::Hook('MySQL.Error.'.$Call['Link']->errno, $Call);
        }
        else
        {
            F::Log($Call['Query'], LOG_INFO);
            F::Counter('MySQL');
        }

        return $Call;
    });

    setFn ('Read', function ($Call)
    {
        $Query = F::Run('IO.Storage.MySQL.Syntax', 'Read', $Call);

        $Call = F::Run(null, 'Operation', $Call, ['Query' => $Query]);

        if ($Call['MySQL Result']->num_rows>0)
        {
            $Data = $Call['MySQL Result']->fetch_all(MYSQLI_ASSOC);
            $Call['MySQL Result']->free();
        }
        else
            $Data = null;

        return $Data;
    });

    setFn ('Write', function ($Call)
    {
        $Data = $Call['Data'];

        foreach ($Data as &$Element)
        {
            $Call['Data'] = $Element;

            if (isset($Call['Where']))
            {
                if (isset($Call['Data']))
                    $Query = F::Run('IO.Storage.MySQL.Syntax', 'Update', $Call);
                else
                    $Query = F::Run('IO.Storage.MySQL.Syntax', 'Delete', $Call);
            }
            else
                $Query = F::Run('IO.Storage.MySQL.Syntax', 'Insert', $Call);

            $Call = F::Run(null, 'Operation', $Call, ['Query' => $Query]);

            if (!isset($Call['Data']['ID']))
                $Element['ID'] = $Call['Link']->insert_id;

            if ($Call['Link']->errno != 0)
            {
                F::Log($Call['Link']->error, LOG_ERR);
                return null;
            }
        }

        $Call['Data'] = $Data;

        if (isset($Call['Data']))
            return $Call['Data'];
        else
            return null;



    });

    setFn ('Close', function ($Call)
    {
        return true;
    });

    setFn ('Run', function ($Call)
    {
        $Call = F::Run(null, 'Operation', $Call, ['Query' => $Call['Run']]);

        if ($Call['MySQL Result']->num_rows>0)
            {
                $Data = $Call['MySQL Result']->fetch_all(MYSQLI_ASSOC);
                $Call['MySQL Result']->free();
                F::Log('['.sizeof($Data).'] '.$Call['Run'], LOG_DEBUG);
            }
        else
            $Data = null;

        return $Data;
    });

    setFn ('Status', function ($Call)
    {
        $Data = explode('  ', $Call['Link']->stat());

        foreach ($Data as &$Row)
            $Row = explode(':', $Row);

        return $Data;
    });

    setFn ('Count', function ($Call)
    {
        $Query = F::Run('IO.Storage.MySQL.Syntax', 'Count', $Call);

        $Call = F::Run(null, 'Operation', $Call, ['Query' => $Query]);

        if ($Call['MySQL Result'])
            $Call['MySQL Result'] = $Call['MySQL Result']->fetch_assoc();

        return $Call['MySQL Result']['count(*)'];
    });

    setFn ('ID', function ($Call)
    {
        $Call = F::Run(null, 'Operation', $Call, ['Query' => 'SELECT MAX(id) AS ID FROM '.$Call['Scope']]);

        if ($Call['MySQL Result'])
            $Call['MySQL Result'] = $Call['MySQL Result']->fetch_assoc();

        return $Call['MySQL Result']['ID']+$Call['Increment'];
    });