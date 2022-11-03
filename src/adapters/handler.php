<?php

// You can configure the security level as you required.
// By default is to allow any requests from any domains.

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new \Stimulsoft\StiDataHandler();
$handler->process();