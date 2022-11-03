<?php

namespace Stimulsoft;

class StiHandlerOptions
{
    public $url = 'handler.php';
    public $timeout = 30;
}

class StiJavaScriptOptions
{
    public $reports = true;
    public $reportsChart = true;
    public $reportsExport = true;
    public $reportsImportXlsx = true;
    public $reportsMaps = true;
    public $dashboards = true;
    public $blocklyEditor = true;
}

class StiComponentType
{
    const Engine = 'Engine';
    const Viewer = 'Viewer';
    const Designer = 'Designer';
}

class StiEventType
{
    const PrepareVariables = "PrepareVariables";
    const BeginProcessData = "BeginProcessData";
    const CreateReport = "CreateReport";
    const OpenReport = "OpenReport";
    const SaveReport = "SaveReport";
    const SaveAsReport = "SaveAsReport";
    const PrintReport = "PrintReport";
    const BeginExportReport = "BeginExportReport";
    const EndExportReport = "EndExportReport";
    const EmailReport = "EmailReport";
}

class StiEventArgs
{
    public $sender;
    public $command;
    public $database;
    public $connectionString;
    public $queryString;
    public $dataSource;
    public $connection;
    public $parameters;
    public $result;
    public $variables;
    public $report;
    public $reportJson;
    public $isWizardUsed;
    public $fileName;
    public $printAction;
    public $action;
    public $format;
    public $formatName;
    public $settings;
    public $fileExtension;
    public $data;

    public function populateVars($obj)
    {
        $className = get_class($this);
        $vars = get_class_vars($className);
        foreach ($vars as $name => $value) {
            if (isset($obj->{$name}))
                $this->{$name} = $obj->{$name};
        }
    }
}

class StiVariable
{
    public $type;
    public $value;
}

class StiVariableRange
{
    public $from;
    public $to;
}

class StiExportFormat
{
    const Pdf = 1;
    const Text = 11;
    const Excel2007 = 14;
    const Word2007 = 15;
    const Csv = 17;
    const ImageSvg = 28;
    const Html = 32;
    const Ods = 33;
    const Odt = 34;
    const Ppt2007 = 35;
    const Html5 = 36;
    const Document = 1000;
}

class StiExportAction
{
    const ExportReport = 1;
    const SendEmail = 2;
}

class StiEmailSettings
{
    /** Email address of the sender. */
    public $from;

    /** Name and surname of the sender. */
    public $name;

    /** Email address of the recipient. */
    public $to;

    /** Email Subject. */
    public $subject;

    /** Text of the Email. */
    public $message;

    /** Attached file name. */
    public $attachmentName;

    /** Charset for the message. */
    public $charset = 'UTF-8';

    /** Address of the SMTP server. */
    public $host;

    /** Port of the SMTP server. */
    public $port = 465;

    /** The secure connection prefix - ssl or tls. */
    public $secure = 'ssl';

    /** Login (Username or Email). */
    public $login;

    /** Password */
    public $password;

    /** The array of 'cc' addresses. */
    public $cc = array();

    /** The array of 'bcc' addresses. */
    public $bcc = array();
}

class StiRequest extends StiDataRequest
{
    public $sender;
    public $event;
    public $data;
    public $fileName;
    public $action;
    public $printAction;
    public $format;
    public $formatName;
    public $settings;
    public $variables;
    public $parameters;
    public $escapeQueryParameters;
    public $isWizardUsed;
    public $report;
    public $reportJson;

    protected function checkRequestParams($obj)
    {
        if (!isset($obj->event) && isset($obj->command) && ($obj->command == StiCommand::TestConnection || StiCommand::ExecuteQuery))
            $this->event = StiEventType::BeginProcessData;

        if (isset($obj->report)) {
            $this->report = $obj->report;
            $this->reportJson = json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return StiResult::success(null, $this);
    }
}