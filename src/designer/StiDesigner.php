<?php

namespace Stimulsoft\Designer;

use Stimulsoft\Report\StiReport;

class StiDesigner
{
    public $id;

    /** @var StiDesignerOptions */
    public $options;

    /** @var StiReport */
    public $report;

    /** The event is invoked before data request, which are needed to render a report. */
    public $onBeginProcessData = false;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables = false;

    /** The event is invoked after creation a new report in the designer. */
    public $onCreateReport = false;

    /** The event is invoked before opening a report from the designer menu. TODO */
    //public $onOpenReport = false;

    /** The event is invoked when saving a report in the designer. */
    public $onSaveReport = false;

    /** The event is invoked when saving a report in the designer with a preliminary input of the file name. */
    public $onSaveAsReport = false;

    /** The event is invoked when going to the report view tab. TODO */
    //public $onPreviewReport = false;

    /** Get the HTML representation of the component. */
    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options ? $this->options->property : 'null';
        $designerProperty = $this->id == 'StiDesigner' ? 'designer' : $this->id;
        $result .= "let $designerProperty = new Stimulsoft.Designer.StiDesigner($optionsProperty, '$this->id', false);\n";

        if ($this->onPrepareVariables)
            $result .= "$designerProperty.onPrepareVariables = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onBeginProcessData)
            $result .= "$designerProperty.onBeginProcessData = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onCreateReport)
            $result .= "$designerProperty.onCreateReport = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onSaveReport)
            $result .= "$designerProperty.onSaveReport = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->onSaveAsReport)
            $result .= "$designerProperty.onSaveAsReport = function (args, callback) { Stimulsoft.Helper.process(args, callback); }\n";

        if ($this->report != null) {
            if (!$this->report->isHtmlRendered)
                $result .= $this->report->getHtml();

            $result .= "$designerProperty.report = {$this->report->reportId};\n";
        }

        $result .= "$designerProperty.renderHtml(" . (strlen($element) > 0 ? "'$element'" : '') . ");\n";

        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml($element = null)
    {
        echo $this->getHtml($element);
    }

    public function __construct($options = null, $id = 'StiDesigner')
    {
        $this->options = $options;
        $this->id = strlen($id) > 0 ? $id : 'StiDesigner';
    }
}