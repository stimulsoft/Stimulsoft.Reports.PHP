<?php

namespace Stimulsoft;

class StiJavaScriptHelper
{
    public $componentType;
    public $options;

    public function getHtml()
    {
        $scripts = array();
        if ($this->options->reports)
            $scripts[] = 'stimulsoft.reports.js';
        else {
            if ($this->options->reportsChart)
                $scripts[] = 'stimulsoft.reports.chart.js';
            if ($this->options->reportsExport)
                $scripts[] = 'stimulsoft.reports.export.js';
            if ($this->options->reportsMaps)
                $scripts[] = 'stimulsoft.reports.maps.js';
            if ($this->options->reportsImportXlsx)
                $scripts[] = 'stimulsoft.reports.import.xlsx.js';
        }

        if ($this->options->dashboards)
            $scripts[] = 'stimulsoft.dashboards.js';

        if ($this->componentType == StiComponentType::Viewer || $this->componentType == StiComponentType::Designer)
            $scripts[] = 'stimulsoft.viewer.js';

        if ($this->componentType == StiComponentType::Designer) {
            $scripts[] = 'stimulsoft.designer.js';

            if ($this->options->blocklyEditor)
                $scripts[] = 'stimulsoft.blockly.editor.js';
        }

        $result = '';
        foreach ($scripts as $name) {
            $result .= "<script src=\"scripts/$name\" type=\"text/javascript\"></script>\n";
        }

        return $result;
    }

    public function renderHtml()
    {
        echo $this->getHtml();
    }

    public function __construct($componentType, $options = null)
    {
        $this->componentType = $componentType;
        $this->options = $options != null ? $options : new StiJavaScriptOptions();
    }
}