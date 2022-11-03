<?php

namespace Stimulsoft;

class StiConnectionInfo
{
    public $dsn;
    public $host;
    public $port;
    public $database;
    public $userId;
    public $password;
    public $charset;
    public $privilege;
    public $dataPath;
    public $schemaPath;
}

class StiDatabaseType
{
    const MySQL = 'MySQL';
    const MSSQL = 'MS SQL';
    const PostgreSQL = 'PostgreSQL';
    const Firebird = 'Firebird';
    const Oracle = 'Oracle';
    const ODBC = 'ODBC';
}

class StiCommand
{
    const GetSupportedAdapters = 'GetSupportedAdapters';
    const TestConnection = 'TestConnection';
    const ExecuteQuery = 'ExecuteQuery';
}

class StiResult
{
    public $handlerVersion;
    public $adapterVersion;
    public $checkVersion = true;

    public $success = false;
    public $notice;
    public $object;

    public static function success($notice = null, $object = null)
    {
        $result = new StiResult();
        $result->success = true;
        $result->notice = $notice;
        $result->object = $object;

        return $result;
    }

    public static function error($notice = null)
    {
        $result = new StiResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }
}

class StiAdapterResult extends StiResult
{
    public $types;
    public $columns;
    public $rows;

    public static function success($notice = null, $object = null)
    {
        $result = new StiAdapterResult();
        $result->success = true;
        $result->notice = $notice;
        $result->object = $object;

        $result->types = array();
        $result->columns = array();
        $result->rows = array();

        return $result;
    }
}

class StiDataRequest
{
    public $encode = false;
    public $command;
    public $connectionString;
    public $queryString;
    public $database;
    public $dataSource;
    public $connection;
    public $timeout;

    private function populateVars($obj)
    {
        $className = get_class($this);
        $vars = get_class_vars($className);
        foreach ($vars as $name => $value) {
            if (isset($obj->{$name}))
                $this->{$name} = $obj->{$name};
        }
    }

    public function parse()
    {
        $input = file_get_contents('php://input');

        if (strlen($input) > 0 && mb_substr($input, 0, 1) != '{') {
            $input = base64_decode(str_rot13($input));
            $this->encode = true;
        }

        $obj = json_decode($input);
        if ($obj == null) {
            $message = 'JSON parser error #' . json_last_error();
            if (function_exists('json_last_error_msg'))
                $message .= ' (' . json_last_error_msg() . ')';

            return StiResult::error($message);
        }

        $this->populateVars($obj);

        return $this->checkRequestParams($obj);
    }

    protected function checkRequestParams($obj)
    {
        if (isset($obj->command))
            $this->command = $obj->command;

        if ($this->command == StiCommand::GetSupportedAdapters)
            return StiResult::success(null, $this);

        if ($this->command != StiCommand::TestConnection && $this->command != StiCommand::ExecuteQuery)
            return StiResult::error('Unknown command [' . $this->command . ']');

        return StiResult::success(null, $this);
    }
}

class StiResponse
{
    public static function json($result, $encode = false)
    {
        unset($result->object);
        $result = defined('JSON_UNESCAPED_SLASHES') ? json_encode($result, JSON_UNESCAPED_SLASHES) : json_encode($result);
        echo $encode ? str_rot13(base64_encode($result)) : $result;
    }
}