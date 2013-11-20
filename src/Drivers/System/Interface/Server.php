<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Do', function ($Call)
    {
        F::Log('Interface: *Server*', LOG_INFO);

        ini_set('implicit_flush', true);

        $Server = stream_socket_server("tcp://".$Call['Host'].':'.$Call['Port'], $ErrorNo, $ErrorMessage);

        if ($Server)
        {
            F::Log('Server created', LOG_GOOD);

            while(true)
            {
                $Client = stream_socket_accept($Server, -1);

                if ($Client)
                {
                    $Request = fread($Client, 8192);

                    if (preg_match('@(GET|POST|UPDATE|DELETE|PUT)(.*)HTTP/(.*)@', $Request, $Pockets))
                    {
                        list(, $Call['HTTP Method'], $Call['URI'],  $Call['HTTP Version']) = $Pockets;

                        $Call['Run'] = rawurldecode($Call['URI']);

                        $Call['URL'] = parse_url($Call['Run'], PHP_URL_PATH);
                        $Call['URL Query'] = parse_url($Call['Run'], PHP_URL_QUERY);

                        if (empty($Call['URL Query']))
                            F::Log('Empty query string', LOG_INFO);
                        else
                            F::Log('Query string: *'.$Call['URL Query'].'*', LOG_INFO);

                        F::Log('Run String: '.$Call['Run'], LOG_INFO);

                        if (preg_match('@Host: (.*)@', $Request, $Pockets))
                            $Call['Host'] = trim($Pockets[1]);

                        $Call = F::Apply(null, 'Proto', $Call);

                        $Call = F::Run('Code.Flow.Front', 'Run', $Call);

                        F::Log('Request accepted', LOG_INFO);

                        $Headers = 'HTTP/1.0 '.$Call['Headers']['HTTP/1.0'].PHP_EOL;
                        unset($Call['Headers']['HTTP/1.0']);

                        if (isset($Call['Headers']))
                            foreach ($Call['Headers'] as $Key => $Value)
                                $Headers.= $Key . ' ' . $Value.PHP_EOL;

                        $Call['Output'] = $Headers.PHP_EOL.time();

                        fwrite($Client, $Call['Output']);
                    }

                    fclose($Client);
                }
            }
        }
        else
            F::Log('Server creating error', LOG_ERR);

        return $Call;
    });

    setFn('Protocol', function ($Call)
    {
        if (isset($Call['Project']['Hosts'][F::Environment()]))
        {
            if (preg_match('/(\S+)\.'.$Call['Project']['Hosts'][F::Environment()].'/', $_SERVER['HTTP_HOST'], $Subdomains)
            && isset($Call['Subdomains'][$Subdomains[1]]))
            {
                $Call = F::Merge($Call, $Call['Subdomains'][$Subdomains[1]]);
                F::Log('Active Subdomain detected: '.$Subdomains[1], LOG_INFO);
            }

            $Call['Host'] = $Call['Project']['Hosts'][F::Environment()];
        }

        F::Log('Protocol is *'.$Call['Proto'].'*', LOG_INFO);
        F::Log('RHost is *'.$Call['RHost'].'*', LOG_INFO);
        F::Log('Host is *'.$Call['Host'].'*', LOG_INFO);

        $Call = F::loadOptions($Call['RHost'], null, $Call);

        return $Call;
    });