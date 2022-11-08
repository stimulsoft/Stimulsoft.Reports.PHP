<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\Report\StiReport;

class StiViewer
{
    public $id;

    /** @var StiViewerOptions */
    public $options;

    /** @var StiReport */
    public $report;

    /** @var bool
     * Process report variables before rendering.
     */
    public $onPrepareVariablesEvent = false;

    /** @var bool
     * Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
     */
    public $onBeginProcessData = false;

    /** @var bool */
    public $onEndProcessData = false;

    /** @var bool */
    public $onPrintReport = false;

    /** @var bool
     * Manage export settings and, if necessary, transfer them to the server and manage there.
     */
    public $onBeginExportReport = false;

    /** @var bool
     * Process exported report file on the server side.
     */
    public $onEndExportReport = false;

    /** @var bool TODO */
    //public $onInteraction = false;

    /** @var bool
     * Send exported report to Email.
     */
    public $onEmailReport = false;

    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options ? $this->options->property : 'null';
        $viewerProperty = $this->id == 'StiViewer' ? 'viewer' : $this->id;
        $result .= "let $viewerProperty = new Stimulsoft.Viewer.StiViewer($optionsProperty, '$this->id', false);\n";

        if ($this->onPrepareVariablesEvent)
            $result .= "$viewerProperty.onPrepareVariables = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onBeginProcessData)
            $result .= "$viewerProperty.onBeginProcessData = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onEndProcessData)
            $result .= "$viewerProperty.onEndProcessData = function (args) { Stimulsoft.Helper.process(args); }\n";

        if ($this->onPrintReport)
            $result .= "$viewerProperty.onPrintReport = function (args) { Stimulsoft.Helper.process(args); }\n";

        if ($this->onBeginExportReport)
            $result .= "$viewerProperty.onBeginExportReport = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onEndExportReport)
            $result .= "$viewerProperty.onEndExportReport = function (args) { args.preventDefault = true; Stimulsoft.Helper.process(args); }\n";

        /*if ($this->onInteraction) TODO
            $result .= "$viewerProperty.onInteraction = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";*/

        if ($this->onEmailReport)
            $result .= "$viewerProperty.onEmailReport = function (args) { Stimulsoft.Helper.process(args); }\n";

        if ($this->report != null) {
            if (!$this->report->isHtmlRendered)
                $result .= $this->report->getHtml();

            $result .= "$viewerProperty.report = {$this->report->reportId};\n";
        }

        $result .= "$viewerProperty.renderHtml(" . (strlen($element) > 0 ? "'$element'" : '') . ");\n";

        return $result;
    }

    public function renderHtml($element = null)
    {
        echo $this->getHtml($element);
    }

    public function __construct($options = null, $id = 'StiViewer')
    {
        $this->options = $options;
        $this->id = strlen($id) > 0 ? $id : 'StiViewer';
    }
}