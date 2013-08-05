<?php

    /* Codeine
     * @author BreathLess
     * @description: Фронт контроллер
     * @package Codeine
     * @version 7.x
     * @date 31.08.11
     * @time 1:12
     */

    setFn('Run', function ($Call)
    {
        // В этом месте, практически всегда, происходит роутинг.
        $Call['Output'] = [];
        $Call['Layouts'] = [];
        $Call['Layout'] = '';

        $Call = F::Hook('beforeApplicationRun', $Call);

        // FIXME
        // Если передан нормальный вызов, совершаем его

        if (F::isCall($Call['Run']))
        {
            list($Call['Service'], $Call['Method']) = array ($Call['Run']['Service'], $Call['Run']['Method']);

            F::Log('*'.$Call['Service'].':'.$Call['Method'].'* started', LOG_INFO);

            $Call = F::Live($Call['Run'], $Call, ['Context' => 'app']);
        }
        // В противном случае, 404
        else
            $Call = F::Hook('on404', $Call);

        // А здесь - рендеринг
        $Call = F::Hook('afterApplicationRun', $Call);

        return $Call;
    });
