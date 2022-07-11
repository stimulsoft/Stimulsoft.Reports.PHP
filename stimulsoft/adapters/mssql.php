<?php
require_once 'class.sql_adapter.php';

class StiMsSqlAdapter extends StiSqlAdapter {
    protected function getLastNativeError() {
        $code = 0;
        if ($this->info->isMicrosoft) {
            if (($errors = sqlsrv_errors()) != null) {
                $error = $errors[count($errors) - 1];
                $code = $error['code'];
                $message = $error['message'];
            }
        } else {
            $error = mssql_get_last_message();
            if ($error) $message = $error;
        }
        return array('code' => $code, 'error' => $error);
    }

    protected function connectViaNativeDriver() {
        if ($this->info->isMicrosoft) {
            if (!function_exists('sqlsrv_connect'))
                return StiResult::error('MS SQL driver not found. Please configure your PHP server to work with MS SQL.');

            sqlsrv_configure('WarningsReturnAsErrors', 0);
            $this->link = sqlsrv_connect(
                $this->info->host,
                array(
                    'UID' => $this->info->userId,
                    'PWD' => $this->info->password,
                    'Database' => $this->info->database,
                    'LoginTimeout' => 10,
                    'ReturnDatesAsStrings' => true,
                    'CharacterSet' => $this->info->charset
                ));

            if (!$this->link)
                return $this->getLastErrorResult();

            return StiResult::success();
        }

        $this->link = mssql_connect($this->info->host, $this->info->userId, $this->info->password);
        if (!$this->link)
            return $this->getLastErrorResult();

        if (!mssql_select_db($this->info->database, $this->link))
            return $this->getLastErrorResult();

        return StiResult::success();
    }

    protected function closeNativeConnection() {
        $this->info->isMicrosoft ? sqlsrv_close($this->link) : mssql_close($this->link);
    }

    protected function getNewConnectionInfo($connectionString) {
        $info = new stdClass();
        $info->isMicrosoft = !function_exists('mssql_connect');
        $info->isPdo = mb_strpos($connectionString, 'sqlsrv:') !== false;
        $info->dsn = '';
        $info->host = '';
        $info->database = '';
        $info->userId = '';
        $info->password = '';
        $info->charset = 'UTF-8';
        return $info;
    }

    protected function processConnectionParameter($info, $name, $value, $parameter) {
        switch ($name) {
            case 'server':
            case 'data source':
                $info->host = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'database':
            case 'initial catalog':
            case 'dbname':
                $info->database = $value;
                if ($info->isPdo) $info->dsn .= $parameter.';';
                break;

            case 'uid':
            case 'user':
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

	private function getStringType($type) {
		switch ($type) {
			case -6:
			case -5:
			case 4:
			case 5:
				return 'int';
			
			case 2:
			case 3:
			case 6:
			case 7:
				return 'decimal';
				
			case -7:
				return 'bit';
			
			case 91:
			case 93:
				return 'datetime';
				
			case -155:
				return 'datetimeoffset';
				
			case -154:
				return 'time';
			
			case -152:
			case -11:
			case -10:
			case -9:
			case -8:
			case -2:
			case -1:
			case 1:
			case 12:
				return 'string';
			
			case -151:
				return 'geometry'; // 'udt'
			
			case -150:
			case -4:
			case -3:
			case -2:
				return 'binary';
		}
		
		return 'string';
	}
	
	private function parseType($meta) {
		$type = 'string';
		$length = 0;
		
		if ($this->info->isPdo) {
			$type = $meta['sqlsrv:decl_type'];
			$length = $meta['len'];
		}
		else {
			$type = $this->getStringType($meta['Type']);
			$length = $meta['Size'];
		}
		
		switch ($type) {
			case 'bigint':
			case 'int':
			case 'smallint':
			case 'tinyint':
				return 'int';
			
			case 'decimal':
			case 'float':
			case 'money':
			case 'numeric':
			case 'real':
			case 'smallmoney':
				return 'number';
			
			case 'bit':
				return 'boolean';
			
			case 'char':
			case 'nchar':
			case 'ntext':
			case 'nvarchar':
			case 'text':
			case 'timestamp':
			case 'uniqueidentifier':
			case 'varchar':
			case 'xml':
				return 'string';
			
			case 'date':
			case 'datetime':
			case 'datetime2':
			case 'smalldatetime':
				return 'datetime';
			
			case 'datetimeoffset':
				return 'datetimeoffset';
			
			case 'time':
				return 'time';
			
			case 'binary':
			case 'image':
			case 'sql_variant':
			case 'varbinary':
			case 'cursor':
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
				$offset = substr($value, strpos($value, '+'));
				$value = substr($value, 0, strpos($value, '+'));
				$timestamp = strtotime($value);
				$format = date("Y-m-d\TH:i:s.v", $timestamp);
				if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
				return $format.$offset;
			
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
			$query = $this->info->isPdo 
				? $this->link->query($queryString) 
				: ($this->info->isMicrosoft 
					? sqlsrv_query($this->link, $queryString) 
					: mssql_query($queryString, $this->link)
				);
			
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
				if ($this->info->isMicrosoft) {
					foreach (sqlsrv_field_metadata($query) as $meta) {
						$result->columns[] = $meta['Name'];
						$result->types[] = $this->parseType($meta);
					}
				}
				
				$isColumnsEmpty = count($result->columns) == 0;
				while ($rowItem = $this->info->isMicrosoft ? sqlsrv_fetch_array($query, $isColumnsEmpty ? SQLSRV_FETCH_ASSOC : SQLSRV_FETCH_NUMERIC) : mssql_fetch_assoc($query)) {
					$row = array();
					foreach ($rowItem as $key => $value) {
						if ($isColumnsEmpty && count($result->columns) < count($rowItem)) $result->columns[] = $key;
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