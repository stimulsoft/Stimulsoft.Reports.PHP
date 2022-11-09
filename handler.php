<?php

use Stimulsoft\Events\StiDataEventArgs;
use Stimulsoft\Events\StiExportEventArgs;
use Stimulsoft\Events\StiReportEventArgs;
use Stimulsoft\Events\StiVariablesEventArgs;
use Stimulsoft\StiEventArgs;
use Stimulsoft\StiHandler;
use Stimulsoft\StiResult;

require_once 'vendor/autoload.php';

// You can configure the security level as you required.
// By default is to allow any requests from any domains.

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');


$handler = new StiHandler();


/** @var $args StiVariablesEventArgs */
$handler->onPrepareVariables = function ($args)
{
    // You can change the values of the variables used in the report.
    // The new values will be passed to the report generator.
    /*
    $args->variables['VariableString']->value = 'Value from Server-Side';
    $args->variables['VariableDateTime']->value = '2020-01-31 22:00:00';

    $args->variables['VariableStringRange']->value->from = 'Aaa';
    $args->variables['VariableStringRange']->value->to = 'Zzz';

    $args->variables['VariableStringList']->value[0] = 'Test';
    $args->variables['VariableStringList']->value = ['1', '2', '2'];

    $args->variables['NewVariable'] = ['value' => 'New Value'];
    */

    return StiResult::success();
};

/** @var $args StiDataEventArgs */
$handler->onBeginProcessData = function ($args)
{
    // You can change the connection string
    /*
    if ($args->connection == 'MyConnectionName')
        $args->connectionString = 'Server=localhost;Database=test;uid=root;password=******;';
    */

    // You can change the SQL query
    /*
    if ($args->dataSource == 'MyDataSource')
        $args->queryString = 'SELECT * FROM MyTable';
    */


    // You can change the SQL query parameters with the required values
    // For example: SELECT * FROM @Parameter1 WHERE Id = @Parameter2 AND Date > @Parameter3
    /*
    if ($args->dataSource == 'MyDataSourceWithParams') {
        $args->parameters['Parameter1']->value = 'TableName';
        $args->parameters['Parameter2']->value = 10;
        $args->parameters['Parameter3']->value = '2019-01-20';
    }
    */

    // You can send a successful result
    return StiResult::success();
    // You can send an informational message
    //return StiResult::success('Warning or other useful information.');
    // You can send an error message
    //return StiResult::error('A message about some connection error.');
};

/** @var $args StiDataEventArgs */
$handler->onEndProcessData = function ($args)
{
    return StiResult::success();
};

/** @var $args StiReportEventArgs */
$handler->onPrintReport = function ($args)
{
    return StiResult::success();
};

/** @var $args StiExportEventArgs */
$handler->onBeginExportReport = function ($args)
{
    return StiResult::success();
};

/** @var $args StiExportEventArgs */
$handler->onEndExportReport = function ($args)
{
    // By default, the exported file is saved to the 'reports' folder.
    // You can change this behavior if required.
    file_put_contents('reports/' . $args->fileName . '.' . $args->fileExtension, base64_decode($args->data));

    //return StiResult::success();
    return StiResult::success('Successful export of the report.');
    //return StiResult::error('An error occurred while exporting the report.');
};

/** @var $args StiExportEventArgs */
$handler->onEmailReport = function ($args)
{
    // These parameters will be used when sending the report by email. You must set the correct values.
    $args->emailSettings->from = '*****@gmail.com';
    $args->emailSettings->host = 'smtp.gmail.com';
    $args->emailSettings->login = '*****';
    $args->emailSettings->password = '*****';

    // These parameters are optional.
    //$args->emailSettings->name = 'John Smith';
    //$args->emailSettings->port = 465;
    //$args->emailSettings->cc[] = 'copy1@gmail.com';
    //$args->emailSettings->bcc[] = 'copy2@gmail.com';
    //$args->emailSettings->bcc[] = 'copy3@gmail.com John Smith';

    return StiResult::success('Email sent successfully.');
};

/** @var $args StiReportEventArgs */
$handler->onCreateReport = function ($args)
{
    // You can load a new report and send it to the designer.
    //$args->report = file_get_contents('reports/SimpleList.mrt');

    return StiResult::success();
};

/** @var $args StiReportEventArgs */
$handler->onSaveReport = function ($args)
{
    // For example, you can save a report to the 'reports' folder on the server-side.
    file_put_contents('reports/' . $args->fileName . '.mrt', $args->reportJson);

    //return StiResult::success();
    return StiResult::success('Save Report OK: ' . $args->fileName);
    //return StiResult::error('Save Report ERROR. Message from server side.');
};

/** @var $args StiReportEventArgs */
$handler->onSaveAsReport = function ($args)
{
    // The event works the same as 'onSaveReport'
    return StiResult::success();
};

// Process request
$handler->process();
