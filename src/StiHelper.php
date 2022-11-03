<?php

namespace Stimulsoft;

class StiHelper
{
    public function renderScripts($componentType, $options = null)
    {
        if ($options == null)
            $options = new StiJavaScriptOptions();

        $scripts = array();
        if ($options->reports)
            $scripts[] = 'stimulsoft.reports.js';
        else {
            if ($options->reportsChart)
                $scripts[] = 'stimulsoft.reports.chart.js';
            if ($options->reportsExport)
                $scripts[] = 'stimulsoft.reports.export.js';
            if ($options->reportsMaps)
                $scripts[] = 'stimulsoft.reports.maps.js';
            if ($options->reportsImportXlsx)
                $scripts[] = 'stimulsoft.reports.import.xlsx.js';
        }

        if ($options->dashboards)
            $scripts[] = 'stimulsoft.dashboards.js';

        if ($componentType == StiComponentType::Viewer || $componentType == StiComponentType::Designer)
            $scripts[] = 'stimulsoft.viewer.js';

        if ($componentType == StiComponentType::Designer) {
            $scripts[] = 'stimulsoft.designer.js';

            if ($options->blocklyEditor)
                $scripts[] = 'stimulsoft.blockly.editor.js';
        }

        foreach ($scripts as $name) {
            echo '<script src="scripts/'.$name.'" type="text/javascript"></script>';
        }
    }

    public function renderHandler($options = null)
    {
        if ($options == null)
            $options = new StiHandlerOptions();

        ?>
        <script type="text/javascript">
            StiHelper.prototype.process = function (args, callback) {
                if (args) {
                    if (callback)
                        args.preventDefault = true;

                    if (args.event === 'BeginProcessData') {
                        if (args.database === 'XML' || args.database === 'JSON' || args.database === 'Excel')
                            return callback(null);
                        if (args.database === 'Data from DataSet, DataTables')
                            return callback(args);
                    }

                    let command = {};
                    for (let p in args) {
                        if (p === 'report') {
                            if (args.report && (args.event === 'CreateReport' || args.event === 'SaveReport' || args.event === 'SaveAsReport'))
                                command.report = JSON.parse(args.report.saveToJsonString());
                        } else if (p === 'settings' && args.settings) command.settings = args.settings;
                        else if (p === 'data') command.data = Stimulsoft.System.Convert.toBase64String(args.data);
                        else if (p === 'variables') command[p] = this.getVariables(args[p]);
                        else command[p] = args[p];
                    }

                    let sendText = Stimulsoft.Report.Dictionary.StiSqlAdapterService.getStringCommand(command);
                    if (!callback) callback = function (args) {
                        if (!args.success || !Stimulsoft.System.StiString.isNullOrEmpty(args.notice)) {
                            let message = Stimulsoft.System.StiString.isNullOrEmpty(args.notice) ? 'There was some error' : args.notice;
                            Stimulsoft.System.StiError.showError(message, true, args.success);
                        }
                    }
                    Stimulsoft.Helper.send(sendText, callback);
                }
            }

            StiHelper.prototype.send = function (json, callback) {
                let request = new XMLHttpRequest();
                try {
                    request.open('post', this.url, true);
                    request.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
                    request.setRequestHeader('Cache-Control', 'max-age=0');
                    request.setRequestHeader('Pragma', 'no-cache');
                    request.timeout = this.timeout * 1000;
                    request.onload = function () {
                        if (request.status === 200) {
                            let responseText = request.responseText;
                            request.abort();

                            try {
                                let args = JSON.parse(responseText);
                                if (args.report) {
                                    let json = args.report;
                                    args.report = new Stimulsoft.Report.StiReport();
                                    args.report.load(json);
                                }

                                callback(args);
                            } catch (e) {
                                Stimulsoft.System.StiError.showError(e.message);
                            }
                        } else {
                            Stimulsoft.System.StiError.showError('Server response error: [' + request.status + '] ' + request.statusText);
                        }
                    };
                    request.onerror = function (e) {
                        let errorMessage = 'Connect to remote error: [' + request.status + '] ' + request.statusText;
                        Stimulsoft.System.StiError.showError(errorMessage);
                    };
                    request.send(json);
                } catch (e) {
                    let errorMessage = 'Connect to remote error: ' + e.message;
                    Stimulsoft.System.StiError.showError(errorMessage);
                    request.abort();
                }
            };

            StiHelper.prototype.isNullOrEmpty = function (value) {
                return value == null || value === '' || value === undefined;
            }

            StiHelper.prototype.getVariables = function (variables) {
                if (variables) {
                    for (let variable of variables) {
                        if (variable.type === 'DateTime' && variable.value != null)
                            variable.value = variable.value.toString('YYYY-MM-DD HH:mm:SS');
                    }
                }

                return variables;
            }

            function StiHelper(url, timeout) {
                this.url = url;
                this.timeout = timeout;

                if (Stimulsoft && Stimulsoft.StiOptions) {
                    Stimulsoft.StiOptions.WebServer.url = url;
                    Stimulsoft.StiOptions.WebServer.timeout = timeout;
                }

                if (Stimulsoft && Stimulsoft.Base) {
                    Stimulsoft.Base.StiLicense.loadFromFile('/stimulsoft/license.php');
                }
            }

            Stimulsoft = Stimulsoft || {};
            Stimulsoft.Helper = new StiHelper('<?php echo $options->url; ?>', <?php echo $options->timeout; ?>);
            jsHelper = typeof jsHelper !== 'undefined' ? jsHelper : Stimulsoft.Helper;
        </script>
        <?php
    }

    public function process()
    {
        ?>Stimulsoft.Helper.process(arguments[0], arguments[1]);<?php
    }
}
