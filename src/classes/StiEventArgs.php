<?php

namespace Stimulsoft;

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