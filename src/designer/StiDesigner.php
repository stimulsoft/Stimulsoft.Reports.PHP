<?php

namespace Stimulsoft;

use Stimulsoft\Report\StiReport;

class StiDesigner
{
    public $id;

    /** @var StiDesignerOptions */
    public $options;

    /** @var StiReport */
    public $report;

    public function getHtml($element = null, $renderOptionsHtml = true, $renderReportHtml = true)
    {
        return '';
    }

    public function renderHtml($element = null, $renderOptionsHtml = true, $renderReportHtml = true)
    {
        echo $this->getHtml($element, $renderOptionsHtml, $renderReportHtml);
    }

    public function __construct($options = null, $id = 'StiDesigner')
    {
        $this->options = $options;
        $this->id = strlen($id) > 0 ? $id : 'StiDesigner';
    }
}