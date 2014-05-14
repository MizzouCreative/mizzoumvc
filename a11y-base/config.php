<?php
/**
* Contains global configuration values.  At a minimum the GSA search parameters
* and the GSA search URL. 
* 
* This is not an official wordpress theme file but used by us to give site 
* maintainers a location to change some theme options without having to build
* an options panel in the wp-admin area. 
* 
* @package WordPress
* @subpackage SITENAME
* @category theme
* @category theme-helper
* @category configuration
* @author Paul F. Gilzow & Jason Rollins, Web Communications, University of Missouri
* @copyright 2013 Curators of the University of Missouri
* @version 201303281212
* 
* 
*/

/**
 * At a minimum, change the sitesearch value to the new site's domain
 */
define('GSA_SEARCH_PARAMS',  serialize(array(
    'site'            => 'default_collection',
    'proxystylesheet' => 'wc_standard',
    'client'          => 'wc_standard',
    'output'          => 'xml_no_dtd',
    'sitesearch'      => 'truman.missouri.edu'
)));

/**
* GSA's site search url
*/
define('GSA_SEARCH_URL','http://search.missouri.edu/search?');

/**
 * The maxinum number of pagination links to show on archive pages. This does 
 * not count Prev and Next, nor does it count first and last.
 * 
 * Example: you are page 19 of 31 total pages, with a max links of 6. Pagination 
 * will looks like this
 * 
 * Prev 1 ... 16 17 18 *19* 20 21 22 ... 31 Next
 */
define('SITE_MAX_NUM_ARCHIVE_PAGE_LINKS',6);
?>
