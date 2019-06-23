<?php

	function sti_get_xml_value($value, $key)
	{
		if (strpos($value, "<".$key.">") < 0 || strpos($value, "</".$key.">") < 0) return null;
		return substr(substr($value, 0, strpos($value, "</".$key.">")), strpos($value, "<".$key.">") + strlen($key) + 2);
	}
	
	function sti_strip($value)
	{
		if (get_magic_quotes_gpc() != 0)
		{
			if (is_array($value))
			{
				if (sti_array_is_associative($value))
				{
					foreach ($value as $k=>$v)
					{
						$tmp_val[$k] = stripslashes($v);
					}
					$value = $tmp_val;
				}
				else
				{
					for ($j = 0; $j < sizeof($value); $j++)
					{
						$value[$j] = stripslashes($value[$j]);
					}
				}
			}
			else $value = stripslashes($value);
		}
		return $value;
	}

	function sti_array_is_associative($array)
	{
		if (is_array($array) && !empty($array))
		{
			for ($iterator = count($array) - 1; $iterator; $iterator--)
			{
				if (!array_key_exists($iterator, $array)) return true;
			}
			return !array_key_exists(0, $array);
		}
		return false;
	}

	function sti_get_parameters($enable_compression)
	{
		$result = "";
		foreach (array_keys($_GET) as $key) $result .= "&$key=".rawurlencode(sti_strip($_GET[$key]));
		foreach (array_keys($_POST) as $key) $result .= "&$key=".rawurlencode(sti_strip($_POST[$key]));
		
		return "?".substr($result, 1)."&stimulsoft_enable_compression=".($enable_compression ? "true" : "false");
	}

	function sti_get_parameter_value($parameter_name, $default_value = "")
	{
		if (isset($_POST[$parameter_name])) return rawurldecode(sti_strip($_POST[$parameter_name]));
		else if (isset($_GET[$parameter_name])) return rawurldecode(sti_strip($_GET[$parameter_name]));
		
		return $default_value;
	}

	function sti_parse_query_parameters($query)
	{
		$bracket_open = "{";
		$bracket_close = "}";
		$result = "";
		while (strpos($query, $bracket_open) !== false)
		{
			$result .= substr($query, 0, strpos($query, $bracket_open));
			$query = substr($query, strpos($query, $bracket_open) + 1);
			$parameter_name = substr($query, 0, strpos($query, $bracket_close));
			$parameter_value = sti_get_parameter_value($parameter_name);
			$result .= sti_get_parameter($parameter_name, $parameter_value);
			$query = substr($query, strpos($query, $bracket_close) + 1);
		}
		
		return $result.$query;
	}

	function sti_create_connection_string($client_data)
	{
		$connection_type = sti_get_xml_value($client_data, "ConnectionType");
		$connection_string = sti_get_xml_value($client_data, "ConnectionString");
		$connection_string = base64_decode($connection_string);
		$connection_string = sti_get_connection_string($connection_type, $connection_string);
		$username = sti_get_xml_value($client_data, "UserName");
		$password = sti_get_xml_value($client_data, "Password");
		
		if ($username != null) $connection_string .= ";user=$username";
		if ($password != null)
		{
			$password = base64_decode($password);
			$connection_string .= ";password=$password";
		}
		
		return $connection_string;
	}


	function sti_client_event_handler($client_key, $report_key, $client_data, $enable_compression)
	{
		switch ($client_key)
		{
			// Loading ViewerFx
			case "ViewerFx":
				if (!isset($report_key)) $report_key = "null";
				$content = file_get_contents("viewer.html");
				$config = file_get_contents("config.xml");
				$content = str_replace("#MARKER_REPORT_PARAMS#", sti_get_parameters($enable_compression), $content);
				$content = str_replace("#MARKER_THEME#", sti_get_xml_value($config, "Theme"), $content);
				return $content;
				break;
			
			// Loading DesignerFx
			case "DesignerFx":
				if (!isset($report_key)) $report_key = "null";
				$content = file_get_contents("designer.html");
				$config = file_get_contents("config.xml");
				$content = str_replace("#MARKER_REPORT_PARAMS#", sti_get_parameters($enable_compression), $content);
				$content = str_replace("#MARKER_THEME#", sti_get_xml_value($config, "Theme"), $content);
				return $content;
				break;
			
			// Loading configuration
			case "LoadConfiguration":
				$config = sti_load_config("config.xml");
				return str_replace("</StiSerializer>", "<LicenseKey>".sti_get_license_key()."</LicenseKey>\n</StiSerializer>", $config);
				break;
			
			// Loading a report when running the viewer or designer
 			case "LoadReport":
				$report = sti_get_report($client_data);
				return empty($report) ? "ServerError:The report is not found." : $report;
				break;
			
			// Loading a report by the specified URL
			// Used for debug. Does not work in the released version
			case "LoadReportFile":
				return file_get_contents($client_data);
				break;
			
			// Loading the requested localization file
			case "LoadLanguage":
				$localization_directory = sti_get_localization_directory();
				return sti_load_localization_file($localization_directory."/".$client_data);
				break;
			
			// Saving a report
			case "SaveReport":
				$report = base64_decode(sti_get_xml_value($client_data, "Report"));
				$report_key = sti_get_xml_value($client_data, "ReportKey");
				$report_file_name = sti_get_xml_value($client_data, "ReportFileName");
				$new_report_flag = sti_get_xml_value($client_data, "NewReportFlag");
				return sti_save_report($report, $report_key, $report_file_name, $new_report_flag);
				break;
			
			// Test database connection
			case "TestConnection":
				$connection_type = sti_get_xml_value($client_data, "ConnectionType");
				$connection_string = sti_create_connection_string($client_data);
				
				switch ($connection_type)
				{
					case "StiSqlDatabase": return sti_mssql_test_connection($connection_string);
					case "StiMySqlDatabase": return sti_mysql_test_connection($connection_string);
					case "StiOdbcDatabase": return sti_odbc_test_connection($connection_string);
					case "StiPostgreSQLDatabase": return sti_pg_test_connection($connection_string);
					case "StiOracleDatabase": return sti_oracle_test_connection($connection_string);
					case "StiFirebirdDatabase": return sti_firebird_test_connection($connection_string);
					case "StiMongoDBDatabase": return sti_mongodb_test_connection($connection_string);
					
					default: return "";
				}
				break;
			
			// Retrieve table columns query
			case "RetrieveColumns":
				$connection_type = sti_get_xml_value($client_data, "ConnectionType");
				$data_path = sti_get_xml_value($client_data, "DataPath");
				$schema_path = sti_get_xml_value($client_data, "SchemaPath");
				$connection_string = sti_create_connection_string($client_data);
				$query = sti_get_xml_value($client_data, "Query");
				$query = base64_decode($query);
				
				switch ($connection_type)
				{
					case "StiXmlDatabase": return sti_xml_get_columns($data_path, $schema_path);
					case "StiSqlDatabase": return sti_mssql_get_columns($connection_string, $query);
					case "StiMySqlDatabase": return sti_mysql_get_columns($connection_string, $query);
					case "StiOdbcDatabase": return sti_odbc_get_columns($connection_string, $query);
					case "StiPostgreSQLDatabase": return sti_pg_get_columns($connection_string, $query);
					case "StiOracleDatabase": return sti_oracle_get_columns($connection_string, $query);
					case "StiFirebirdDatabase": return sti_firebird_get_columns($connection_string, $query);
					case "StiMongoDBDatabase": return sti_mongodb_get_columns($connection_string, $query);
					
					default: return "";
				}
				break;
			
			// Data query
			// Response to the client - data in the xml format
			case "LoadData":
				$connection_type = sti_get_xml_value($client_data, "ConnectionType");
				$data_path = sti_get_xml_value($client_data, "DataPath");
				$schema_path = sti_get_xml_value($client_data, "SchemaPath");
				$connection_string = sti_create_connection_string($client_data);
				$data_source_name = sti_get_xml_value($client_data, "DataSourceName");
				$query = sti_get_xml_value($client_data, "Query");
				$query = base64_decode($query);
				
				switch ($connection_type)
				{
					case "StiXmlDatabase": return sti_xml_get_data($data_path, $schema_path);
					case "StiSqlDatabase": return sti_mssql_get_data($connection_string, $data_source_name, $query);
					case "StiMySqlDatabase": return sti_mysql_get_data($connection_string, $data_source_name, $query);
					case "StiOdbcDatabase": return sti_odbc_get_data($connection_string, $data_source_name, $query);
					case "StiPostgreSQLDatabase": return sti_pg_get_data($connection_string, $data_source_name, $query);
					case "StiOracleDatabase": return sti_oracle_get_data($connection_string, $data_source_name, $query);
					case "StiFirebirdDatabase": return sti_firebird_get_data($connection_string, $data_source_name, $query);
					case "StiMongoDBDatabase": return sti_mongodb_get_data($connection_string, $data_source_name, $query);
					
					default: return "";
				}
				break;
			
			// Exported report
			case "ExportReport":
				$format = sti_get_xml_value($client_data, "Format");
				$report_key = sti_get_xml_value($client_data, "ReportKey");
				$file_name = sti_get_xml_value($client_data, "FileName");
				$data = base64_decode(sti_get_xml_value($client_data, "Data"));
				return sti_export_report($format, $report_key, $file_name, $data);
				break;
			
			// Send report by E-mail
			case "SendEmailReport":
				$options = new DOMDocument();
				$options->loadXML($client_data);
				$format = $options->getElementsByTagName("Format")->item(0)->nodeValue;
				$report_key = $options->getElementsByTagName("ReportKey")->item(0)->nodeValue;
				$data = base64_decode($options->getElementsByTagName("Data")->item(0)->nodeValue);
				$email_options = $options->getElementsByTagName("EmailOptions")->item(0);
				$email = $email_options->getElementsByTagName("Email")->item(0)->nodeValue;
				$subject = $email_options->getElementsByTagName("Subject")->item(0)->nodeValue;
				$message = $email_options->getElementsByTagName("Message")->item(0)->nodeValue;
				$file_name = $email_options->getElementsByTagName("FileName")->item(0)->nodeValue;
				return sti_send_email_report($format, $report_key, $email, $subject, $message, $file_name, $data);
				break;
		}
	}

?>