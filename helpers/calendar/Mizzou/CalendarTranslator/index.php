<?php
/**
 * PHP was having a melt down that the server's default timezone had not been set'
 */
date_default_timezone_set('America/Chicago');
/**
 * Example usage of calendar translator
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ExampleTranslator.php');
$aryCalendarOptions = array(
	'method'    =>'type',
	'term'      =>'Science',
	'days'      => 60
);
$objCalendarEvents = new Mizzou\CalendarTranslator\ExampleTranslator($aryCalendarOptions);
$aryCalendarEvents = $objCalendarEvents->retrieveCalendarItems($aryCalendarOptions);

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title></title>
	</head>
	<body>
		<?php foreach($aryCalendarEvents as $objEvent): ?>
		<!-- do something with the data here -->

		<?php endforeach; ?>
	</body>
</html>
