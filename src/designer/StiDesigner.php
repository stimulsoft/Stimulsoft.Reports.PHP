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

    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options ? $this->options->property : 'null';
        $designerProperty = $this->id == 'StiDesigner' ? 'designer' : $this->id;
        $result .= "let $designerProperty = new Stimulsoft.Designer.StiDesigner($optionsProperty, '$this->id', false);\n";



        if ($this->report != null) {
            if (!$this->report->isHtmlRendered)
                $result .= $this->report->getHtml();

            $result .= "$designerProperty.report = {$this->report->reportId};\n";
        }

        $result .= "$designerProperty.renderHtml(" . (strlen($element) > 0 ? "'$element'" : '') . ");\n";

        return $result;
    }

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