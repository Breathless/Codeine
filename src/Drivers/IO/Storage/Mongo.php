<?php

   /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn ('Open', function ($Call)
    {
        $Link = new MongoClient('mongodb://'.$Call['Server']);
        $Link = $Link->selectDB($Call['Database']);

        if (isset($Call['Auth']))
            $Link->authenticate($Call['Auth']['Username'], $Call['Auth']['Password']);

        return $Link;
    });

    setFn ('Read', function ($Call)
    {
        $Call['Scope'] = strtr($Call['Scope'], '.', '_');
        $Data = null;

        if (isset($Call['Where']) and $Call['Where'] !== null)
        {
            foreach ($Call['Where'] as $Key => $Value)
                if (is_array($Value))
                    foreach ($Value as $Subkey => $Subvalue)
                        $Where[$Key.'.'.$Subkey] = $Subvalue;
                else
                    $Where[$Key] = $Value;

            F::Log('Mongo: db.'.$Call['Scope'].'.find('.json_encode($Where).')', LOG_INFO);
            $Cursor = $Call['Link']->$Call['Scope']->find($Where);
        }
        else
        {
            F::Log('Mongo: db.'.$Call['Scope'].'.find()', LOG_INFO);
            $Cursor = $Call['Link']->$Call['Scope']->find();
        }

        $Data = null;

        if ($Cursor !== null)
        {
            if (isset($Call['Fields']))
            {
                $Fields = array();

                foreach ($Call['Fields'] as $Field)
                    $Fields[$Field] = true;

                $Cursor->fields($Fields);
            }

            if (isset($Call['Sort']))
                foreach($Call['Sort'] as $Key => $Direction)
                    $Cursor->sort(array($Key => (int)(($Direction == SORT_ASC) or ($Direction == 1))? 1: -1));

            if (isset($Call['Limit']))
                $Cursor->limit($Call['Limit']['To']-$Call['Limit']['From'])->skip($Call['Limit']['From']);

            if ($Cursor->count()>0)
                foreach ($Cursor as $cCursor)
                {
                    unset($cCursor['_id']);
                    $Data[] = $cCursor;
                }
        }

        return $Data;
    });

    setFn ('Write', function ($Call)
    {
        $Call['Scope'] = strtr($Call['Scope'], '.', '_');

        if (null === $Call['Data'] or empty($Call['Data']))
        {
            if (isset($Call['Where']))
            {
                F::Log('Mongo: db.'.$Call['Entity'].'remove('.json_encode($Call['Where']).')', LOG_INFO);
                return $Call['Link']->$Call['Scope']->remove ($Call['Where']);
            }
        }
        else
        {
            try
            {
                if (isset($Call['Where']))
                {
                    F::Log('Mongo: db.'.$Call['Entity'].'.update('.json_encode($Call['Where'].',',json_encode(['$set' => $Call['Data']])).')', LOG_INFO);
                    $Call['Link']->$Call['Scope']->update($Call['Where'], ['$set' => $Call['Data']]);
                }
                else
                {
                    F::Log('Mongo: db.'.$Call['Entity'].'.insert('.json_encode($Call['Data']).')', LOG_INFO);
                    $Call['Link']->$Call['Scope']->insert ($Call['Data']);
                }
            }
            catch (MongoCursorException $e)
            {
                return F::Hook('IO.Mongo.Update.Failed', $Call);
            }

            return $Call['Data'];
        }
    });

    setFn ('Close', function ($Call)
    {
        return true;
    });

    setFn ('Execute', function ($Call)
    {
        return $Call['Link']->execute($Call['Command']);
    });

    setFn ('Count', function ($Call)
    {
        $Call['Scope'] = strtr($Call['Scope'], '.', '_');


        if (isset($Call['Where']))
        {
            foreach ($Call['Where'] as $Key => $Value)
                if (is_array($Value))
                    foreach ($Value as $Subkey => $Subvalue)
                        $Where[$Key.'.'.$Subkey] = $Subvalue;
                else
                    $Where[$Key] = $Value;

            $Cursor = $Call['Link']->$Call['Scope']->find($Where);
        }
        else
            $Cursor = $Call['Link']->$Call['Scope']->find();

        if ($Cursor)
            return $Cursor->count();
        else
            return null;
    });

    setFn ('ID', function ($Call)
    {
        $Call['Scope'] = strtr($Call['Scope'], '.', '_');

        if (isset($Call['Where']))
            $Cursor = $Call['Link']->$Call['Scope']->find($Call['Where']);
        else
            $Cursor = $Call['Link']->$Call['Scope']->find();

        return $Cursor->count()+1;
    });

