<?php

	function sti_mysql_parse_connection_string($connection_string)
	{
		$info = Array(
			"host" => "",
			"port" => "3306",
			"database" => "",
			"user_id" => "",
			"password" => "",
			"charset" => "utf8"
		);
		
		$parameters = explode(";", $connection_string);
		foreach($parameters as $parameter)
		{
			if (strpos($parameter, "=") < 1) continue;
			
			$parts = explode("=", $parameter);
			$name = strtolower(trim($parts[0]));
			if (count($parts) > 1) $value = $parts[1];
			
			if (isset($value))
			{
				switch ($name)
				{
					case "server":
					case "host":
					case "location":
						$info["host"] = $value;
						break;
					
					case "port":
						$info["port"] = $value;
						break;
					
					case "database":
					case "data source":
						$info["database"] = $value;
						break;
					
					case "uid":
					case "user":
					case "username":
					case "userid":
					case "user id":
						$info["user_id"] = $value;
						break;
					
					case "pwd":
					case "password":
						$info["password"] = $value;
						break;
					
					case "charset":
						$info["charset"] = $value;
						break;
				}
			}
		}
		
		return $info;
	}

	function sti_mysql_get_column_type($fields, $column)
	{
		$column_type = $column->type;
		if (strlen($column->table) > 0)
		{
			foreach ($fields as $field)
			{
				if ($field->table == $column->table && $field->name == $column->name)
				{
					$column_type = $field->type;
					break;
				}
			}
		}
		
		if (strpos($column_type, "(") > 0) $column_type = substr($column_type, 0, strpos($column_type, "("));
		$column_type = strtolower($column_type);
		
		switch ($column_type)
		{
			case "int":
			case "tinyint":
			case "smallint":
			case "mediumint":
			case "bigint":
				return "int";
			
			case "decimal":
			case "float":
			case "double":
			case "real":
				return "decimal";
			
			case "bit":
				return "boolean";
			
			case "date":
			case "time":
			case "datetime":
			case "timestamp":
			case "year":
				return "dateTime";
			
			case "blob":
			case "tinyblob":
			case "mediumblob":
			case "longblob":
			case "binary":
			case "varbinary":
				return "base64Binary";
		}
		
		return "string";
	}

	function sti_mysql_test_connection($connection_string)
	{
		$info = sti_mysql_parse_connection_string($connection_string);
		$link = mysqli_connect($info["host"], $info["user_id"], $info["password"], $info["database"], $info["port"]) or die("ServerError:Could not connect to host '".$info["host"]."'");
		mysqli_close($link);
		
		return "Successfull";
	}

	function sti_mysql_get_columns($connection_string, $query)
	{
		$info = sti_mysql_parse_connection_string($connection_string);
		$link = mysqli_connect($info["host"], $info["user_id"], $info["password"], $info["database"], $info["port"]) or die("ServerError:Could not connect to host '".$info["host"]."'");
		mysqli_set_charset($link, $info["charset"]);
		mysqli_select_db($link, $info["database"]) or die("ServerError:Could not find database '".$info["database"]."'");
		
		$query = sti_parse_query_parameters($query);
		$result = mysqli_query($link, $query) or die("ServerError:".mysqli_error($link));
		
		$columns = Array();
		$tables = Array();
		while ($column = mysqli_fetch_field($result))
		{
			array_push($columns, $column);
			if (strlen($column->table) > 0) $tables[$column->table] = $column->table;
		}
		
		mysqli_free_result($result);
		
		$fields = Array();
		foreach ($tables as $table)
		{
			$result = mysqli_query($link, "show columns from $table");
			if ($result !== false)
			{
				while ($column = mysqli_fetch_array($result))
				{
					$field = (object)null;
					$field->table = $table;
					$field->name = $column["Field"];
					$field->type = $column["Type"];
					array_push($fields, $field);
					unset($field);
				}
				mysqli_free_result($result);
			}
		}
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables><RetrieveColumns>";
		
		foreach ($columns as $column)
		{
			$column_type = sti_mysql_get_column_type($fields, $column);
			$xml_output .= "<$column->name type=\"$column_type\" />";
		}
		
		$xml_output .= "</RetrieveColumns></Tables>";
		
		mysqli_close($link);
		
		return $xml_output;
	}

	function sti_mysql_get_data($connection_string, $data_source_name, $query)
	{
		$info = sti_mysql_parse_connection_string($connection_string);
		$link = mysqli_connect($info["host"], $info["user_id"], $info["password"], $info["database"], $info["port"]) or die("ServerError:Could not connect to host '".$info["host"]."'");
		mysqli_set_charset($link, $info["charset"]);
		mysqli_select_db($link, $info["database"]) or die("ServerError:Could not find database '".$info["database"]."'");
		
		$query = sti_parse_query_parameters($query);
		$query_result = mysqli_query($link, $query) or die("ServerError:".mysqli_error($link));
		
		$columns = Array();
		$tables = Array();
		while ($column = mysqli_fetch_field($query_result))
		{
			array_push($columns, $column);
			if (strlen($column->table) > 0) $tables[$column->table] = $column->table;
		}
		
		$fields = Array();
		foreach ($tables as $table)
		{
			$result = mysqli_query($link, "show columns from $table");
			if ($result !== false)
			{
				while ($column = mysqli_fetch_array($result))
				{
					$field = (object)null;
					$field->table = $table;
					$field->name = $column["Field"];
					$field->type = $column["Type"];
					array_push($fields, $field);
					unset($field);
				}
				mysqli_free_result($result);
			}
		}
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
		
		while ($row = mysqli_fetch_assoc($query_result))
		{
			$xml_output .= "<$data_source_name>";
			foreach ($columns as $column)
			{
				$value = $row[$column->name];
				if ($value !== null) {
					$column_type = sti_mysql_get_column_type($fields, $column);
					
					if ($column_type == "base64Binary") $value = base64_encode($value);
					else if ($column_type == "boolean")
					{
						if ($value != 0) $value = "true";
						else $value = "false";
					}
					else
					{
						$value = str_replace("&", "&amp;", $value);
						$value = str_replace("<", "&lt;", $value);
						$value = str_replace(">", "&gt;", $value);
					}
					
					$xml_output .= "<$column->name>$value</$column->name>";
				}
			}
			$xml_output .= "</$data_source_name>";
		}
		
		$xml_output .= "</Database>";
		
		mysqli_free_result($query_result);
		mysqli_close($link);
		
		return $xml_output;
	}

	if (function_exists('mysqli_set_charset') === false)
	{
		function mysqli_set_charset($link_identifier, $charset)
		{
			if ($link_identifier == null) return mysqli_query('SET NAMES "'.$charset.'"');
			else return mysqli_query($link_identifier, 'SET NAMES "'.$charset.'"');
		}
	}
?>