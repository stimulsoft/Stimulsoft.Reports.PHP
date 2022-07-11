<?php

abstract class StiSqlAdapter {
    public $version = '2022.3.2';
    public $checkVersion = true;

    protected $info = null;
    protected $link = null;

    protected function getLastNativeError() {
        return array('code' => null, 'error' => null);
    }

    protected function getLastErrorResult() {
        $message = 'Unknown';

        if ($this->info->isPdo) {
            $info = $this->link->errorInfo();
            $code = $info[0];
            if (count($info) >= 3) $message = $info[2];
        } else {
            $nativeError = $this->getLastNativeError();
            $code = $nativeError['code'];
            $error = $nativeError['error'];
            if ($error) $message = $error;
        }

        if ($code == 0) return StiResult::error($message);
        return StiResult::error("[$code] $message");
    }

    protected function connectViaNativeDriver() {
        return StiResult::success();
    }

    protected function connect() {
        if ($this->info->isPdo) {
            try {
                $this->link = new PDO($this->info->dsn, $this->info->userId, $this->info->password);
            }
            catch (PDOException $e) {
                $code = $e->getCode();
                $message = $e->getMessage();
                return StiResult::error("[$code] $message");
            }

            return StiResult::success();
        }

       return $this->connectViaNativeDriver();
    }

    protected function closeNativeConnection() {
    }

    protected function disconnect() {
        if (!$this->link) return;
        if (!$this->info->isPdo) $this->closeNativeConnection();
        $this->link = null;
    }

    abstract protected function getNewConnectionInfo($connectionString);

    protected function processConnectionParameter($info, $name, $value, $parameter) {
    }

    public function parse($connectionString) {
        $connectionString = trim($connectionString);
        $info = $this->getNewConnectionInfo($connectionString);

        $parameters = explode(';', $connectionString);
        foreach ($parameters as $parameter) {
            if (mb_strpos($parameter, '=') < 1) {
                if ($info->isPdo) $info->dsn .= $parameter.';';
                continue;
            }

            $pos = mb_strpos($parameter, '=');
            $name = mb_strtolower(trim(mb_substr($parameter, 0, $pos)));
            $value = trim(mb_substr($parameter, $pos + 1));

            $this->processConnectionParameter($info, $name, $value, $parameter);
        }

        if (mb_strlen($info->dsn) > 0 && mb_substr($info->dsn, mb_strlen($info->dsn) - 1) == ';')
            $info->dsn = mb_substr($info->dsn, 0, mb_strlen($info->dsn) - 1);

        $this->info = $info;
    }

    public function test() {
        $result = $this->connect();
        if ($result->success) $this->disconnect();
        return $result;
    }

    abstract public function getValue($type, $value);
}
