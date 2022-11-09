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

    <script src="scripts/stimulsoft.reports.js" type="text/javascript"></script>
    <script src="scripts/stimulsoft.viewer.js" type="text/javascript"></script>

    <script type="text/javascript">
        StiOptions.WebServer.url = "http://reports-php.site/src/adapters/handler.php";

        function onLoad() {
            let viewer = new Stimulsoft.Viewer.StiViewer(null, "StiViewer", false);

            let report = new Stimulsoft.Report.StiReport();
            report.loadFile("reports/ReportMySql.mrt");
            //report.loadFile("reports/SimpleList.mrt");
            viewer.report = report;

            viewer.renderHtml("viewerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>
