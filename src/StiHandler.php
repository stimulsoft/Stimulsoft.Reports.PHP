<?php

namespace Stimulsoft;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class StiHandler extends StiDataHandler
{
    public $version = '2022.4.3';

    public $onBeginProcessData;
    public $onEndProcessData;
    public $onPrepareVariables;
    public $onCreateReport;
    public $onOpenReport;
    public $onSaveReport;
    public $onSaveAsReport;
    public $onPrintReport;
    public $onBeginExportReport;
    public $onEndExportReport;
    public $onEmailReport;


    // Functions

    private function checkEventResult($event, $args)
    {
        if (isset($event)) $result = $event($args);
        if (!isset($result)) $result = StiResult::success();
        if ($result === true) return StiResult::success();
        if ($result === false) return StiResult::error();
        if (gettype($result) == 'string') return StiResult::error($result);
        if (isset($args)) $result->object = $args;
        return $result;
    }

    private function applyQueryParameters($query, $parameters, $escape)
    {
        $result = '';

        while (mb_strpos($query, '@') !== false) {
            $result .= mb_substr($query, 0, mb_strpos($query, '@'));
            $query = mb_substr($query, mb_strpos($query, '@') + 1);

            $parameterName = '';
            while (strlen($query) > 0) {
                $char = mb_substr($query, 0, 1);
                if (!preg_match('/[a-zA-Z0-9_-]/', $char)) break;

                $parameterName .= $char;
                $query = mb_substr($query, 1);
            }

            $replaced = false;
            foreach ($parameters as $key => $item) {
                if (strtolower($key) == strtolower($parameterName)) {
                    switch ($item->typeGroup) {
                        case 'number':
                            $result .= $item->value;
                            break;

                        case 'datetime':
                            $result .= "'" . $item->value . "'";
                            break;

                        default:
                            $result .= "'" . ($escape ? addcslashes($item->value, "\\\"'") : $item->value) . "'";
                            break;
                    }

                    $replaced = true;
                }
            }

            if (!$replaced) $result .= '@' . $parameterName;
        }

        return $result . $query;
    }

    private function getFileExtension($format)
    {
        switch ($format) {
            case StiExportFormat::Pdf:
                return "pdf";

            case StiExportFormat::Text:
                return "txt";

            case StiExportFormat::Excel2007:
                return "xlsx";

            case StiExportFormat::Word2007:
                return "docx";

            case StiExportFormat::Csv:
                return "csv";

            case StiExportFormat::ImageSvg:
                return "svg";

            case StiExportFormat::Html:
            case StiExportFormat::Html5:
                return "html";

            case StiExportFormat::Ods:
                return "ods";

            case StiExportFormat::Odt:
                return "odt";

            case StiExportFormat::Ppt2007:
                return "pptx";

            case StiExportFormat::Document:
                return "mdc";
        }

        return strtolower($format);
    }

    private function addAddress($param, $settings, $mail)
    {
        $arr = $settings->$param;

        if ($arr != null && count($arr) > 0) {
            if ($param == 'cc') $mail->clearCCs();
            else $mail->clearBCCs();

            foreach ($arr as $value) {
                $name = mb_strpos($value, ' ') > 0 ? mb_substr($value, mb_strpos($value, ' ')) : '';
                $address = strlen($name) > 0 ? mb_substr($value, 0, mb_strpos($value, ' ')) : $value;

                if ($param == 'cc') $mail->addCC($address, $name);
                else $mail->addBCC($address, $name);
            }
        }
    }


    // Events

    private function invokeBeginProcessData($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        if (isset($request->queryString) && isset($request->parameters)) {
            $args->parameters = array();
            foreach ($request->parameters as $item) {
                $args->parameters[$item->name] = $item;
                unset($item->name);
            }
        }

        $result = $this->checkEventResult($this->onBeginProcessData, $args);
        if (isset($result->object->queryString) && isset($args->parameters) && count($args->parameters) > 0)
            $result->object->queryString = $this->applyQueryParameters($result->object->queryString, $args->parameters, $request->escapeQueryParameters);

        return $result;
    }

    private function invokeEndProcessData($request, $result)
    {
        $args = new StiEventArgs();
        $args->sender = $request->sender;
        $args->result = $result;
        return $this->checkEventResult($this->onEndProcessData, $args);
    }

    private function invokePrepareVariables($request)
    {
        $args = new StiEventArgs();
        $args->sender = $request->sender;

        $args->variables = array();
        if (isset($request->variables)) {
            foreach ($request->variables as $item) {
                $request->variables[$item->name] = $item;
                $variableObject = new StiVariable();
                $variableObject->value = $item->value;
                $variableObject->type = $item->type;

                if (substr($item->type, -5) === 'Range') {
                    $variableObject->value = new StiVariableRange();
                    $variableObject->value->from = $item->value->from;
                    $variableObject->value->to = $item->value->to;
                }

                $args->variables[$item->name] = $variableObject;
            }
        }

        $result = $this->checkEventResult($this->onPrepareVariables, $args);

        if (isset($result->object)) {
            $variables = array();
            foreach ($result->object->variables as $key => $item) {
                // Send only changed or new values
                if (!array_key_exists($key, $request->variables) ||
                    $item->value != $request->variables[$key]->value ||
                    substr($item->type, -5) === 'Range' && (
                        $item->value->from != $request->variables[$key]->value->from ||
                        $item->value->to != $request->variables[$key]->value->to)
                ) {
                    if (!is_object($item)) $item = (object)$item;
                    $item->name = $key;
                    $variables[] = $item;
                }
            }

            $result->variables = $variables;
        }

        return $result;
    }

    private function invokeCreateReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        $result = $this->checkEventResult($this->onCreateReport, $args);
        $result->report = $args->report;

        return $result;
    }

    private function invokeOpenReport($request)
    {
        $args = new StiEventArgs();
        $args->sender = $request->sender;
        return $this->checkEventResult($this->onOpenReport, $args);
    }

    private function invokeSaveReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        return $this->checkEventResult($this->onSaveReport, $args);
    }

    private function invokeSaveAsReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        return $this->checkEventResult($this->onSaveAsReport, $args);
    }

    private function invokePrintReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        return $this->checkEventResult($this->onPrintReport, $args);
    }

    private function invokeBeginExportReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);

        $result = $this->checkEventResult($this->onBeginExportReport, $args);
        $result->fileName = $args->fileName;
        $result->settings = $args->settings;

        return $result;
    }

    private function invokeEndExportReport($request)
    {
        $args = new StiEventArgs();
        $args->populateVars($request);
        $args->fileExtension = $this->getFileExtension($request->format);

        return $this->checkEventResult($this->onEndExportReport, $args);
    }

    private function invokeEmailReport($request)
    {
        $settings = new StiEmailSettings();
        $settings->to = $request->settings->email;
        $settings->subject = $request->settings->subject;
        $settings->message = $request->settings->message;
        $settings->attachmentName = $request->fileName . '.' . $this->getFileExtension($request->format);

        $args = new StiEventArgs();
        $args->sender = $request->sender;
        $args->settings = $settings;
        $args->format = $request->format;
        $args->formatName = $request->formatName;
        $args->fileName = $request->fileName;
        $args->data = base64_decode($request->data);

        $result = $this->checkEventResult($this->onEmailReport, $args);
        if (!$result->success) return $result;

        $guid = substr(md5(uniqid() . mt_rand()), 0, 12);
        if (!file_exists('tmp')) mkdir('tmp');
        file_put_contents('tmp/' . $guid . '.' . $args->fileName, $args->data);

        // Detect auth mode
        $auth = $settings->host != null && $settings->login != null && $settings->password != null;

        $mail = new PHPMailer(true);
        if ($auth) $mail->IsSMTP();
        try {
            $mail->CharSet = $settings->charset;
            $mail->IsHTML(false);
            $mail->From = $settings->from;
            $mail->FromName = $settings->name;

            // Add Emails list
            $emails = preg_split('/[,;]/', $settings->to);
            foreach ($emails as $settings->to) {
                $mail->AddAddress(trim($settings->to));
            }

            // Fill email fields
            $mail->Subject = htmlspecialchars($settings->subject);
            $mail->Body = $settings->message;
            $mail->AddAttachment('tmp/' . $guid . '.' . $args->fileName, $settings->attachmentName);

            // Fill auth fields
            if ($auth) {
                $mail->Host = $settings->host;
                $mail->Port = $settings->port;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $settings->secure;
                $mail->Username = $settings->login;
                $mail->Password = $settings->password;
            }

            // Fill CC and BCC
            $this->addAddress('cc', $settings, $mail);
            $this->addAddress('bcc', $settings, $mail);

            $mail->Send();
        }
        catch (Exception $e) {
            $error = strip_tags($e->getMessage());
        }

        unlink('tmp/' . $guid . '.' . $args->fileName);

        return isset($error) ? StiResult::error($error) : $result;
    }


    // Process request

    public function process($response = true)
    {
        $request = new StiRequest();
        $result = $request->parse();
        if ($result->success) {
            switch ($request->event) {
                case StiEventType::BeginProcessData:
                    $result = $this->invokeBeginProcessData($request);
                    if (!$result->success) break;
                    $queryString = $result->object->queryString;
                    $result = $this->getDataAdapter($result->object);
                    $result->handlerVersion = $this->version;
                    if (!$result->success) break;

                    $dataAdapter = $result->object;

                    /** @var StiSqlAdapter $dataAdapter */
                    switch ($request->command) {
                        case StiCommand::TestConnection:
                            $result = $dataAdapter->test();
                            break;

                        case StiCommand::ExecuteQuery:
                            $result = $dataAdapter->execute($queryString);
                            break;
                    }

                    /** @var StiAdapterResult $result */
                    $result = $this->invokeEndProcessData($request, $result);
                    $result->handlerVersion = $this->version;
                    $result->adapterVersion = $dataAdapter->version;
                    $result->checkVersion = $dataAdapter->checkVersion;
                    if (!$result->success) break;

                    if (isset($result->object) && isset($result->object->result)) {
                        /** @var StiResult $result */
                        $result = $result->object->result;
                        $result->handlerVersion = $this->version;
                        $result->adapterVersion = $dataAdapter->version;
                        $result->checkVersion = $dataAdapter->checkVersion;
                    }
                    break;

                case StiEventType::PrepareVariables:
                    $result = $this->invokePrepareVariables($request);
                    break;

                case StiEventType::CreateReport:
                    $result = $this->invokeCreateReport($request);
                    break;

                case StiEventType::OpenReport:
                    $result = $this->invokeOpenReport($request);
                    break;

                case StiEventType::SaveReport:
                    $result = $this->invokeSaveReport($request);
                    break;

                case StiEventType::SaveAsReport:
                    $result = $this->invokeSaveAsReport($request);
                    break;

                case StiEventType::PrintReport:
                    $result = $this->invokePrintReport($request);
                    break;

                case StiEventType::BeginExportReport:
                    $result = $this->invokeBeginExportReport($request);
                    break;

                case StiEventType::EndExportReport:
                    $result = $this->invokeEndExportReport($request);
                    break;

                case StiEventType::EmailReport:
                    $result = $this->invokeEmailReport($request);
                    break;

                default:
                    $result = StiResult::error("Unknown event [$request->event]");
                    break;
            }
        }

        if ($response)
            StiResponse::json($result, $request->encode);

        return $result;
    }
}