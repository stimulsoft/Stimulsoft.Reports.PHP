<?php

	function sti_xml_get_columns($data_path, $schema_path)
	{
		$schema = file_get_contents($schema_path);
		return $schema;
	}
	
	function sti_xml_get_data($data_path, $schema_path)
	{
		$data = file_get_contents($data_path);
		return $data;
	}

?>