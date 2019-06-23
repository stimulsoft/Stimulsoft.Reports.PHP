<?php

	function sti_firebird_parse_connection_string($connection_string)
	{
		$info = Array(
			"host" => "",
			"port" => "3050",
			"database" => "",
			"user_id" => "",
			"password" => ""
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
					case "user id":
						$info["user_id"] = $value;
						break;
					
					case "pwd":
					case "password":
						$info["password"] = $value;
						break;
				}
			}
		}
		
		return $info;
	}

	function sti_firebird_get_column_type($type)
	{
		switch (strtolower($type))
		{
			case "decimal":
			case "float":
			case "double precision":
			case "numeric":
				return "decimal";
			
			// Only from firebird 3.0 version (future version)
			// Before you had to use Char (1)('T' or 'F') or smallint (0 or 1)
			case "boolean":
				return "boolean";
			
			case "smallint":
			case "integer":
			case "int64":
				return "int";
			
			case "timestamp":
			case "time":
			case "date":
				return "dateTime";
		}
		
		return "string";
	}

	function sti_firebird_test_connection($connection_string)
	{
		$info = sti_firebird_parse_connection_string($connection_string);
		$link = ibase_connect($info["host"].":".$info["database"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		ibase_close($link);
		
		return "Successfull";
	}

	function sti_firebird_get_columns($connection_string, $query)
	{
		$info = sti_firebird_parse_connection_string($connection_string);
		$link = ibase_connect($info["host"].":".$info["database"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		
		$query = sti_parse_query_parameters($query);
		$result = ibase_query($link, $query) or die("ServerError:Data not found");
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables><RetrieveColumns>";
		
		$count = ibase_num_fields($result);
		
		for ($fid = 0; $fid < $count; $fid++)
		{
			$field_info = ibase_field_info($result, $fid);
			$field_name = $field_info['alias'];
			$field_type = $field_info['type'];
			$column_type = sti_firebird_get_column_type($field_type);
			$xml_output .= "<$field_name type=\"$column_type\" />";
		}
		
		$xml_output .= "</RetrieveColumns></Tables>";
		
		ibase_free_result($result);
		ibase_close($link);
		
		return $xml_output;
	}

	function sti_firebird_get_data($connection_string, $data_source_name, $query)
	{
		$info = sti_firebird_parse_connection_string($connection_string);
		$link = ibase_connect($info["host"].":".$info["database"], $info["user_id"], $info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		
		$query = sti_parse_query_parameters($query);
		$result = ibase_query($link, $query) or die("ServerError:Data not found");
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
		
		$count = ibase_num_fields($result);
		for ($fid = 0; $fid < $count; $fid++)
		{
			$field_info = ibase_field_info($result, $fid); 
			$columns[$fid] = $field_info['alias'];
		}
		
		while ($row = ibase_fetch_assoc($result))
		{
			$xml_output .= "<$data_source_name>";
			foreach($columns as $column)
			{
				$value = $row[$column];
				$value = str_replace("&", "&amp;", $value);
				$value = str_replace("<", "&lt;", $value);
				$value = str_replace(">", "&gt;", $value);
				
				$xml_output .= "<$column>$value</$column>";
			}
			$xml_output .= "</$data_source_name>";
		}
		
		$xml_output .= "</Database>";
		
		ibase_free_result($result);
		ibase_close($link);
		
		return $xml_output;
	}

?>