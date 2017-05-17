<?php
namespace tomlib\auth\config;

include_once dirname(__FILE__) . "/config.php";
include_once dirname(__FILE__) . "/object.php";

use tomlib\config as Config;
use tomlib\object as Object;

function load() {
		global $tomlib_auth_config;

		if(is_null($tomlib_auth_config))
				$tomlib_auth_config = Config\get_setting("auth");
}

function get_setting($name) {
		global $tomlib_auth_config;

		load();

		return Object\try_get_property($tomlib_auth_config, $name);
}
