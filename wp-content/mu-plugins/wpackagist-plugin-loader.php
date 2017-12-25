<?php

exec('grep -l "Plugin Name" ' . WPMU_PLUGIN_DIR . '/*/*.php', $stdout, $status);

foreach($stdout as $file){
	require_once $file;
}
