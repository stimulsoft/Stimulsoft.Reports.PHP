<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiHandler;
use Stimulsoft\StiJavaScriptHelper;
use Stimulsoft\Viewer\Enums\StiToolbarDisplayMode;
use Stimulsoft\Viewer\Enums\StiViewerTheme;
use Stimulsoft\Viewer\StiViewer;
use Stimulsoft\Viewer\StiViewerOptions;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Stimulsoft Reports.PHP - Viewer</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Loading the necessary JavaScript for the components
    $helper = new StiJavaScriptHelper(StiComponentType::Viewer);
    $helper->renderHtml();

    // You can change the set of scripts to be loaded using the options
    /*
    $options = new \Stimulsoft\StiJavaScriptOptions();
    $options->dashboards = false;
    $helper = StiJavaScriptHelper(StiComponentType::Viewer, $engineOptions);
    $helper->renderHtml();
    */
    ?>

    <script type="text/javascript">
        function onLoad() {
            <?php
            // Render all JavaScript functions to work with the PHP server
            $handler = new StiHandler();
            $handler->renderHtml();

            // You can change the handler options (request url and timeout) if required
            /*
            $options = new \Stimulsoft\StiHandlerOptions();
            $options->handler->url = 'handler.php';
            $options->handler->timeout = 30;
            $handler = new StiHandler($options);
            $handler->renderHtml();
            */

            // Creating Viewer options
            $options = new StiViewerOptions();
            $options->appearance->theme = StiViewerTheme::Office2022WhiteGreen;
            $options->appearance->fullScreenMode = true;
            $options->appearance->scrollbarsMode = true;
            $options->toolbar->displayMode = StiToolbarDisplayMode::Separated;
            $options->toolbar->showSendEmailButton = true;
            $options->appearance->backgroundColor = 'gray';

            // Creating Viewer component
            $viewer = new StiViewer($options);

            // Adding the necessary event handlers.
            $viewer->onBeginProcessData = true;
            $viewer->onEmailReport = true;

            // Creating a report
            $report = new StiReport();
            $report->loadFile("reports/SimpleList.mrt");
            //$report->loadFile("reports/ReportMySql.mrt");
            //$report->load("reports/ReportMySql.mrt");

            $viewer->report = $report;

            // Output of the HTML representation of the component
            $viewer->renderHtml('viewerContent');
            ?>
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>
