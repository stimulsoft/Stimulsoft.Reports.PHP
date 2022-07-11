<?php

require_once 'mysql.php';
require_once 'mssql.php';
require_once 'firebird.php';
require_once 'postgresql.php';
require_once 'oracle.php';
require_once 'odbc.php';

class StiDatabaseType {
    const MySQL = "MySQL";
    const MSSQL = "MS SQL";
    const PostgreSQL = "PostgreSQL";
    const Firebird = "Firebird";
    const Oracle = "Oracle";
    const ODBC = "ODBC";

    public static function getSupportedDatabases() {
        return array(self::MySQL, self::MSSQL, self::PostgreSQL, self::Firebird, self::Oracle, self::ODBC);
    }

    public static function getDataAdapter($databaseName) {
        switch ($databaseName) {
            case StiDatabaseType::MySQL: return new StiMySqlAdapter();
            case StiDatabaseType::MSSQL: return new StiMsSqlAdapter();
            case StiDatabaseType::Firebird: return new StiFirebirdAdapter();
            case StiDatabaseType::PostgreSQL: return new StiPostgreSqlAdapter();
            case StiDatabaseType::Oracle: return new StiOracleAdapter();
            case StiDatabaseType::ODBC: return new StiOdbcAdapter();
        }

        return null;
    }
}

class StiResult {
    public $success = true;
    public $notice = null;
    public $object = null;

    public static function success($notice = null, $object = null) {
        $result = new StiResult();
        $result->success = true;
        $result->notice = $notice;
        $result->object = $object;
        return $result;
    }

    public static function error($notice = null) {
        $result = new StiResult();
        $result->success = false;
        $result->notice = $notice;
        return $result;
    }
}

class StiResponse {
    protected static function serializeResult($result) {
        unset($result->object);
        return defined('JSON_UNESCAPED_SLASHES') ? json_encode($result, JSON_UNESCAPED_SLASHES) : json_encode($result);
    }

    public static function json($result, $exit = true) {
        $result = self::serializeResult($result);
        echo $result;
        if ($exit) exit;
    }
}

function stiErrorHandler($errNo, $errStr, $errFile, $errLine) {
    $result = StiResult::error("[$errNo] $errStr ($errFile, Line $errLine)");
    StiResponse::json($result);
}

function stiShutdownFunction() {
    $err = error_get_last();
    if ($err != null && (($err['type'] & E_COMPILE_ERROR) || ($err['type'] & E_ERROR) || ($err['type'] & E_CORE_ERROR) || ($err['type'] & E_RECOVERABLE_ERROR))) {
        $result = StiResult::error("[{$err['type']}] {$err['message']} ({$err['file']}, Line {$err['line']})");
        StiResponse::json($result);
    }
}

class StiBaseHandler {
    protected $version = '2022.3.2';

    public function __construct() {
        // You can configure the security level as you required.
        // By default, is to allow any requests from any domains.
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
        header('Cache-Control: no-cache');
    }

    public function registerErrorHandlers() {
        error_reporting(0);
        set_error_handler('stiErrorHandler');
        register_shutdown_function('stiShutdownFunction');
    }

    public function process($response = true) {
    }

    protected function getDataAdapter($args) {
        $dataAdapter = StiDatabaseType::getDataAdapter($args->database);

        if (isset($dataAdapter)) {
            $dataAdapter->parse($args->connectionString);
            return StiResult::success(null, $dataAdapter);
        }

        return StiResult::error("Unknown database type [".$args->database."]");
    }
}

