<?php

	function sti_odbc_parse_connection_string($connection_string)
	{
		$info = Array(
			"dsn" => "",
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
					case "uid":
					case "user":
					case "user id":
						$info["user_id"] = $value;
						break;
					
					case "pwd":
					case "password":
						$info["password"] = $value;
						break;
					
					default:
						if ($info["dsn"] != "") $info["dsn"] .= ";";
						$info["dsn"] .= $parameter;
						break;
				}
			}
		}
		
		return $info;
	}

	function sti_odbc_get_column_type($type)
	{
		switch (strtolower($type))
		{
			case "counter":
			case "integer":
			case "byte":
				return "int";
			
			case "currency":
			case "double":
				return "decimal";
			
			case "bit":
				return "boolean";
			
			case "longbinary":
				return "base64Binary";
			
			case "datetime":
				return "dateTime";
		}
		
		return "string";
	}

	function sti_odbc_test_connection($connection_string)
	{
		$info = sti_odbc_parse_connection_string($connection_string);
		$link = odbc_connect($info["dsn"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host");
		odbc_close($link);
		
		return "Successfull";
	}

	function sti_odbc_get_columns($connection_string, $query)
	{
		$info = sti_odbc_parse_connection_string($connection_string);
		$link = odbc_connect($info["dsn"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host");
		
		$query = sti_parse_query_parameters($query);
		$result = odbc_exec($link, $query) or die("ServerError:Data not found");
		
		odbc_fetch_row($result);
		$count = odbc_num_fields($result);
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables>\n<RetrieveColumns>";
		
		for($fid = 1; $fid <= $count; $fid++)
		{
			$field_name = odbc_field_name($result, $fid);
			$field_type = odbc_field_type($result, $fid);
			$column_type = sti_odbc_get_column_type($field_type);
			$xml_output .= "<$field_name type=\"$column_type\" />";
		}
		
		$xml_output .= "</RetrieveColumns></Tables>";
		
		odbc_free_result($result);
		odbc_close($link);
		
		return $xml_output;
	}

	function sti_odbc_get_data($connection_string, $data_source_name, $query)
	{
		$info = sti_odbc_parse_connection_string($connection_string);
		$link = odbc_connect($info["dsn"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host");
		
		$query = sti_parse_query_parameters($query);
		$result = odbc_exec($link, $query) or die("ServerError:Data not found");
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
		
		odbc_fetch_row($result, 0);
		$count = odbc_num_fields($result);
		
		for($fid = 1; $fid <= $count; $fid++)
		{
			$columns[$fid] = odbc_field_name($result, $fid);
		}
		
		while (odbc_fetch_row($result))
		{
			$xml_output .= "<$data_source_name>";
			for($fid = 1; $fid <= $count; $fid++)
			{
				$column = $columns[$fid];
				$value = odbc_result($result, $column);
				
				$field_type = odbc_field_type($result, $fid);
				$column_type = sti_odbc_get_column_type($field_type);
				
				if ($column_type == "base64Binary") $value = base64_encode($value);
				else
				{
					$value = str_replace("&", "&amp;", $value);
					$value = str_replace("<", "&lt;", $value);
					$value = str_replace(">", "&gt;", $value);
				}
				
				$xml_output .= "<$column>$value</$column>";
			}
			$xml_output .= "</$data_source_name>";
		}
		
		$xml_output .= "</Database>";
		
		odbc_free_result($result);
		odbc_close($link);
		
		return $xml_output;
	}

?>