<?php
// error_reporting(E_ALL); ini_set('display_errors', 1);

/**
 * Contains functions necessary for the functionality and display of the theme
 * 
 * 
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category functions
 * @author Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 * @version 201303281326
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Base.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'MizzouPost.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'A11yPageWalker.php';

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'settings.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'post-types.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'menus.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'editor.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'childnav.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'shortcodes.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'widgets.php';


function mizzou_setup(){
    add_filter('query_vars','mizzou_add_URL_query_vars');
    add_filter('default_hidden_meta_boxes', 'mizzou_display_postexcerpt', 10, 2);
    add_filter( 'wp_terms_checklist_args', 'mizzou_no_top_float', 10, 2 );
    add_filter('edit_tag_link', 'edit_tag_link_new_window');
    // Remove tabindex setting from gravity forms (learned from NewsA11y)
    add_filter("gform_tabindex", create_function("", "return false;"));
    /**
     * Completely disable pingback support
     * @see http://blog.sucuri.net/2014/03/more-than-162000-wordpress-sites-used-for-distributed-denial-of-service-attack.html
     */
    add_filter('xmlrpc_methods', function( $aryMethods ) {
            unset( $aryMethods['pingback.ping'] );
            return $aryMethods;
	}   
    );
    
    /**
     * Remove link for feeds
     */
    remove_action( 'wp_head', 'feed_links', 2);
    /**
     * remove link to feeds for extra areas like categories
     */
    remove_action('wp_head','feed_links_extra', 3);
    /**
     * remove link for windows live writer
     */
    remove_action( 'wp_head', 'wlwmanifest_link' );
    /**
     * remove generator meta 
     */
    remove_action( 'wp_head', 'wp_generator' );
    /**
     * remove rsd link
     */
    remove_action( 'wp_head', 'rsd_link' ); 
    /**
     * remove head links to previous and next
     */
    remove_action('wp_head', 'start_post_rel_link', 10); 
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    
}

function mizzou_setup_theme(){
    add_theme_support( 'post-thumbnails' );
}

/**
 * Adds the 'q' query var for google search 
 * 
 * @param array $aryVars
 * @return array 
 */
function mizzou_add_URL_query_vars($aryVars){
    $aryVars[] = 'q';
    return $aryVars;
}

/**
 * Takes the 404 URL, strips out the extra characters and ignore words and returns
 * a string of words to search on
 * 
 * @param array $aryIgnoreWords words/characters to ignore, DEFAULT: / - .php .html .aspx
 * @return string
 */
function mizzou_gather_404_search_terms($aryIgnoreWords=null){
    $aryIgnore = array(
            '/',
            '-',
            '.php',
            '.html',
            '.aspx',
            '.htm',
            '.cfm',
            '.asp',
     );
    
    if(!is_null($aryIgnoreWords) && is_array($aryIgnoreWords)){
        $aryIgnore = array_merge($aryIgnore,$aryIgnoreWords);
    }
    
    $aryReplace = array_fill(0, count($aryIgnore), ' ');
    
    $strRequestedPage = $_SERVER['REQUEST_URI'];
    
    /**
    * This would be much better with a regex, but i dont have time right now to 
    * build it.  We dont care about any url parameters. we need to remove 
    * index.php and index.html BEFORE we strip out .php and .html since they 
    * could be looking for a page with the word 'index' that isnt part of 
    * index.php/html
    * 
    * @todo convert this to a regex
    */    
    
    if(FALSE !== $intQMPos = strpos($strRequestedPage, '?')){
        $strRequestedPage = substr($strRequestedPage, 0,$intQMPos);
    }
    
    $strSearchWords = str_replace(array('index.php','index.html'), array('',''), $strRequestedPage);
    $strSearchWords = trim(str_replace($aryIgnore, $aryReplace,$strSearchWords));
    
    return $strSearchWords;
    
}

/**
 * Restores the Post Excerpt metabox in the write panel for new users
 * 
 * Starting at 3.1, Wordpress began hiding the post excerpt by default in the
 * write panel.  It was still accessible through screen options.  We prefer to
 * have it displayed by default.  
 * 
 * @param array $aryHidden
 * @param object $objScreen
 * @return array
 */
function mizzou_display_postexcerpt($aryHidden,$objScreen){
    if($objScreen->base == 'post'){
        if(FALSE !== $intKey = array_search('postexcerpt', $aryHidden)) unset($aryHidden[$intKey]);
    }
    return $aryHidden;
}

/**
* Removes dashboard items from the Admin dashboard
* 
* @return void
*/
function mizzou_remove_dashboard_widgets(){
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    
}

/**
 * Disable 'checked_ontop' for taxonomies (learned from NewsA11y)
 * @param boolean $args
 * @param type $post_id
 * @return boolean
 * @todo complete documentation please
 */
function mizzou_no_top_float( $args, $post_id ) {
  // If the taxonomy is set and equals person_type or group
	if ( isset( $args['taxonomy'] ) && 'person_type' == $args['taxonomy'] || 'group' == $args['taxonomy'] || 'group' == $args['author_assignment']   )
		$args['checked_ontop'] = false;
 
	return $args;
}




/**
 * Solely here for backwards compatibility and to catch instances when it is still being used
 */
class a11y_walker extends A11yPageWalker {
    public function __construct()
    {
        _mizzou_log(self,'old a11y_walker being called. see backtrace',true);
        parent::__construct();
    }
}

/**
 * If more than one page of archived posts exists, return TRUE.
 * @global type $wp_query
 * @return type
 * @todo documentation
 */
function pagination_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}

/**
 * 
 * @param type $content
 * @return type
 * @todo documentation
 */
function edit_tag_link_new_window($content) {
    $content = preg_replace('/href/', 'target="_blank" href', $content);
    return $content;
}
/**
 * Last update of anything on the site
 * @todo documentation
 * @param boolean $boolReturn, default is false
 * @global type $wpdb
 */
function site_modified_date($boolReturn=false) {
	global $wpdb;
	$last_site_update =  $wpdb->get_var( "SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT 1" );
	$strLastUpdate = date('M j, Y', strtotime($last_site_update));

    if($boolReturn){
        return $strLastUpdate;
    } else {
        echo $strLastUpdate;
    }
}

/**
* ================= ACTIONS ================================
*/
/**
*  Removes all of the dashboard widgets except right now and drafts
*/
add_action('wp_dashboard_setup','mizzou_remove_dashboard_widgets');
/**
* In order to add theme options, they need to be hooked BEFORE the init.
*/
add_action('after_setup_theme','mizzou_setup_theme');
/**
*  All other filters and actions that dont need to fire before init
*/
add_action('init','mizzou_setup');


/**
 * NO ADDITIONAL CODE SHOULD BE BELOW THIS LINE
* ================= DEBUG =====================================
*/

if(!function_exists('_mizzou_log')){
  /**
  * For logging debug messages into the debug log.
  * 
  * To enable logging, you'll need to add the following to @see wp-config.php:
  * <code>
        define('WP_DEBUG', true); 
        define('WP_DEBUG_DISPLAY', false);
        define('WP_DEBUG_LOG', true);
  * </code>
  * 
   * This will create a debug.log file inside of /wp-content/
   * 
  * @param mixed $mxdVariable variable we need to debug
  * @param $strPrependMessage message to include
  * @param boolean $boolBackTraced
  * @param array $aryDetails details for doing a mini backtrace instead of the full thing
  * 
  */
  function _mizzou_log( $mxdVariable, $strPrependMessage = null, $boolBackTraced = false, $aryDetails = array() ) {
    $boolBackTrace = true;
    if( WP_DEBUG === true ){
      $strMessage = 'MIZZOU_LOG: ';
      
      if(count($aryDetails) > 0){
          if(isset($aryDetails['line'])){
              $strMessage .= 'At line number ' . $aryDetails['line'] . ' ';
          }
          
          if(isset($aryDetails['func'])){
              $strMessage .= 'inside of function ' . $aryDetails['func'] . ' ';
          }
          
          if(isset($aryDetails['file'])){
              $strMessage .= 'in file ' . $aryDetails['file'] .' ';
          }
          
          $strMessage .= PHP_EOL;
      }
      
      if(!is_null($strPrependMessage)) $strMessage .= $strPrependMessage.PHP_EOL;
      
      if( is_array( $mxdVariable ) || is_object( $mxdVariable ) ){
         $strMessage .= PHP_EOL . var_export($mxdVariable,true);
      } elseif(is_bool($mxdVariable)) {
        $strMessage .= 'Boolean: ';
        $strMessage .=  (true === $mxdVariable) ? 'true' : 'false';
      } else {
          $strMessage .= $mxdVariable;
      }
      
      if($boolBackTrace && $boolBackTraced){
          $aryBackTrace = debug_backtrace();
          
          $strMessage .= PHP_EOL.'Contents of backtrace:'.PHP_EOL.var_export($aryBackTrace,true).PHP_EOL;          
      }

      $strMessage .= PHP_EOL;
      error_log($strMessage);
    }
  }
}
?>