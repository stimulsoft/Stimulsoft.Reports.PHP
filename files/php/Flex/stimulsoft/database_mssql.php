<?php
	function encodeName($name)
	{
		if (!$name) return $name;
		
		$name = preg_replace('/ /i', '_x0020_', $name);
		
		return $name;
	}

	function sti_mssql_parse_connection_string($connection_string)
	{
		$info = Array(
			"host" => "",
			"database" => "",
			"user_id" => "",
			"password" => "",
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
					case "data source":
					case "server":
						$info["host"] = $value;
						break;
					
					case "database":
					case "initial catalog":
						$info["database"] = $value;
						break;
					
					case "uid":
					case "user":
					case "user id":
						$info["user_id"] = $value;
						break;
					
					case "password":
					case "pwd":
						$info["password"] = $value;
						break;
				}
			}
		}
		
		return $info;
	}

	function sti_mssql_get_column_type($type)
	{
		$type = strtolower($type);
		switch ($type)
		{
			case "numeric":
			case "int":
			case 2:
			case 4:
				return "int";
			
			case "datetime":
			case 93:
			case 91:
				return "dateTime";
			
			case "real":
			case "money":
			case 3:
			case 7:
				return "decimal";
				
			case "image":
				return "base64Binary";
		}
		
		return "string";
	}

	function sti_bytes_to_int($value)
	{
		$n = 0;
		$words = unpack('v*', $value);
		foreach ($words as $word) $n = (1 << 16) * $n + $word;
		return $n;
	}

	function sti_mssql_connect($connection_string)
	{
		$info = sti_mssql_parse_connection_string($connection_string);
		if (function_exists("mssql_connect"))
		{
			$link = mssql_connect($info["host"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."'");
			mssql_select_db($info["database"], $link) or die("ServerError:Could not find database '".$info["database"]."'");
		}
		else
		{
			$link = sqlsrv_connect($info["host"], array("UID" => $info["user_id"], "PWD" => $info["password"], "Database" => $info["database"],"ReturnDatesAsStrings" => true)) or die("ServerError:Could not connect to host '".$info["host"]."'");
		}
		return $link;
	}

	function sti_mssql_query($query, $link)
	{
		$query = sti_parse_query_parameters($query);
		if (function_exists("mssql_query")) $result = mssql_query($query, $link) or die("ServerError:Data not found");
		else $result = sqlsrv_query($link, $query) or die("ServerError:Data not found");
		return $result;
	}

	function sti_mssql_test_connection($connection_string)
	{
		$info = sti_mssql_connect($connection_string);
		return "Successfull";
	}

	function sti_mssql_get_columns($connection_string, $query)
	{
		$link = sti_mssql_connect($connection_string);
		$result = sti_mssql_query($query, $link);
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables><RetrieveColumns>";
		
		if (function_exists("mssql_fetch_field"))
		{
			while ($column = mssql_fetch_field($result))
			{
				$column_type = sti_mssql_get_column_type($column->type);
				$columnName = encodeName($column->name);
				$xml_output .= "<$columnName type=\"$column_type\" />";
			}
		}
		else
		{
			foreach (sqlsrv_field_metadata($result) as $field_metadata)
			{
				$column_type = sti_mssql_get_column_type($field_metadata["Type"]);
				$columnName = encodeName($field_metadata["Name"]);
				$xml_output .= "<$columnName type=\"$column_type\" />";
			}
		}
		
		$xml_output .= "</RetrieveColumns></Tables>";
		
		mssql_free_result($result);
		mssql_close($link);
		
		return $xml_output;
	}

	function sti_mssql_get_data($connection_string, $data_source_name, $query)
	{
		$link = sti_mssql_connect($connection_string);
		$result = sti_mssql_query($query, $link);
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
		
		$columns = Array();
		if (function_exists("mssql_fetch_field"))
		{
			while ($column = mssql_fetch_field($result))
			{
				array_push($columns, $column);
			}
		}
		else
		{
			foreach (sqlsrv_field_metadata($result) as $field_metadata)
			{
				array_push($columns, $field_metadata);
			}
		}
		
		if (function_exists("mssql_fetch_assoc"))
		{
			while ($row = mssql_fetch_assoc($result))
			{
				$xml_output .= "<$data_source_name>";
				foreach ($columns as $column)
				{
					$value = $row[$column->name];
					$column_type = sti_mssql_get_column_type($column->type);
					
					if ($column_type == "base64Binary") $value = base64_encode($value);
					else if (ord($value[0]) == 0) $value = sti_bytes_to_int($value);
					else
					{
						$value = str_replace("&", "&amp;", $value);
						$value = str_replace("<", "&lt;", $value);
						$value = str_replace(">", "&gt;", $value);
					}
					
					$columnName = encodeName($column->name);
					$xml_output .= "<$columnName>$value</$columnName>";
				}
				$xml_output .= "</$data_source_name>";
			}
		}
		else
		{
			while ($row = sqlsrv_fetch_array($result))
			{
				$xml_output .= "<$data_source_name>";
				foreach($columns as $column)
				{
					$value = $row[$column["Name"]];
					$column_type = sti_mssql_get_column_type($field_metadata["Type"]);
					
					if ($column_type == "base64Binary") $value = base64_encode($value);
					else if (ord($value[0]) == 0) $value = sti_bytes_to_int($value);
					else
					{
						$value = str_replace("&", "&amp;", $value);
						$value = str_replace("<", "&lt;", $value);
						$value = str_replace(">", "&gt;", $value);
					}
					
					$columnName = encodeName($column["Name"]);
					$xml_output .= "<$columnName>$value</$columnName>";
				}
				$xml_output .= "</$data_source_name>";
			}
		}
		
		$xml_output .= "</Database>";
		
		mssql_free_result($result);
		mssql_close($link);
		
		return $xml_output;
	}

	if (!function_exists("mssql_free_result"))
	{
		function mssql_free_result($result)
		{
			sqlsrv_free_stmt($result);
		}
	}

	if (!function_exists("mssql_close"))
	{
		function mssql_close($link)
		{
			sqlsrv_close($link);
		}
	}

?>