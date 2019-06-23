<?php

	// Getting a list of localization files
	function sti_get_localizations_list($localization_directory)
	{		$list = array();
		
		if (is_dir($localization_directory))
		{			$directory = opendir($localization_directory);
			$index = 0;
			
			while ($fileName = readdir($directory))
			{				$parts = explode(".", $fileName);
				if (is_array($parts) && count($parts) == 2)
				{					$extension = end($parts);
					if (strtolower($extension) == "xml") $list[$index++] = $parts[0];
				}
			}
			
			closedir($directory);
		}
		
		return $list;
	}

	// Find the list of localizations in configuration. If the list is not found then create a new one
	function sti_get_localizations_list_node($config_xml)
	{
		$element = $config_xml->getElementsByTagName("StiSerializer");		$config_node = $element->item(0);
		
		$element = $config_node->getElementsByTagName("Localizations");		$localizations_node = $element->item(0);
		if (!isset($localizations_node))
		{			$localizations_node = $config_xml->createElement("Localizations");
			$config_node->appendChild($localizations_node);
		}
		
		$element = $localizations_node->getElementsByTagName("LocalizationsList");
		$localizations_list_node = $element->item(0);
		if (!isset($localizations_list_node))
		{
			$localizations_list_node = $config_xml->createElement("LocalizationsList");
			$localizations_node->appendChild($localizations_list_node);
		}
		
		return $localizations_list_node;
	}

	// Returns the information about the specified localization
	function sti_get_localization_node($config_xml, $localization_directory, $localization_file_name)
	{		$localization_xml = new DOMDocument();
		$localization_xml->load($localization_directory."/".$localization_file_name.".xml");
		$element = $localization_xml->getElementsByTagName("Localization");
		$localization_node = $element->item(0);
		
		$element = $config_xml->createElement("Value");
		
		$value = $config_xml->createElement("FileName", $localization_file_name.".xml");
		$element->appendChild($value);
		
		$value = $config_xml->createElement("Language", $localization_node->getAttribute("language"));
		$element->appendChild($value);
		
		$value = $config_xml->createElement("Description", $localization_node->getAttribute("description"));
		$element->appendChild($value);
		
		$value = $config_xml->createElement("CultureName", $localization_node->getAttribute("cultureName"));
		$element->appendChild($value);
		
		return $element;
	}

	// Returns the config.xml file with settings and a list of available localizations
	function sti_load_config($config_file_name)
	{
		if (class_exists("DOMDocument"))
		{			$config_xml = new DOMDocument();
			$config_xml->preserveWhiteSpace = false;
			$config_xml->formatOutput = true;
			$config_xml->load($config_file_name);
			
			$localization_directory = sti_get_localization_directory();
			$localizations_list = sti_get_localizations_list($localization_directory);
			$localizations_list_node = sti_get_localizations_list_node($config_xml);
			foreach ($localizations_list as $localization_file_name)
			{				$element = sti_get_localization_node($config_xml, $localization_directory, $localization_file_name);				$localizations_list_node->appendChild($element);
			}
			
			return $config_xml->saveXML();
		}
		
		return file_get_contents($config_file_name);
	}

	// Load the localization file, if necessary - combine two files into one localization
	function sti_load_localization_file($file_path)
	{
		$file_path_ext = substr($file_path, 0, strlen($file_path) - 3)."ext.xml";
		if (class_exists("DOMDocument") && file_exists($file_path_ext))
		{
			$localization_xml = new DOMDocument();
			$localization_xml->load($file_path_ext);
			$localization_element = $localization_xml->getElementsByTagName("Localization")->item(0);
			
			$result_xml = new DOMDocument();
			$result_xml->load($file_path);
			$element = $result_xml->getElementsByTagName("Localization")->item(0);
			$node = $result_xml->importNode($localization_element, true);
			$element->appendChild($node);
			
			return $result_xml->saveXML();
		}
		
		return file_get_contents($file_path);
	}

?>
