<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Designer\Enums\StiDesignerTheme;
use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Designer\StiDesignerOptions;
use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiHandler;
use Stimulsoft\StiJavaScriptHelper;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Stimulsoft Reports.PHP - Designer</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Loading the necessary JavaScript for the components
    $helper = new StiJavaScriptHelper(StiComponentType::Designer);
    $helper->renderHtml();

    // You can change the set of scripts to be loaded using the options
    /*
    $options = new \Stimulsoft\StiJavaScriptOptions();
    $options->dashboards = false;
    $helper = StiJavaScriptHelper(StiComponentType::Designer, $engineOptions);
    $helper->renderHtml();
    */
    ?>

    <script type="text/javascript">function onLoad() {
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

            // Creating Designer options
            $options = new StiDesignerOptions();
            $options->appearance->theme = StiDesignerTheme::Office2022WhiteGreen;
            $options->appearance->fullScreenMode = true;

            // Creating Designer component
            $designer = new StiDesigner($options);
            //$designer->onPrepareVariablesEvent = true;

            // Creating a report
            $report = new StiReport();
            $report->loadFile("reports/ReportMySql.mrt");
            //$report->load("reports/ReportMySql.mrt");

            $designer->report = $report;

            $designer->renderHtml('designerContent');
            ?>
        }
    </script>
</head>
<body onload="onLoad();">
<div id="designerContent"></div>
</body>
</html>
