<?php
namespace tomlib\config;

include_once dirname(__FILE__) . "/file.php";
include_once dirname(__FILE__) . "/object.php";

use tomlib\file as File;
use tomlib\object as Object;

const CONFIG_FILE_NAME = "config.json";

function load() {
		global $tomlib_config;

		if(is_null($tomlib_config)) {
				$filename = dirname(__FILE__) . "/" . CONFIG_FILE_NAME;

				$tomlib_config = File\read_json($filename);
		}
}

function get_setting($name) {
		global $tomlib_config;

		load();

		return Object\try_get_property($tomlib_config, $name);
}
