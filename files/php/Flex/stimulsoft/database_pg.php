<?php

	function sti_pg_parse_connection_string($connection_string)
	{
		$info = Array(
			"host" => "",
			"port" => "5432",
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

	function sti_pg_get_column_type($type)
	{
		switch (strtolower($type))
		{
			case "varchar":
			case "bpchar":
			case "macaddr":
			case "inet":
			case "money":
			case "text":
			case "interval":
			case "bit":
			case "varbit":
			case "bytea":
				return "string";
			
			case "bool":
				return "boolean";
			
			case "int8":
			case "int4":
			case "int2":
				return "int";
			
			case "date":
			case "time":
			case "timetz":
			case "timestamp":
			case "timestamptz":
				return "dateTime";
		}
		
		return "decimal";
	}

	function sti_pg_test_connection($connection_string)
	{
		$info = sti_pg_parse_connection_string($connection_string);
		$link = pg_connect("host=".$info["host"]." port=".$info["port"]." dbname=".$info["database"]." user=".$info["user_id"]." password=".$info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		pg_close($link);
		
		return "Successfull";
	}

	function sti_pg_get_columns($connection_string, $query)
	{
		$info = sti_pg_parse_connection_string($connection_string);
		$link = pg_connect("host=".$info["host"]." port=".$info["port"]." dbname=".$info["database"]." user=".$info["user_id"]." password=".$info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		pg_set_client_encoding($link, $info["charset"]);
		
		$query = sti_parse_query_parameters($query);
		$result = pg_query($link, $query) or die("ServerError:Data not found");
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables><RetrieveColumns>";
		
		$count = pg_num_fields($result);
		for ($fid = 0; $fid < $count; $fid++)
		{
			$field_name = pg_field_name($result, $fid);
			$field_type = pg_field_type($result, $fid);
			$column_type = sti_pg_get_column_type($field_type);
			$xml_output .= "<$field_name type=\"$column_type\" />";
		}
		
		$xml_output .= "</RetrieveColumns></Tables>";
		
		pg_free_result($result);
		pg_close($link);
		
		return $xml_output;
	}

	function sti_pg_get_data($connection_string, $data_source_name, $query)
	{
		$info = sti_pg_parse_connection_string($connection_string);
		$link = pg_connect("host=".$info["host"]." port=".$info["port"]." dbname=".$info["database"]." user=".$info["user_id"]." password=".$info["password"]) or die("ServerError:Could not connect to host '".$info["host"]."', database '".$info["database"]."'");
		pg_set_client_encoding($link, $info["charset"]);
		
		$query = sti_parse_query_parameters($query);
		$result = pg_query($link, $query) or die("ServerError:Data not found");
		
		$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
		
		$count = pg_num_fields($result);
		for ($fid = 0; $fid < $count; $fid++)
		{
			$columns[$fid] = pg_field_name($result, $fid);
		}
		
		while ($row = pg_fetch_assoc($result))
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
		
		pg_free_result($result);
		pg_close($link);
		
		return $xml_output;
	}

?>