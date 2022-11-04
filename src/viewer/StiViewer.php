<?php

namespace Stimulsoft;

use Stimulsoft\Viewer\StiViewerOptions;

class StiViewer
{
    /** @var StiViewerOptions $options */
    public $options;
    public $viewerId;
    public $report;

    public function renderHtml($element)
    {
        echo 'viewer.renderHtml(' . (strlen($element) > 0 ? "'$element'" : '') . ");\n";
    }

    public function __toString()
    {
        $result = "<script type=\"text/javascript\">\n";
        $result .= "var $this->viewerId = new Stimulsoft.Viewer.StiViewer({$this->options->property}, '$this->viewerId', false);\n";
        return $result . "</script>\n";
    }

    public function __construct($options = null, $viewerId = 'StiViewer')
    {
        $this->options = $options != null ? $options : new StiViewerOptions();
        $this->viewerId = $viewerId;
    }
}