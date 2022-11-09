<?php

namespace Stimulsoft;

use ReflectionClass;
use Stimulsoft\Adapters\StiFirebirdAdapter;
use Stimulsoft\Adapters\StiMsSqlAdapter;
use Stimulsoft\Adapters\StiMySqlAdapter;
use Stimulsoft\Adapters\StiOdbcAdapter;
use Stimulsoft\Adapters\StiOracleAdapter;
use Stimulsoft\Adapters\StiPostgreSqlAdapter;
use Stimulsoft\Enums\StiDataCommand;
use Stimulsoft\Enums\StiDatabaseType;

class StiDataHandler
{
    public $version = '2022.3.3';

    private function stiErrorHandler($errNo, $errStr, $errFile, $errLine)
    {
        $result = StiResult::error("[$errNo] $errStr ($errFile, Line $errLine)");
        StiResponse::json($result);
    }

    private function stiShutdownFunction()
    {
        $err = error_get_last();
        if ($err != null && (($err['type'] & E_COMPILE_ERROR) || ($err['type'] & E_ERROR) || ($err['type'] & E_CORE_ERROR) || ($err['type'] & E_RECOVERABLE_ERROR))) {
            $result = StiResult::error("[{$err['type']}] {$err['message']} ({$err['file']}, Line {$err['line']})");
            StiResponse::json($result);
        }
    }

    public function registerErrorHandlers()
    {
        set_error_handler(array($this, 'stiErrorHandler'));
        register_shutdown_function(array($this, 'stiShutdownFunction'));
        error_reporting(0);
    }

    protected function getDataAdapter($request)
    {
        switch ($request->database) {
            case StiDatabaseType::MySQL:
                $dataAdapter = new StiMySqlAdapter();
                break;

            case StiDatabaseType::MSSQL:
                $dataAdapter = new StiMsSqlAdapter();
                break;

            case StiDatabaseType::Firebird:
                $dataAdapter = new StiFirebirdAdapter();
                break;

            case StiDatabaseType::PostgreSQL:
                $dataAdapter = new StiPostgreSqlAdapter();
                break;

            case StiDatabaseType::Oracle:
                $dataAdapter = new StiOracleAdapter();
                break;

            case StiDatabaseType::ODBC:
                $dataAdapter = new StiOdbcAdapter();
                break;
        }

        if (isset($dataAdapter)) {
            $dataAdapter->parse($request->connectionString);
            return StiResult::success(null, $dataAdapter);
        }

        return StiResult::error("Unknown database type [$request->database]");
    }

    public function process()
    {
        $request = new StiDataRequest();
        $result = $request->parse();
        if ($result->success) {
            if ($result->object->command == StiDataCommand::GetSupportedAdapters) {
                $reflectionClass = new ReflectionClass('\Stimulsoft\StiDatabaseType');
                $databases = $reflectionClass->getConstants();
                $result = array(
                    'success' => true,
                    'types' => array_values($databases)
                );
            }
            else {
                $result = $this->getDataAdapter($request);
                $dataAdapter = $result->object;
                $result = $request->command == StiDataCommand::TestConnection
                    ? $dataAdapter->test()
                    : $dataAdapter->execute($request->queryString);
                $result->handlerVersion = $this->version;
                $result->adapterVersion = $dataAdapter->version;
                $result->checkVersion = $dataAdapter->checkVersion;
            }
        }

        StiResponse::json($result, $request->encode);
    }

    public function __construct()
    {
        $this->registerErrorHandlers();
    }
}
