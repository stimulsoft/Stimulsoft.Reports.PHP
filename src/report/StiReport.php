<?php

namespace Stimulsoft\Report;

class StiReport
{
    public $reportId;
    public $isHtmlRendered = false;

    private $reportString;
    private $reportUrl;

    private function getReportFileExt($fileUrl)
    {
        return substr($fileUrl, strlen($fileUrl) > 3 ? -3 : 0);
    }

    public function loadFile($fileUrl)
    {
        $this->clearReport();
        $this->reportUrl = $fileUrl;
    }

    public function load($fileUrl)
    {
        $this->clearReport();
        if (file_exists($fileUrl)) {
            $this->reportString = file_get_contents($fileUrl);
            if ($this->getReportFileExt($fileUrl) == 'mrt')
                $this->reportString = base64_encode(gzencode($this->reportString));
        }
    }

    private function clearReport()
    {
        $this->reportString = null;
        $this->reportUrl = null;
    }

    public function getHtml()
    {
        $result = "let $this->reportId = new Stimulsoft.Report.StiReport();\n";

        if (strlen($this->reportUrl) > 0)
            $result .= "$this->reportId.loadFile('$this->reportUrl');\n";

        else if (strlen($this->reportString) > 0)
            $result .= "$this->reportId.loadPacked('$this->reportString');\n";

        $this->isHtmlRendered = true;
        return $result;
    }

    public function renderHtml()
    {
        echo $this->getHtml();
    }

    public function __construct($reportId = 'report')
    {
        $this->reportId = strlen($reportId) > 0 ? $reportId : 'report';
    }
}