<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Enums\StiToolbarDisplayMode;
use Stimulsoft\Enums\StiViewerTheme;
use Stimulsoft\StiComponentType;
use Stimulsoft\StiHandler;
use Stimulsoft\StiJavaScriptHelper;
use Stimulsoft\StiReport;
use Stimulsoft\StiViewer;
use Stimulsoft\Viewer\StiViewerOptions;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Stimulsoft Reports.PHP - Viewer</title>
    <style>html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Loading the necessary JavaScript for the components
    $helper = new StiJavaScriptHelper(StiComponentType::Viewer);
    echo $helper;

    // You can change the set of scripts to be loaded using the options
    /*
    $options = new \Stimulsoft\StiJavaScriptOptions();
    $options->dashboards = false;
    $helper = StiJavaScriptHelper(StiComponentType::Viewer, $engineOptions);
    echo $helper;
    */
    ?>

    <script type="text/javascript"><?php
        $handler = new StiHandler();

        // You can change the handler options (request url and timeout) if required
        /*
        $options = new \Stimulsoft\StiHandlerOptions();
        $options->handler->url = 'handler.php';
        $options->handler->timeout = 30;
        $handler = new StiHandler($options);
        */

        // Render all JavaScript functions to work with the PHP server
        echo $handler;


        $options = new StiViewerOptions();
        $options->appearance->theme = StiViewerTheme::Office2022WhiteGreen;
        $options->appearance->fullScreenMode = true;
        $options->appearance->scrollbarsMode = true;
        $options->toolbar->displayMode = StiToolbarDisplayMode::Separated;
        $options->appearance->backgroundColor = 'gray';
        echo $options;

        $viewer = new StiViewer($options);
        echo $viewer;

        $report = new StiReport();

        $viewer->report = $report;
        $viewer->renderHtml('viewerContent');

        ?>
    </script>

    <script type="text/javascript">

        // Create and set options.
        // More options can be found in the documentation at the link:
        // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_settings.htm

        // Create Viewer component.
        // A description of the parameters can be found in the documentation at the link:
        // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_showing_reports.htm
        //var viewer = new Stimulsoft.Viewer.StiViewer(viewerOptions, "StiViewer", false);

        // Optional Viewer events for fine tuning. You can uncomment and change any event or all of them, if necessary.
        // In this case, the built-in handler will be overridden by the selected event.
        // You can read and, if necessary, change the parameters in the args before server-side handler.

        // All events and their details can be found in the documentation at the link:
        // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_viewer_events.htm


        /*

        // Process report variables before rendering.
        viewer.onPrepareVariables = function (args, callback) {

            // Call the server-side handler
            Stimulsoft.Helper.process(args, callback);
        }

        */

        /*

        // Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
        viewer.onBeginProcessData = function (args, callback) {

            // Call the server-side handler
            Stimulsoft.Helper.process(args, callback);
        }

        */

        /*

        // Manage export settings and, if necessary, transfer them to the server and manage there
        viewer.onBeginExportReport = function (args, callback) {

            // Call the server-side handler
            Stimulsoft.Helper.process(args, callback);

            // Manage export settings
            // args.fileName = "MyReportName";
        }

        */

        /*

        // Process exported report file on the server side
        viewer.onEndExportReport = function (args) {

            // Prevent built-in handler (save the exported report as a file)
            args.preventDefault = true;

            // Call the server-side handler
            Stimulsoft.Helper.process(args);
        }

        */

        /*

        // Send exported report to Email
        viewer.onEmailReport = function (args) {

            // Call the server-side handler
            Stimulsoft.Helper.process(args);
        }

        */

        // Create a report and load a template from an MRT file:
        //var report = new Stimulsoft.Report.StiReport();
        //report.loadFile("reports/ReportMySql.mrt");
        //report.loadFile("reports/SimpleList.mrt");

        // Assigning a report to the Viewer:
        //viewer.report = report;

        // After loading the HTML page, display the visual part of the Viewer in the specified container.
        //function onLoad() {
        //    viewer.renderHtml("viewerContent");
        //}
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>
