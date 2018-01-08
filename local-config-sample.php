<?php
define('DB_NAME',           getenv('MYSQL_DATABASE') );
define('DB_USER',           getenv('MYSQL_USER') );
define('DB_PASSWORD',       getenv('MYSQL_PASSWORD') );
define('DB_HOST',           getenv('MYSQL_HOST') );
define('WP_POST_REVISIONS', 3);
define('WP_DEBUG',          (bool) getenv('WP_DEBUG') );
