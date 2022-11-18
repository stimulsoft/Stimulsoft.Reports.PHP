<?php

namespace Stimulsoft\Report;

use Stimulsoft\Enums\StiExportFormat;
use Stimulsoft\StiHtmlComponent;

class StiReport extends StiHtmlComponent
{
    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables;

    /** The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData;

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

    /**
     * Loading a report template from a file or URL address.
     * @param string $filePath The path to the file or the URL of the report template.
     * @param bool $load Loading a report file on the server side.
     */
    public function loadFile($filePath, $load = false)
    {
        $this->clearReport();
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        if ($load) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && $extension == 'mrt') {
                $reportString = file_get_contents($filePath);
                $this->reportString = base64_encode(gzencode($reportString));
            }
        }
        else {
            $this->reportFile = $filePath;
        }
    }

    /**
     * Loading a report template from an XML or JSON string and send it as a packed string in Base64 format.
     * @param string $data Report template in XML or JSON format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function load($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->reportString = base64_encode(gzencode($data));
    }

    /**
     * Load a rendered report from a file or URL address.
     * @param string $filePath The path to the file or the URL of the rendered report.
     * @param bool $load Loading a report file on the server side.
     */
    public function loadDocumentFile($filePath, $load = false)
    {
        $this->clearReport();
        $this->isTemplate = false;
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        if ($load) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && $extension == 'mdc') {
                $documentString = file_get_contents($filePath);
                $this->documentString = base64_encode(gzencode($documentString));
            }
        }
        else {
            $this->documentFile = $filePath;
        }
    }

    /**
     * Load a rendered report from an XML or JSON string and send it as a packed string in Base64 format.
     * @param string $data Rendered report in XML or JSON format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadDocument($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->isTemplate = false;
        $this->exportFile = $fileName;
        $this->documentString = base64_encode(gzencode($data));
    }

    /**
     * Exporting the report to the specified format and saving it as a file on the client side.
     * @param string $format The type of the export. Is equal to one of the values of the StiExportFormat enumeration.
     */
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
        $result = "let $this->id = new Stimulsoft.Report.StiReport();\n";

        if ($this->onPrepareVariables)
            $result .= $this->getEventHtml('onPrepareVariables', true);

        if ($this->onBeginProcessData)
            $result .= $this->getEventHtml('onBeginProcessData'. true);

        if (strlen($this->reportFile) > 0)
            $result .= "$this->id.loadFile('$this->reportFile');\n";

        else if (strlen($this->reportString) > 0)
            $result .= "$this->id.loadPacked('$this->reportString');\n";

        else if (strlen($this->documentFile) > 0)
            $result .= "$this->id.loadDocumentFile('$this->documentFile');\n";

        else if (strlen($this->documentString) > 0)
            $result .= "$this->id.loadPackedDocument('$this->documentString');\n";

        if ($this->renderCallback != null && $this->isTemplate) {
            $callback = $this->renderCallback != 'null' ? $this->renderCallback : '';
            $result .= "$this->id.renderAsync($callback);\n";
        }

        if ($this->exportFormat != null) {
            $exportFileExt = StiExportFormat::getFileExtension($this->exportFormat);
            $exportMimeType = StiExportFormat::getMimeType($this->exportFormat);
            $exportName = StiExportFormat::getFormatName($this->exportFormat);

            if ($this->isTemplate)
                $result .= "$this->id.renderAsync(function () {\n";

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

    public function __construct($id = 'report')
    {
        $this->id = strlen($id) > 0 ? $id : 'report';
    }
}