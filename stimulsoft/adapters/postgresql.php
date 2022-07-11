<?php

require_once 'class.sql_adapter.php';

class StiPostgreSqlAdapter extends StiSqlAdapter {
    protected function getLastNativeError() {
        return array('code' => 0, 'error' => pg_last_error());
    }

    protected function connectViaNativeDriver() {
        if (!function_exists('pg_connect'))
            return StiResult::error('PostgreSQL driver not found. Please configure your PHP server to work with PostgreSQL.');

        $connectionString = "host='".$this->info->host."' port='".$this->info->port."' dbname='".$this->info->database."' user='".$this->info->userId."' password='".$this->info->password."' options='--client_encoding=".$this->info->charset."'";
        $this->link = pg_connect($connectionString);
        if (!$this->link)
            return $this->getLastErrorResult();

        return StiResult::success();
    }

    protected function closeNativeConnection() {
        pg_close($this->link);
    }

    protected function getNewConnectionInfo($connectionString) {
        $info = new stdClass();
        $info->isPdo = mb_strpos($connectionString, 'pgsql:') !== false;
        $info->dsn = '';
        $info->host = '';
        $info->port = 5432;
        $info->database = '';
        $info->userId = '';
        $info->password = '';
        $info->charset = 'utf8';
        return $info;
    }

    protected function processConnectionParameter($info, $name, $value, $parameter) {
        switch ($name)
        {
            case 'server':
            case 'host':
            case 'location':
                $info->host = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'port':
                $info->port = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'database':
            case 'data source':
            case 'dbname':
                $info->database = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'uid':
            case 'user':
            case 'userid':
            case 'user id':
            case 'username':
                $info->userId = $value;
                break;

            case 'pwd':
            case 'password':
                $info->password = $value;
                break;

            case 'charset':
                $info->charset = $value;
                break;

            default:
                if ($info->isPdo && mb_strlen($parameter) > 0) $info->dsn .= $parameter.';';
                break;
        }
    }

	private function parseType($meta) {
		$type = strtolower($this->info->isPdo ? $meta['native_type'] : $meta);
		if (substr($type, 0, 1) == '_') $type = 'array';
		
		switch ($type) {
			case "int":
			case 'int2':
			case 'int4':
			case 'int8':
			case "smallint":
			case "bigint":
			case "tinyint":
			case "integer":
			case 'numeric':
			case "uniqueidentifier":
				return 'int';
			
			case "float":
			case 'float4':
			case 'float8':
			case "real":
			case "double":
			case "decimal":
			case "smallmoney":
			case "money":
				return 'number';
				
			case 'bool':
			case "boolean":
				return 'boolean';
			
			case "abstime":
			case "date":
			case "datetime":
			case "smalldatetime":
			case "timestamp":
				return 'datetime';
			
			case 'timetz':
			case 'timestamptz':
				return 'datetimeoffset';
				
			case 'time':
				return 'time';
			
			case 'bytea':
			case 'array':
				return 'array';
		}
		
		return 'string';
	}

	public function getValue($type, $value) {
		if (is_null($value) || strlen($value) == 0)
			return null;
		
		switch ($type) {
			case 'array':
				return base64_encode($value);
			
			case 'datetime':
				$timestamp = strtotime($value);
				$format = date("Y-m-d\TH:i:s.v", $timestamp);
				if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
				return $format;
			
			case 'datetimeoffset':
				if (strlen($value) <= 15) {
					$offset = substr($value, strpos($value, '+'));
					if (strlen($offset) == 3) $offset = $offset.':00';
					$value = substr($value, 0, strpos($value, '+'));
					$value = '0001-01-01 '.$value;
					$timestamp = strtotime($value);
					$format = date("Y-m-d\TH:i:s.v", $timestamp);
					if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
					return $format.$offset;
				}
				
				$timestamp = strtotime($value);
				$format = gmdate("Y-m-d\TH:i:s.v\Z", $timestamp);
				if (strpos($format, '.v') > 0) $format = gmdate("Y-m-d\TH:i:s.000\Z", $timestamp);
				return $format;
			
			case 'time':
				$timestamp = strtotime($value);
				$format = date("H:i:s.v", $timestamp);
				if (strpos($format, '.v') > 0) $format = date("H:i:s.000", $timestamp);
				return $format;
		}
		
		return $value;
	}

	public function execute($queryString) {
		$result = $this->connect();
		if ($result->success) {
			$query = $this->info->isPdo ? $this->link->query($queryString) : pg_query($this->link, $queryString);
			if (!$query)
				return $this->getLastErrorResult();
			
			$result->types = array();
			$result->columns = array();
			$result->rows = array();
			
			if ($this->info->isPdo) {
				$result->count = $query->columnCount();
				
				for ($i = 0; $i < $result->count; $i++) {
					$meta = $query->getColumnMeta($i);
					$result->columns[] = $meta['name'];
					$result->types[] = $this->parseType($meta);
				}
				
				while ($rowItem = $query->fetch()) {
					$row = array();
					for ($i = 0; $i < $result->count; $i++) {
						$type = count($result->types) >= $i + 1 ? $result->types[$i] : 'string';
						$row[] = $this->getValue($type, $rowItem[$i]);
					}
					$result->rows[] = $row;
				}
			}
			else {
				$result->count = pg_num_fields($query);
				
				for ($i = 0; $i < $result->count; $i++) {
					$result->columns[] = pg_field_name($query, $i);
					$type = pg_field_type($query, $i);
					$result->types[] = $this->parseType($type);
				}
				
				while ($rowItem = pg_fetch_assoc($query)) {
					$row = array();
					foreach ($rowItem as $key => $value) {
						$type = count($result->types) >= count($row) + 1 ? $result->types[count($row)] : 'string';
						$row[] = $this->getValue($type, $value);
					}
					$result->rows[] = $row;
				}
			}
			
			$this->disconnect();
		}

		return $result;
	}
}