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

/**
 * We're using wordpress' get_template_part here so that our config file is loaded from a child theme, if available.
 * Otherwise, it'll fall back to the one in the framework which is set to search all of missouri.edu
 */
get_template_part('config');
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Base.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Site.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Content.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'MizzouPost.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Subview.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Header.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Footer.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'WpBase.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'A11yPageWalker.php';

/**
 * @todo which of these can we remove?
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'settings.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'post-types.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'menus.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'editor.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'childnav.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'shortcodes.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'widgets.php';

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'twig'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Twig'.DIRECTORY_SEPARATOR.'Autoloader.php';


/**
 * Contains and fires all of the add_filter, add_action and remove_action hooks that need to fire during init
 * @todo rename function to conform to naming standards
 */
function mizzou_setup(){
    add_filter('query_vars','mizzou_add_URL_query_vars');
    add_filter('default_hidden_meta_boxes', 'mizzou_display_postexcerpt', 10, 2);
    add_filter( 'wp_terms_checklist_args', 'mizzou_no_top_float', 10, 2 );
    add_filter('edit_tag_link', 'edit_tag_link_new_window');

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
     * @todo is this the best place for this to occur?
     */
    Twig_Autoloader::register();

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

/**
 * contains all of the other functions that need to occur related to setting up the theme.
 * @todo since this is really a theme-specific option, should it be moved out of the framework and pushed back down to
 * the child theme to implement?
 * @todo rename function to conform to naming standards
 */
function mizzou_setup_theme(){
    add_theme_support( 'post-thumbnails' );
}

/**
 * Adds the 'q' query var for google search 
 * 
 * @param array $aryVars
 * @return array
 * @todo rename function to conform to naming standards
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
 * @todo this needs to be converted over to the Site model?
 * @todo rename function to conform to naming standards
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
 * @todo should this be moved into a theme option and handled by the theme class?
 * @todo rename function to conform to naming standards
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
* @todo rename function to conform to naming standards
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
 * @param array $args
 * @param integer $post_id
 * @return array
 * @todo rename function to conform to naming standards
 */
function mizzou_no_top_float( $args, $post_id ) {
  // If the taxonomy is set and equals person_type or group
	if ( isset( $args['taxonomy'] ) && 'person_type' == $args['taxonomy'] || 'group' == $args['taxonomy'] || 'group' == $args['author_assignment']   ) {
        $args['checked_ontop'] = false;
    }

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
 * @deprecated moved into Site model
 */
function site_modified_date($boolReturn=false) {
	_mizzou_log(__FUNCTION__,'use of deprecated function detected.',true);
    global $wpdb;
	$last_site_update =  $wpdb->get_var( "SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT 1" );
	$strLastUpdate = date('M j, Y', strtotime($last_site_update));

    if($boolReturn){
        return $strLastUpdate;
    } else {
        echo $strLastUpdate;
    }
}

function mizzouCreateTaxonomyLabels($strBaseWord,$strBaseWordPlural = '')
{
    return mizzouCreateLabels('taxonomy',$strBaseWord,$strBaseWordPlural);
}


function mizzouCreatePostTypeLabels($strBaseWord,$strBaseWordPlural='')
{
    return mizzouCreateLabels('post',$strBaseWord,$strBaseWordPlural);
}

function mizzouCreateLabels($srtType,$strBaseWord,$strBaseWordPlural='')
{
    $aryAdditionalLabels = array();
    if($strBaseWordPlural ==''){
        $strBaseWordPlural = $strBaseWord . 's';
    }

    $aryBaseLabels = array(
        'name'          => $strBaseWordPlural,
        'singular_name' => $strBaseWord,
        'menu_name'     => $strBaseWordPlural,
        'all_items'     => 'All ' . $strBaseWordPlural,
        'edit_item'     => 'Edit ' . $strBaseWord,
        'add_new_item'  => 'Add New ' . $strBaseWord,
        'view_item'     => 'View ' . $strBaseWord,
        'search_items'  => 'Search ' . $strBaseWordPlural,
        'not_found'     => 'No ' . $strBaseWordPlural . ' found',
        'parent_item_colon' => 'Parent ' . $strBaseWord .':',
    );

    switch ($srtType){
        case 'post':
            $aryAdditionalLabels = array(
                'name_admin_bar'    => $strBaseWord,
                'new_item'          => 'New ' . $strBaseWord,
                'not_found_in_trash'=> 'No '.$strBaseWordPlural.' found in Trash',
            );
            break;
        case 'taxonomy':
            $aryAdditionalLabels = array(
                'update_item'               => 'Update '.$strBaseWord,
                'new_item_name'             => 'New ' . $strBaseWord . ' Name',
                'parent_item'               => 'Parent ' . $strBaseWord,
                'popular_items'             => 'Popular ' . $strBaseWordPlural,
                'separate_items_with_commas'=> 'Separate ' . $strBaseWordPlural . ' with commas',
                'add_or_remove_items'       => 'Add or remove ' . $strBaseWordPlural,
                'choose_from_most_used'     => 'Choose from the most used ' . $strBaseWordPlural,

            );
            break;
    }

    return array_merge($aryBaseLabels,$aryAdditionalLabels);
}

/**
 * Removes the default contextual help panel tabs and adds our custom help tab.  Contents for our custom tab are assumed
 * to be contained in a directory names "views" in a file named "sampleHelp.html".  Adjust as needed
 *
 * This is not being used currently in the framework, but is being left here, for now, for future use.
 *
 * In order to use, uncomment the add_filter('contextual_help line in the FILTERS area below
 *
 * @param $strOldHelp string
 * @param $intScreenID integer
 * @param $objScreen object
 * @return string
 */
function mizzouAdjustHelpScreen($strOldHelp,$intScreenID,$objScreen)
{
    // we only want to adjust the help contents when working on the default post type
    if($objScreen->post_type == 'post'){
        // we only want to adjust the help contents if we can load our custom help contents
        if (FALSE !== $strHelpContents = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'sampleHelp.html')) {

            //get rid of all the default help tabs in the contextual help panel
            $objScreen->remove_help_tabs();//get rid of all the help tabs

            //remove the help panel sidebar
            $objScreen->set_help_sidebar('');

            //add our custom help tab
            $objScreen->add_help_tab(array(
                'id' => 'sample_help_tab',
                'title' => 'Sample Help Tab',
                'content' => $strHelpContents,
            ));
        }
    }

    return $strOldHelp;
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
 * ================= CONSTANTS ================================
 */
define('MVC_PARENT_PATH',get_template_directory().DIRECTORY_SEPARATOR);
define('MVC_CHILD_PATH',get_stylesheet_directory().DIRECTORY_SEPARATOR);

/**
 * ================= FILTERS ================================
 */
// not currently in use. @see mizzouAdjustHelpScreen()
//add_filter('contextual_help','mizzouAdjustHelpScreen',10,3);

/**
 * NO ADDITIONAL CODE SHOULD BE BELOW THIS LINE
* ================= DEBUG =====================================
*/

/**
 * @todo should this function be moved into the Framework singleton?
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
      $strMessage = 'MIZZOU_LOGGER: ';
      
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

      $strMessage .= 'The variable is a ' . gettype($mxdVariable) . PHP_EOL;
      
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