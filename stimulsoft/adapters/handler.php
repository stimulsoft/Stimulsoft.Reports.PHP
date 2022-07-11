<?php

require_once 'classes.php';

class StiDataAdapterRequest {
    public $command = null;
    public $connectionString = null;
    public $queryString = null;
    public $database = null;
    public $dataSource = null;
    public $connection = null;
    public $timeout = null;

    public function parse() {
        $input = file_get_contents('php://input');

        if (strlen($input) > 0 && mb_substr($input, 0, 1) != '{') {
            $input = base64_decode(str_rot13($input));
            StiDataAdapterResponse::$encodeResponse = true;
        }

        $obj = json_decode($input);
        if ($obj == null) {
            $message = 'JSON parser error #'.json_last_error();
            if (function_exists('json_last_error_msg'))
                $message .= ' ('.json_last_error_msg().')';

            return StiResult::error($message);
        }

        if (isset($obj->command)) $this->command = $obj->command;
        if ($this->command != 'GetSupportedAdapters') {
            if ($this->command != 'TestConnection' && $this->command != 'ExecuteQuery')
                return StiResult::error('Unknown command ['.$this->command.']');

            if (isset($obj->connectionString)) $this->connectionString = $obj->connectionString;
            if (isset($obj->queryString)) $this->queryString = $obj->queryString;
            if (isset($obj->database)) $this->database = $obj->database;
            if (isset($obj->dataSource)) $this->dataSource = $obj->dataSource;
            if (isset($obj->connection)) $this->connection = $obj->connection;
            if (isset($obj->timeout)) $this->timeout = $obj->timeout;
        }

        return StiResult::success(null, $this);
    }
}

class StiDataAdapterResponse extends StiResponse {
    public static $encodeResponse = false;

    protected static function serializeResult($result) {
        $result = parent::serializeResult($result);
        return StiDataAdapterResponse::$encodeResponse ? str_rot13(base64_encode($result)) : $result;
    }
}

class StiDataAdaptersHandler extends StiBaseHandler {
    public function process($response = true) {
        $request = new StiDataAdapterRequest();
        $result = $request->parse();
        if ($result->success) {
            if ($result->object->command == 'GetSupportedAdapters') {
                $result = array('success' => true, 'types' => StiDatabaseType::getSupportedDatabases());
            } else {
                $result = $this->getDataAdapter($request);
                $dataAdapter = $result->object;
                $result = $request->command == 'TestConnection'
                    ? $dataAdapter->test()
                    : $dataAdapter->execute($request->queryString);
                $result->handlerVersion = $this->version;
                $result->adapterVersion = $dataAdapter->version;
                $result->checkVersion = $dataAdapter->checkVersion;
            }
        }

        StiDataAdapterResponse::json($result);
    }
}

$handler = new StiDataAdaptersHandler();
$handler->registerErrorHandlers();
$handler->process();
