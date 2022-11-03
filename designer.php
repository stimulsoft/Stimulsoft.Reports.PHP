<?php
require_once 'vendor/autoload.php';

use Stimulsoft\StiComponentType;
use Stimulsoft\StiHelper;
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<title>Stimulsoft Reports.PHP - Designer</title>
	<style>html, body { font-family: sans-serif; }</style>

    <?php
    // Creating a component deployment helper
    $helper = new StiHelper();

    // Adding designer, viewer and report engine JavaScript
    $helper->renderScripts(StiComponentType::Designer);

    // You can change the set of scripts to be loaded using the options
    /*
    $options = new \Stimulsoft\StiJavaScriptOptions();
    $options->dashboards = false;
    $helper->renderScripts(StiComponentType::Designer, $options);
    */

    // Adding JavaScript functions to work with the PHP server
    $helper->renderHandler();

    // You can change the handler file and timeout if required
    /*
    $options = new \Stimulsoft\StiHandlerOptions();
    $options->handler->url = 'handler.php';
    $options->handler->timeout = 30;
    $helper->renderHandler($handlerOptions);
    */
    ?>
	
	<script type="text/javascript">
		// Create and set options.
		// More options can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_settings.htm
		var options = new Stimulsoft.Designer.StiDesignerOptions();
		options.appearance.fullScreenMode = true;
		
		// Create Designer component.
		// A description of the parameters can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_add_designer.htm
		var designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);
		
		// Optional Designer events for fine tuning. You can uncomment and change any event or all of them, if necessary.
		// In this case, the built-in handler will be overridden by the selected event.
		// You can read and, if necessary, change the parameters in the args before server-side handler.
		
		// All events and their details can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_designer_events.htm
		
		
		/*
		
		// Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
		designer.onBeginProcessData = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		/*
		
		// Save report template on the server side.
		designer.onSaveReport = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		// Create a report and load a template from an MRT file:
		var report = new Stimulsoft.Report.StiReport();
		report.loadFile("reports/ReportMySql.mrt");
		
		// Assigning a report to the Designer:
		designer.report = report;
		
		// After loading the HTML page, display the visual part of the Designer in the specified container.
		function onLoad() {
			designer.renderHtml("designerContent");
		}
	</script>
</head>
<body onload="onLoad();">
	<div id="designerContent"></div>
</body>
</html>
