<?php
 /**
 * Template Name: Search
 * 
 * Displays search results from the GSA. Also doubles as the the template to 
 * be attached to the Search page created in wordpress.  
 *
 * @package WordPress
 * @subpackage mizzou-news
 * @category theme
 * @category template
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */

$arySearchParams = unserialize(GSA_SEARCH_PARAMS); 
/**
* Doesnt matter if s or q has been used as the search parameter, we want to use either to invoke a gsa search
*/
if ( (isset( $_GET['q'] ) && $_GET['q'] != '') || (isset($_GET['s']) && $_GET['s'] != '')) {

    // Add search inputs to query array
    $arySearchParams['q'] = (isset($_GET['q'])) ? $_GET['q'] : $_GET['s'];
    if(is_array($arySearchParams['q'])) $arySearchParams['q'] = implode (' ', $arySearchParams['q']);
    if ( isset($_GET['start']) && $_GET['start'] != '') $arySearchParams['start']  = $_GET['start'];
    if ( isset($_GET['sort']) && $_GET['sort'] != '') $arySearchParams['sort']   = $_GET['sort'];
    if ( isset($_GET['filter']) && $_GET['filter']  != '') $arySearchParams['filter'] = $_GET['filter'];
    

    $arySearchParams['q'] = stripcslashes($arySearchParams['q']);
    
    

    $strResults = file_get_contents(GSA_SEARCH_URL.http_build_query($arySearchParams));
    
} 
$boolNoResults = (!isset($strResults) || $strResults == '') ? true : false; 
get_header();
define('MUNEWS_SEARCHTERM', htmlentities($arySearchParams['q'],ENT_QUOTES,'UTF-8'));
 ?>

        <div class="span7">
            <section id="main">
                <h2 id="skip">Search <?php bloginfo('name'); ?></h2>
                <div class="content" role="main">
                    <div class="search-box">
                        <?php get_search_form(); ?>
                    </div>
                    
                    <?php if(!$boolNoResults): ?>
                        <?php echo $strResults;?>
                    <?php endif;?>                      
                </div>
            </section>
        </div><!-- #default -->
        <?php if(file_exists(dirname(__FILE__).'/aside-search.php')) :?>  
        <div class="aside">
            <div class="box">
                <?php get_template_part('aside', 'search'); ?>
            </div>
        </div>    
        <?php endif; ?>

<?php get_footer(); ?>