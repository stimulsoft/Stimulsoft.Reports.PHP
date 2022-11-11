<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiComponentType;

class StiJavaScript
{
    public $componentType;
    public $options;
    public $packed = false;

    public function getHtml()
    {
        $dashboardsDir = $_SERVER['DOCUMENT_ROOT'] . '/vendor/stimulsoft/dashboards-php';
        $dashboards = is_dir($dashboardsDir);

        $extension = $this->packed ? 'pack.js' : 'js';

        $scripts = array();
        if ($this->options->reports)
            $scripts[] = "stimulsoft.reports.$extension";
        else {
            if ($this->options->reportsChart)
                $scripts[] = "stimulsoft.reports.chart.$extension";
            if ($this->options->reportsExport)
                $scripts[] = "stimulsoft.reports.export.$extension";
            if ($this->options->reportsMaps)
                $scripts[] = "stimulsoft.reports.maps.$extension";
            if ($this->options->reportsImportXlsx)
                $scripts[] = "stimulsoft.reports.import.xlsx.$extension";
        }

        if ($dashboards)
            $scripts[] = "stimulsoft.dashboards.$extension";

        if ($this->componentType == StiComponentType::Viewer || $this->componentType == StiComponentType::Designer)
            $scripts[] = "stimulsoft.viewer.$extension";

        if ($this->componentType == StiComponentType::Designer) {
            $scripts[] = "stimulsoft.designer.$extension";

            if ($this->options->blocklyEditor)
                $scripts[] = "stimulsoft.blockly.editor.$extension";
        }

        $result = '';
        foreach ($scripts as $name) {
            $result .= "<script src=\"vendor/stimulsoft/reports-php/public/scripts/$name\" type=\"text/javascript\"></script>\n";
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