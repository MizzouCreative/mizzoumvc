<?php
//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'base.php';

global $wp_query;
$objWpBase = new WpBase();

var_export($wp_query);
