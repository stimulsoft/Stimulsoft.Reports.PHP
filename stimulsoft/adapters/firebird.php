<?php
require_once 'class.sql_adapter.php';

class StiFirebirdAdapter extends StiSqlAdapter {
    protected function getLastNativeError() {
        return array('code' => ibase_errcode(), 'error' => ibase_errmsg());
    }

    protected function connectViaNativeDriver() {
        if (!function_exists('ibase_connect'))
            return StiResult::error('Firebird driver not found. Please configure your PHP server to work with Firebird.');

        $this->link = ibase_connect($this->info->host.'/'.$this->info->port.':'.$this->info->database, $this->info->userId, $this->info->password, $this->info->charset);
        if (!$this->link)
            return $this->getLastErrorResult();

        return StiResult::success();
    }

    protected function closeNativeConnection() {
        ibase_close($this->link);
    }

    protected function getNewConnectionInfo($connectionString) {
        $info = new stdClass();
        $info->isPdo = mb_strpos($connectionString, 'firebird:') !== false;
        $info->dsn = '';
        $info->host = '';
        $info->port = 3050;
        $info->database = '';
        $info->userId = '';
        $info->password = '';
        $info->charset = 'UTF8';
        return $info;
    }

    protected function processConnectionParameter($info, $name, $value, $parameter) {
        switch ($name)
        {
            case 'server':
            case 'host':
            case 'location':
            case 'datasource':
            case 'data source':
                $info->host = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'port':
                $info->port = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'database':
            case 'dbname':
                $info->database = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'uid':
            case 'user':
            case 'username':
            case 'userid':
            case 'user id':
                $info->userId = $value;
                break;

            case 'pwd':
            case 'password':
                $info->password = $value;
                break;

            case 'charset':
                $info->charset = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            default:
                if ($info->isPdo && mb_strlen($parameter) > 0) $info->dsn .= $parameter.';';
                break;
        }
    }

	private function detectType($value) {
		if (preg_match('~[^\x20-\x7E\t\r\n]~', $value) > 0)
			return 'array';
		
		if (is_numeric($value)) {
			if (strpos($value, '.') !== false) return 'number';
			return 'int';
		}
		
		if (DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false || DateTime::createFromFormat('Y-m-d', $value) !== false || DateTime::createFromFormat('H:i:s', $value) !== false)
			return 'datetime';
		
		if (is_string($value))
			return 'string';
		
		return 'array';
	}
	
	private function parseType($type) {
		switch ($type) {
			case 'SMALLINT':
			case 'INTEGER':
			case 'BIGINT':
				return 'int';
				
			case 'FLOAT':
			case 'DOUBLE PRECISION':
			case 'NUMERIC':
			case 'DECIMAL':
				return 'number';
			
			case 'DATE':
			case 'TIMESTAMP':
				return 'datetime';
				
			case 'TIME':
				return 'time';
			
			case 'CHAR':
			case 'VARCHAR':
				return 'string';
			
			case 'BLOB':
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
			
			case 'time':
				$timestamp = strtotime($value);
				$format = date("H:i:s.v", $timestamp);
				if (strpos($format, '.v') > 0) $format = date("H:i:s.000", $timestamp);
				return $format;
				
			case 'string':
				return utf8_encode($value);
		}
		
		return $value;
	}
	
	public function execute($queryString) {
		$result = $this->connect();
		if ($result->success) {
			$query = $this->info->isPdo ? $this->link->query($queryString) : ibase_query($this->link, $queryString);
			if (!$query)
				return $this->getLastErrorResult();
			
			$result->types = array();
			$result->columns = array();
			$result->rows = array();
			
			if ($this->info->isPdo) {
				$result->count = $query->columnCount();
				
				// PDO Firebird driver does not support getColumnMeta()
				// The type is determined by the first value
				
				while ($rowItem = $query->fetch()) {
					$index = 0;
					$row = array();
					
					foreach ($rowItem as $key => $value) {
						if (is_string($key)) {
							$index++;
							if (count($result->columns) < $index) $result->columns[] = $key;
							if (count($result->types) < $index) $result->types[] = $this->detectType($value);
							$type = $result->types[$index - 1];
							$row[] = $this->getValue($type, $value);
						}
					}
					
					$result->rows[] = $row;
				}
			}
			else {
				$result->count = ibase_num_fields($query);
				
				for ($i = 0; $i < $result->count; $i++) {
					$meta = ibase_field_info($query, $i);
					$result->columns[] = $meta['name'];
					$result->types[] = $this->parseType($meta['type']);
				}
				
				while ($rowItem = ibase_fetch_assoc($query, IBASE_TEXT)) {
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