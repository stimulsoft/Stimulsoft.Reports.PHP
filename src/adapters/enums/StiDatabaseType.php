<?php

namespace Stimulsoft;

class StiDatabaseType
{
    const MySQL = 'MySQL';
    const MSSQL = 'MS SQL';
    const PostgreSQL = 'PostgreSQL';
    const Firebird = 'Firebird';
    const Oracle = 'Oracle';
    const ODBC = 'ODBC';
    const MongoDB = 'MongoDB';

    public static function getTypes() {
        $reflectionClass = new \ReflectionClass('\Stimulsoft\StiDatabaseType');
        $databases = $reflectionClass->getConstants();
        return array_values($databases);
    }
}