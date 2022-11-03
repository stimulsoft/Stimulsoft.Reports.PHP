<?php

namespace Stimulsoft;

use DateTime;

class StiOracleAdapter extends StiSqlAdapter
{
    public $version = '2022.3.3';
    public $checkVersion = true;

    protected $driverName = 'oci';

    protected function getLastErrorResult()
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $code = 0;
        $message = 'Unknown';
        $error = oci_error();
        if ($error !== false) {
            $code = $error['code'];
            $error = $error['message'];
        }

        if ($error) $message = $error;

        return $code == 0 ? StiResult::error($message) : StiResult::error("[$code] $message");
    }

    protected function connect()
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!function_exists('oci_connect'))
            return StiResult::error('Oracle driver not found. Please configure your PHP server to work with Oracle.');

        if ($this->info->privilege == '') $this->link = oci_connect($this->info->userId, $this->info->password, $this->info->database, $this->info->charset);
        else $this->link = oci_pconnect($this->info->userId, $this->info->password, $this->info->database, $this->info->charset, $this->info->privilege);

        if (!$this->link)
            return $this->getLastErrorResult();

        return StiResult::success();
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->link) {
            oci_close($this->link);
            $this->link = null;
        }
    }

    public function parse($connectionString)
    {
        if (parent::parse($connectionString))
            return true;

        $this->info->port = 3306;
        $this->info->charset = 'AL32UTF8';

        $parameterNames = array(
            'database' => ['database', 'data source', 'dbname'],
            'userId' => ['uid', 'user', 'user id'],
            'password' => ['pwd', 'password'],
            'charset' => ['charset']
        );

        return $this->parseParameters($connectionString, $parameterNames);
    }

    protected function parseUnknownParameter($parameter, $name, $value)
    {
        parent::parseUnknownParameter($parameter, $name, $value);

        if ($name == 'dba privilege' || $name == 'privilege') {
            $value = strtolower($value);
            $this->info->privilege = OCI_DEFAULT;
            if ($value == 'sysoper' || $value == 'oci_sysoper') $this->info->privilege = OCI_SYSOPER;
            if ($value == 'sysdba' || $value == 'oci_sysdba') $this->info->privilege = OCI_SYSDBA;
        }
    }

    protected function parseType($meta)
    {
        switch ($meta) {
            case 'SMALLINT':
            case 'INTEGER':
            case 'BIGINT':
                return 'int';

            case 'NUMBER':
                return 'number';

            case 'DATE':
            case 'TIMESTAMP':
                return 'datetime';

            case 'CHAR':
            case 'VARCHAR2':
            case 'INTERVAL DAY TO SECOND':
            case 'INTERVAL YEAR TO MONTH':
            case 'ROWID':
                return 'string';

            case 'BFILE':
            case 'BLOB':
            case 'LONG':
            case 'CLOB':
            case 'RAW':
            case 100:
            case 101:
                return 'array';
        }

        return 'string';
    }

    protected function getValue($type, $value)
    {
        if (is_null($value) || strlen($value) == 0)
            return null;

        switch ($type) {
            case 'array':
                return base64_encode($value);

            case 'datetime':
                $timestamp = DateTime::createFromFormat("d#M#y H#i#s*A", $value);
                if ($timestamp === false) $timestamp = strtotime($value);
                $format = date("Y-m-d\TH:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                return $format;
        }

        return $value;
    }

    protected function executeNative($queryString, $result)
    {
        $query = oci_parse($this->link, $queryString);
        if (!$query || !oci_execute($query))
            return $this->getLastErrorResult();

        $result->count = oci_num_fields($query);

        for ($i = 1; $i <= $result->count; $i++) {
            $name = oci_field_name($query, $i);
            $result->columns[] = $name;

            $type = oci_field_type($query, $i);
            $result->types[] = $this->parseType($type);
        }

        while ($rowItem = oci_fetch_assoc($query)) {
            $row = array();
            foreach ($rowItem as $key => $value) {
                if (count($result->columns) < count($rowItem)) $result->columns[] = $key;
                $type = $result->types[count($row)];
                $row[] = $this->getValue($type, $value);
            }
            $result->rows[] = $row;
        }

        return $result;
    }

    protected function executePDO($queryString, $result)
    {
        // PDO Oracle driver doesn't support getColumnMeta()
        // The type is determined by the first value

        return $this->executePDOv2($queryString, $result);
    }
}