<?php


/**
 * @todo figure out a way to dynamically load all files in the helpers dir
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'paths.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shortcodes.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'CaptureOutput.php';
/**
 *
 */
function mizzouIppInit()
{
    quote_init(); //is this still needed

    add_action('pre_get_posts','mizzouRemoveRestrictionOnArchives');

    /**
     * @todo come back to this one. removed for now
     */
    //add_rewrite_endpoint('author_archive', EP_PERMALINK | EP_PAGES);
    //add rewrite tag so that we can use it when we register our taxonomies
    //add_rewrite_tag('%post_type%','([^/]+)');

}
//handle to add custom post type 

/**
 *
 */
function quote_init()
{
//labels for the UI for the custom post type
  $labels = array(
    'name' => _x('Quote', 'post type general name'),
    'singular_name' => _x('Quote', 'post type singular name'),
    'add_new' => _x('Add New', 'quote'),
    'add_new_item' => __('Add New Quote'),
    'edit_item' => __('Edit Quote'),
    'new_item' => __('New Quote'),
    'view_item' => __('View Quote'),
    'search_items' => __('Search Quotes'),
    'not_found' =>  __('No quotes found'),
    'not_found_in_trash' => __('No quotes found in Trash'), 
    'parent_item_colon' => ''
  );
  //functionality of custom post type see register_post_type entry for info
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => false, // People do need a single page
    'show_ui' => true, 
    'query_var' => false,
    'rewrite' => true, // false in this case because of special characters and very long titles 
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor'),
	'taxonomies' => array('') // line to activate categories and tags for post type
  ); 
  register_post_type('quote',$args);
}

function mizzouRemoveRestrictionOnArchives($objQuery)
{
    if($objQuery->is_archive() && $objQuery->is_main_query()){
        $objQuery->set('posts_per_page',-1);
    }
}

add_action('init', 'mizzouIppInit');

?>