<?php
	function sti_oracle_parse_connection_string($connection_string)
	{
		$info = Array(
			"database" => "",
			"user_id" => "",
			"password" => "",
			"charset" => "AL32UTF8",
			"privilege" => ""
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
					
					case "dba privilege":
					case "privilege":
						$value = strtolower($value);
						$info["privilege"] = OCI_DEFAULT;
						if ($value == "sysoper" || $value == "oci_sysoper") $info["privilege"] = OCI_SYSOPER;
						if ($value == "sysdba" || $value == "oci_sysdba") $info["privilege"] = OCI_SYSDBA;
						break;
				}
			}
		}
		
		return $info;
	}

	function sti_oracle_get_column_type($type, $precision)
	{
		switch (strtolower($type))
		{
			case "char":
			case "varchar":
			case "varchar2":
				return "string";
			
			case "long":
				return "int";
				
			case "number":
				if ($precision > 38) return "decimal";
				return "int";
			
			case "date":
			case "timestamp":
				return "dateTime";
		}
		
		return "base64Binary";
	}

	function sti_oracle_test_connection($connection_string)
	{
		$info = sti_oracle_parse_connection_string($connection_string);
		if ($info["privilege"] == "") $conn = oci_connect($info["user_id"], $info["password"], $info["database"], $info["charset"]);
		else $conn = oci_pconnect($info["user_id"], $info["password"], $info["database"], $info["charset"], $info["privilege"]);
		
		if ($conn === false)
		{
			$err = ocierror();
			return "ServerError:Could not connect {$err['message']}";
		}
		
		return "Successfull";
	}

	function sti_oracle_get_columns($connection_string, $query)
	{
		$info = sti_oracle_parse_connection_string($connection_string);
		if ($info["privilege"] == "") $conn = oci_connect($info["user_id"], $info["password"], $info["database"], $info["charset"]);
		else $conn = oci_pconnect($info["user_id"], $info["password"], $info["database"], $info["charset"], $info["privilege"]);
		
		if ($conn === false)
		{
			$err = ocierror();
			return "ServerError:Could not connect {$err['message']}";
		}
		
		$query = sti_parse_query_parameters($query);
		$stmt = oci_parse($conn, $query);
		
		if ($stmt === false)
		{
			$err = oci_error($conn);
			return "ServerError:Parse Error {$err['message']}";
		}
		else
		{
			if (strpos($query, "cursor") !== false)
			{
				$curs = oci_new_cursor($conn);
				oci_bind_by_name($stmt, "cursor", $curs, -1, OCI_B_CURSOR);
			}
			
			if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS) === true)
			{
				if (isset($curs)) 
				{
					if (oci_execute($curs, OCI_DEFAULT) === false)
					{
						$err = oci_error();
						return "ServerError:Cursor Execute Error {$err['message']}";
					}
					
					$stmt_curs = $curs;
				}
				else $stmt_curs = $stmt;
				
				$ncols = oci_num_fields($stmt_curs);
				$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Tables><RetrieveColumns>";
				for ($i = 1; $i <= $ncols; $i++)
				{
					$column_name  = oci_field_name($stmt_curs, $i);
					$column_type  = oci_field_type($stmt_curs, $i);
					$column_precision  = oci_field_precision($stmt_curs, $i);
					$column_type = sti_oracle_get_column_type($column_type, $column_precision);
					$xml_output .= "<$column_name type=\"$column_type\" />";
				}
				$xml_output .= "</RetrieveColumns></Tables>";
			}
			else
			{
				$err = oci_error($conn);
				return "ServerError:Execute Error {$err['message']}";
			}
			
			if (isset($curs)) oci_free_statement($curs);
			oci_free_statement($stmt);
		}
		
		return $xml_output;
	}

	function sti_oracle_get_data($connection_string, $data_source_name, $query)
	{
		$info = sti_oracle_parse_connection_string($connection_string);
		if ($info["privilege"] == "") $conn = oci_connect($info["user_id"], $info["password"], $info["database"], $info["charset"]);
		else $conn = oci_pconnect($info["user_id"], $info["password"], $info["database"], $info["charset"], $info["privilege"]);
		
		if ($conn === false)
		{
			$err = ocierror();
			return "ServerError:Could not connect {$err['message']}";
		}
		
		$query = sti_parse_query_parameters($query);
		$stmt = oci_parse($conn, $query);
		
		if ($stmt === false)
		{
			$err = oci_error($conn);
			return "ServerError:Parse Error {$err['message']}";
		}
		else
		{
			if (strpos($query, "cursor") !== false)
			{
				$curs = oci_new_cursor($conn);
				oci_bind_by_name($stmt, "cursor", $curs, -1, OCI_B_CURSOR);
			}
			
			if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS) === true)
			{
				if (isset($curs))
				{
					if (oci_execute($curs, OCI_DEFAULT) === false)
					{
						$err = oci_error();
						return "ServerError:Cursor Execute Error {$err['message']}";
					}
					
					$stmt_curs = $curs;
				}
				else $stmt_curs = $stmt;
				
				$ncols = oci_num_fields($stmt_curs);
				
				$column_names = Array();
				$column_types = Array();
				for ($i = 1; $i <= $ncols; $i++)
				{
					$column_names[] = oci_field_name($stmt_curs, $i);
					$column_type  = oci_field_type($stmt_curs, $i);
					$column_precision  = oci_field_precision($stmt_curs, $i);
					$column_types[] = sti_oracle_get_column_type($column_type, $column_precision);
				}
				
				$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Database>";
				
				oci_fetch_all($stmt_curs, $data);
				for ($i = 0; $i < count($data[$column_names[0]]); $i++)
				{
					$xml_output .= "<$data_source_name>";
					for($j = 0; $j < count($column_names); $j++)
					{
						$value = $data[$column_names[$j]][$i];
						if ($column_types[$j] == "base64Binary") $value = base64_encode($value);
						if ($column_types[$j] == "dateTime" && strlen($value) > 0 && strpos($value, ".") > 0) {
							$values = preg_split("/\./", $value);
							if (count($values) >= 3) {
								if (strlen($values[2]) > 2) $value = $values[2].'-'.$values[1].'-'.$values[0];
								else $value = ((int)$values[2] >= 30 ? '19'.$values[2] : '20'.$values[2]).'-'.$values[1].'-'.$values[0];
							}
						} else {
							$value = str_replace("&", "&amp;", $value);
							$value = str_replace("<", "&lt;", $value);
							$value = str_replace(">", "&gt;", $value);
						}
						$xml_output .= "<{$column_names[$j]}>{$value}</{$column_names[$j]}>";
					}
					$xml_output .= "</$data_source_name>";
				}
				$xml_output .= "</Database>";
				
				if (isset($curs)) oci_free_statement($curs);
				oci_free_statement($stmt);
			}
			else
			{
				$err = ocierror($stmt);
				return "ServerError:Execute Error {$err['message']} $query";
			}
		}
		return $xml_output;
	}

?>
