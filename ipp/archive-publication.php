<?php
//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'base.php';

global $WP_Query;
$objWpBase = new WpBase();

var_export($WP_Query);
