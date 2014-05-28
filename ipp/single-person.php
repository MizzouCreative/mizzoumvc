<?php
/**
 * Controller for display of a single Person CPT
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @todo move function calls out of this view
 */

/**
 * get our model
 * @todo we should be able to make this a function and move up higher
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'people.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();

$objStaffModel = new People();
$objPerson = $objStaffModel->retrieveStaff($post)

$aryData['objPerson'] = $objPerson;

?>
<p>Single Person:</p>

<xmp>
    <?php var_export($objPerson); ?>
</xmp>