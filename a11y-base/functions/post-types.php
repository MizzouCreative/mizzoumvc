<?php

function modify_post_type()
{
	remove_post_type_support('post', 'trackbacks');
    register_taxonomy('post_tag', array());
}
add_action('init', 'modify_post_type');


//handle to add custom post type 
add_action('init', 'slide_post_type_init');

//callback function
function slide_post_type_init() 
{
	slide_init();
	modify_post_type();
}
function slide_init()
{
//labels for the UI for the custom post type
  $labels = array(
    'name' => _x('Slides', 'post type general name'),
    'singular_name' => _x('Slide', 'post type singular name'),
    'add_new' => _x('Add New', 'slide'),
    'add_new_item' => __('Add New Slide'),
    'edit_item' => __('Edit Slide'),
    'new_item' => __('New Slide'),
    'view_item' => __('View Slide'),
    'search_items' => __('Search Slides'),
    'not_found' =>  __('No slides found'),
    'not_found_in_trash' => __('No slides found in Trash'), 
    'parent_item_colon' => ''
  );
  //functionality of custom post type see register_post_type entry for info
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => false, // Slides don't need a single page
    'show_ui' => true, 
    'query_var' => false,
    'rewrite' => false, // false in this case because of special characters and very long titles 
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail','revisions','page-attributes'),
  ); 
  register_post_type('slide',$args);
}


?>