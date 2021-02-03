<?php
require_once 'stimulsoft/helper.php';

// You can configure the security level as you required.
// By default is to allow any requests from any domains.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");
header('Cache-Control: no-cache');


$handler = new StiHandler();
$handler->registerErrorHandlers();


$handler->onPrepareVariables = function ($args) {
	//$args->variables['VarString']->value = 'Value from Server-Side';
	//$args->variables['VariableDateTime']->value = '2020-01-31 22:00:00';
	
	//$args->variables['VariableStringRange']->value->from = 'Aaa';
	//$args->variables['VariableStringRange']->value->to = 'Zzz';
	
	//$args->variables['VariableStringList']->value = ["1", "2", "2"];
	//$args->variables['VariableStringList2']->value[0] = "Test";
	
	//$args->variables['NewVar'] = ['value' => 'New Value'];
	
	return StiResult::success();
};

$handler->onBeginProcessData = function ($args) {
	
	// Current database type: 'XML', 'JSON', 'MySQL', 'MS SQL', 'PostgreSQL', 'Firebird', 'Oracle'
	$database = $args->database;
	// Current connection name
	$connection = $args->connection;
	// Current data source name
	$dataSource = $args->dataSource;
	// Connection string for the current data source
	$connectionString = $args->connectionString;
	// SQL query string for the current data source
	$queryString = $args->queryString;
	
	
	// You can change the connection string
	/*
	if ($connection == "MyConnectionName")
		$args->connectionString = "Server=localhost;Database=test;Port=3306;";
	*/
	
	// You can change the SQL query
	/*
	if ($dataSource == "MyDataSource")
		$args->queryString = "SELECT * FROM MyTable";
	*/
	
	
	// You can replace the SQL query parameters with the required values
	// For example: SELECT * FROM @Parameter1 WHERE Id=@Parameter2
	// WARNING: If the datasource contains a parameter with this name, its value will be used instead of the specified value
	/*
	$args->parameters["Parameter1"] = "TableName";
	$args->parameters["Parameter2"] = 10;
	*/
	
	// You can send a successful result:
	return StiResult::success();
	// You can send an informational message:
	//return StiResult::success("Warning or other useful information.");
	// You can send an error message:
	//return StiResult::error("A message about some connection error.");
};

$handler->onPrintReport = function ($args) {
	$fileName = $args->fileName; // Report file name
	
	return StiResult::success();
};

$handler->onBeginExportReport = function ($args) {
	$format = $args->format; // Export format
	$settings = $args->settings; // Export settions
	$fileName = $args->fileName; // Report file name
	
	return StiResult::success();
};

$handler->onEndExportReport = function ($args) {
	$format = $args->format; // Export format
	$data = $args->data; // Base64 export data
	$fileName = $args->fileName; // Report file name
	
	// By default, the exported file is saved to the 'reports' folder.
	// You can change this behavior if required.
	file_put_contents('reports/'.$fileName.'.'.strtolower($format), base64_decode($data));
	
	//return StiResult::success();
	return StiResult::success("Successful export of the report.");
	//return StiResult::error("An error occurred while exporting the report.");
};

$handler->onEmailReport = function ($args) {
	
	// These parameters will be used when sending the report by email. You must set the correct values.
	$args->settings->from = "******@gmail.com";
	$args->settings->host = "smtp.gmail.com";
	$args->settings->login = "******";
	$args->settings->password = "******";
	
	// These parameters are optional.
	//$args->settings->name = "John Smith";
	//$args->settings->port = 465;
	//$args->settings->cc[] = "copy1@gmail.com";
	//$args->settings->bcc[] = "copy2@gmail.com";
	//$args->settings->bcc[] = "copy3@gmail.com John Smith";
	
	return StiResult::success("Email sent successfully.");
};

$handler->onDesignReport = function ($args) {
	return StiResult::success();
};

$handler->onCreateReport = function ($args) {
	$fileName = $args->fileName;
	return StiResult::success();
};

$handler->onSaveReport = function ($args) {
	$report = $args->report; // Report object
	$reportJson = $args->reportJson; // Report in JSON format
	$fileName = $args->fileName; // Report file name
	
	file_put_contents('reports/'.$fileName.".mrt", $reportJson);
	
	//return StiResult::success();
	return StiResult::success("Save Report OK: ".$fileName);
	//return StiResult::error("Save Report ERROR. Message from server side.");
};

$handler->onSaveAsReport = function ($args) {
	return StiResult::success();
};


// Process request
$handler->process();
