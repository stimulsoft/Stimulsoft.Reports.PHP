<?php

namespace Stimulsoft\Report;

class StiReport
{
    public $reportId;
    public $isHtmlRendered = false;

    private $reportString;
    private $reportFile;

    private function getReportFileExt($filePath)
    {
        return substr($filePath, strlen($filePath) > 3 ? -3 : 0);
    }

    public function loadFile($filePath)
    {
        $this->clearReport();
        $this->reportFile = $filePath;
    }

    public function load($filePath)
    {
        $this->clearReport();
        if (file_exists($filePath)) {
            $this->reportString = file_get_contents($filePath);
            if ($this->getReportFileExt($filePath) == 'mrt')
                $this->reportString = base64_encode(gzencode($this->reportString));
        }
    }

    private function clearReport()
    {
        $this->reportString = null;
        $this->reportFile = null;
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = "let $this->reportId = new Stimulsoft.Report.StiReport();\n";

        if (strlen($this->reportFile) > 0)
            $result .= "$this->reportId.loadFile('$this->reportFile');\n";

        else if (strlen($this->reportString) > 0)
            $result .= "$this->reportId.loadPacked('$this->reportString');\n";

        $this->isHtmlRendered = true;
        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }

    public function __construct($reportId = 'report')
    {
        $this->reportId = strlen($reportId) > 0 ? $reportId : 'report';
    }
}