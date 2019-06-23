<?php
	require_once("localization.php");
	require_once("database_firebird.php");
	require_once("database_mongodb.php");
	require_once("database_mssql.php");
	require_once("database_mysql.php");
	require_once("database_odbc.php");
	require_once("database_pg.php");
	require_once("database_oracle.php");
	require_once("database_xml.php");
	require_once("handler.php");
	require_once("class.phpmailer.php");
	require_once("class.pop3.php");
	require_once("class.smtp.php");
	require_once("PHPMailerAutoload.php");


	$enable_compression = true;

	$report_key = sti_get_parameter_value("stimulsoft_report_key");
	$client_key = sti_get_parameter_value("stimulsoft_client_key");
	$client_data = file_get_contents("php://input");
	
	
	/**
	 *  Get the license key for product.
	 */
	function sti_get_license_key()
	{
		//return "6vJhGtLLLz2GNviWmUTrhSqnO......";
		if (file_exists("license.key")) return file_get_contents("license.key");
		return "";
	}
	
	
	/**
	 *  Directory, which contains the localization XML files.
	 */
	function sti_get_localization_directory()
	{
		return "localization";
	}


	/**
	 *  Returns .mrt or .mdc file by string ID that was set when running.
	 *  If necessary, it is possible to change the code for getting a report by its ID from file or from database.
	 */
	function sti_get_report($report_key)
	{
		/*switch ($report_key)
		{
			case "report1": return file_get_contents("/reports/Report.mrt");
			case "report2": return file_get_contents("/reports/Document.mdc");
		}*/
		
		if (file_exists("../reports/$report_key")) return file_get_contents("../reports/$report_key");
		
		// If there is no need to load the report, then the empty string will be sent
		return "";
		
		// If you want to display an error message, please use the following format
		return "ServerError:Some text message";
	}


	/**
	 *  The code for saving a report can be placed in this function.
	 *  
	 *  Response to the client - report key and error code.
	 *  You can use the next error codes:
	 *     -1: the message box is not shown
	 *      0: shows the "Report is successfully saved" message
	 *  In other cases shows a window with the defined value
	 */
	function sti_save_report($report, $report_key, $report_file_name, $new_report_flag)
	{
		// You can change the report key, if necessary
		//if ($new_report_flag == "True") $report_key = "MyReport.mrt";
		
		$error_code = "-1";
		$error_description = "";
		if (file_put_contents("../reports/$report_key", $report) === false)
		{
			$error_code = "Error when saving a report.";
			$arr = error_get_last();
			if (count($arr) > 0)
			{
				$error_description = $arr["message"];
				$error_description = str_replace("&", "&amp;", $error_description);
				$error_description = str_replace("<", "&lt;", $error_description);
				$error_description = str_replace(">", "&gt;", $error_description);
			}
		}
		
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><SaveReport><ReportKey>$report_key</ReportKey><ErrorCode>$error_code</ErrorCode><ErrorDescription>$error_description</ErrorDescription></SaveReport>";
	}


	/**
	 *  The function for changing values on parameters by their name in the SQL query.
	 *  Parameters can be set as {ParamName} in the SQL query.
	 *  By default values are taken according to the name of a parameter in the URL string or in the POST data.
	 */
	function sti_get_parameter($parameter_name, $default_value)
	{
		/*switch ($parameter_name)
		{
			case "ParameterName1": return "Value1";
			case "ParameterName2": return "Value2";
		}*/
		
		return $default_value;
	}


	/**
	 *  Getting the Connection String when requesting the client's Flash application to a database.
	 *  In this function you can change the Connection String of a report.
	 */
	function sti_get_connection_string($connection_type, $connection_string)
	{
		/*switch ($connection_type)
		{
			case "StiSqlDatabase": return "Data Source=SERVER\SQLEXPRESS;Initial Catalog=master;Integrated Security=True";
			case "StiMySqlDatabase": return "Server=localhost;Database=db_name;Port=3306;User=root;Password=;";
			case "StiOdbcDatabase": return "DSN=MS Access Database;DBQ=D:\NWIND.MDB;DefaultDir=D:;DriverId=281;FIL=MS Access;MaxBufferSize=2048;PageTimeout=5;UID=admin;";
			case "StiPostgreSQLDatabase": return "Server=localhost;Database=db_name;Port=5432;User=postgres;Password=postgres;";
			case "StiOracleDatabase": return "database=ORCL;user=SYSDBA;password=111;privilege=sysdba";
			case "StiFirebirdDatabase": return "server=localhost;database=/usr/local/firebird-2.1/data/test.fdb;user=SYSDBA;password=masterkey;";
		}*/
		
		return $connection_string;
	}


	/**
	 *  Saving an exported report.
	 *  Response to the client - error code. Standard codes:
	 *      -1: the message box is not shown
	 *       0: shows the "Report is successfully saved." message
	 *  In other cases shows a window with the defined value
	 */
	function sti_export_report($format, $report_key, $file_name, $data)
	{
		if (file_put_contents("../exports/$file_name", $data) === false) return "Error when saving an exported report.";
		return "-1";
	}


	/**
	 *  Send report by Email
	 */
	function sti_send_email_report($format, $report_key, $email, $subject, $message, $file_name, $data)
	{
		// You should change these parameters according to the requirements.
		$_options = array(
			// Email address of the sender
			"from" => "emailfrom@domain.com",
			// Name and surname of the sender
			"name" => "John Smith",
			// Email address of the recipient
			"to" => $email,
			// Email Subject
			"subject" => $subject,
			// Text of the Email
			"message" => $message,
			// Show a message when Email is successfully sent
			"successfully" => true,
			// The text of the error message if unable to send Email 
			"error" => "An error occurred while sending Email.",
			
			
			// Set to true if authentication is required
			"auth" => false,
			// Address of the SMTP server
			"host" => "smtp.gmail.com",
			// Port of the SMTP server
			"port" => 465,
			// The secure connection prefix - ssl or tls
			"secure" => "ssl",
			// Login (Username or Email)
			"login" => "login",
			// Password
			"password" => "password"
		);
		
		$guid = substr(md5(uniqid().mt_rand()), 0, 12);
		if (!file_exists('tmp')) mkdir('tmp');
		file_put_contents('tmp/'.$guid.'.'.$file_name, $data);
		
		$error = $_options['error'];
		$error_description = '';
		
		$mail = new PHPMailer(true);
		if ($_options['auth']) $mail->IsSMTP();
		try {
			$mail->CharSet = 'UTF-8';
			$mail->IsHTML(false);
			$mail->From = $_options['from'];
			$mail->FromName = $_options['name'];
			
			// Add Emails list
			$emails = preg_split('/,|;/', $_options['to']);
			foreach ($emails as $email) {
				$mail->AddAddress(trim($email));
			}
			
			$mail->Subject = htmlspecialchars($_options['subject']);
			$mail->Body = $_options['message'];
			$mail->AddAttachment('tmp/'.$guid.'.'.$file_name, $file_name);
			
			if ($_options['auth']) {
				$mail->Host = $_options['host'];
				$mail->Port = $_options['port'];
				$mail->SMTPAuth = $_options['auth'];
				$mail->SMTPSecure = $_options['secure'];
				$mail->Username = $_options['login'];
				$mail->Password = $_options['password'];
			}
			
			$mail->Send();
			$error = $_options['successfully'] ? '0' : '-1';
		}
		catch (phpmailerException $e) {
			$error_description = strip_tags($e->errorMessage());
		}
		catch (Exception $e) {
			$error_description = strip_tags($e->getMessage());
		}
		
		unlink('tmp/'.$guid.'.'.$file_name);
		
		if ($error == "0" || $error == "-1") return $error;
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><SendEmail><ErrorCode>$error</ErrorCode><ErrorDescription>$error_description</ErrorDescription></SendEmail>";
	}


	// Processing client Flash application commands
	if (isset($client_key))
	{
		if (!function_exists("gzuncompress")) $enable_compression = false;
		
		if ($enable_compression && $client_key != "ViewerFx" && $client_key != "DesignerFx" && strlen($client_data) > 0)
		{
			$client_data = base64_decode($client_data);
			$client_data = gzuncompress($client_data);
		}
		
		$response = sti_client_event_handler($client_key, $report_key, $client_data, $enable_compression);
		
		if ($enable_compression && strlen($response) > 0 && $client_key != "ViewerFx" && $client_key != "DesignerFx")
		{
			$response = gzcompress($response);
			$response = base64_encode($response);
		}
		
		echo $response;
	}

?>