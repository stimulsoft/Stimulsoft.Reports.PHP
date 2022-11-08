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

    /** The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData = false;

    /** The event is invoked after loading data before rendering a report. */
    public $onEndProcessData = false;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables = false;

    /** The event is invoked before printing a report. */
    public $onPrintReport = false;

    /** The event is invoked before exporting a report after the dialog of export settings. */
    public $onBeginExportReport = false;

    /** The event is invoked after exporting a report till its saving as a file. */
    public $onEndExportReport = false;

    /**
     * The event is invoked while interactive action of the viewer (dynamic sorting, collapsing, drill-down, applying of parameters)
     * until processing values by the report generator. TODO
     */
    //public $onInteraction = false;

    /** The event is invoked after exporting a report before sending it by Email. */
    public $onEmailReport = false;

    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options ? $this->options->property : 'null';
        $viewerProperty = $this->id == 'StiViewer' ? 'viewer' : $this->id;
        $result .= "let $viewerProperty = new Stimulsoft.Viewer.StiViewer($optionsProperty, '$this->id', false);\n";

        if ($this->onPrepareVariables)
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