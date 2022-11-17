<?php

namespace Stimulsoft\Report;

use Stimulsoft\Enums\StiExportFormat;

class StiReport
{
    public $reportId;
    public $isHtmlRendered = false;
    public $isTemplate = true;

    private $reportString;
    private $reportFile;
    private $documentString;
    private $documentFile;
    private $exportFormat;
    private $exportFile;
    private $renderCallback;

    private function clearReport()
    {
        $this->isTemplate = true;
        $this->reportString = null;
        $this->reportFile = null;
        $this->documentString = null;
        $this->documentFile = null;
        $this->exportFile = null;
    }

    /** Load a report template from a file or URL address. */
    public function loadFile($filePath)
    {
        $this->clearReport();
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        $this->reportFile = $filePath;
    }

    /** Load a report template from a file or URL address and send it as a packed string in Base64 format. */
    public function load($filePath)
    {
        $this->clearReport();
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        if (file_exists($filePath)) {
            $this->reportString = file_get_contents($filePath);
            if (pathinfo($filePath, PATHINFO_EXTENSION) == 'mrt')
                $this->reportString = base64_encode(gzencode($this->reportString));
        }
    }

    /** Load a rendered report from a file or URL address. */
    public function loadDocumentFile($filePath)
    {
        $this->clearReport();
        $this->isTemplate = false;
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        $this->documentFile = $filePath;
    }

    /** Load a rendered report from a file or URL address and send it as a packed string in Base64 format. */
    public function loadDocument($filePath)
    {
        $this->clearReport();
        if (file_exists($filePath)) {
            $this->isTemplate = false;
            $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
            $this->documentString = file_get_contents($filePath);
            if (pathinfo($filePath, PATHINFO_EXTENSION) == 'mdc')
                $this->documentString = base64_encode(gzencode($this->documentString));
        }
    }

    /** Exporting the report to the specified format and saving it as a file on the client side. */
    public function exportDocument($format)
    {
        $this->exportFormat = $format;
    }

    /** Building a report and calling a JavaScript callback function, if it is set. */
    public function render($callback = null)
    {
        $this->renderCallback = $callback != null ? $callback : 'null';
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = "let $this->reportId = new Stimulsoft.Report.StiReport();\n";

        if (strlen($this->reportFile) > 0)
            $result .= "$this->reportId.loadFile('$this->reportFile');\n";

        else if (strlen($this->reportString) > 0)
            $result .= "$this->reportId.loadPacked('$this->reportString');\n";

        else if (strlen($this->documentFile) > 0)
            $result .= "$this->reportId.loadDocumentFile('$this->documentFile');\n";

        else if (strlen($this->documentString) > 0)
            $result .= "$this->reportId.loadPackedDocument('$this->documentString');\n";

        if ($this->renderCallback != null && $this->isTemplate) {
            $callback = $this->renderCallback != 'null' ? $this->renderCallback : '';
            $result .= "$this->reportId.renderAsync($callback);\n";
        }

        if ($this->exportFormat != null) {
            $exportFileExt = StiExportFormat::getFileExtension($this->exportFormat);
            $exportMimeType = StiExportFormat::getMimeType($this->exportFormat);
            $exportName = StiExportFormat::getFormatName($this->exportFormat);

            if ($this->isTemplate)
                $result .= "$this->reportId.renderAsync(function () {\n";

            $result .= "report.exportDocumentAsync(function (data) {
                            Stimulsoft.System.StiObject.saveAs(data, '$this->exportFile.$exportFileExt', '$exportMimeType');
                        }, Stimulsoft.Report.StiExportFormat.$exportName);
                    ";

            if ($this->isTemplate)
                $result .= "});\n";
        }

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