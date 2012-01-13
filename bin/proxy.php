<?php

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$kernel = new AppKernel('test', true);
$documentRoot = __DIR__.'/../web';

$socket = socket_create_listen(1234);
while ($client = socket_accept($socket)) {
    $string = '';
    while ($line = socket_read($client, 4096, PHP_NORMAL_READ)) {
        $string .= $line;
        var_dump($line);
        if ($line === "\r") {
            break;
        }
    }

    var_dump($string);exit;
    $request = Request::createFromString($string);

    var_dump($request);
    exit;


    if (file_exists($file = $documentRoot.$request->getRequestUri()) && !is_dir($file))
    {
        echo "File found: ".realpath($file)."\n";
        $response = new Response(file_get_contents($file));
    } elseif ($request->getPathInfo() === '/start-isolation') {
        $response = new Response('OK');
        echo "Start isolation\n";
    } elseif ($request->getPathInfo() === '/stop-isolation') {
        echo "Stop isolation\n";
        $response = new Response('OK');
    } else {
        echo "Request: ".$request->getPathInfo()."\n";
        $kernel->boot();
        $response = $kernel->handle($request);
        $kernel->shutdown();
    }

    socket_write($client, $response->__toString());
    socket_close($client);
}
socket_close($socket);
