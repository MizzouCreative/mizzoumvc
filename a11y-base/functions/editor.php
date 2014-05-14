<?php // Remove tools from editor
if (isset($wp_version)) {
add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);
}

function extended_editor_mce_buttons($buttons) {
return array(
"pastetext", "formatselect", "separator",
"bullist", "numlist", "blockquote", "separator", "outdent",  "indent", 
"separator", "removeformat", "fullscreen","wp_adv");
}

function extended_editor_mce_buttons_2($buttons) {
// the second toolbar line
return array(
"bold", "italic", "strikethrough", "separator", "link", "unlink", "anchor","charmap", "separator", "wp_help");
}


// Remove the ability to Open link in a new window/tab 
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
			.link-target {
				display: none !important;
			}
		</style>';
}

// Only show specialized edit button for the actual author of the post
function author_edit() {
   if ( current_user_can('edit_post') ) { ?>
       	<a class="post-edit-link" href="/edit-<?php echo get_post_type(get_the_ID());?>/?gform_post_id=<?php the_ID(); ?>" title="Edit">Edit</a>
	<? }
}



// Delete post from the frontend (only used on attachments)

function frontend_delete_link() {
		// add this to the post template: frontend_delete_link(); 

       if ( current_user_can('edit_post') ) {

       $post_id = get_the_ID();

       $url = add_query_arg (
	        	array (
		            'action'=>'frontend_delete',
		            'post'=> $post_id,
	                )
		        );
        echo  "<a class='post-delete-link' href='{$url}'>Permanently Delete</a>";
       } // end if user can

} // end function

if ( isset($_REQUEST['action']) && $_REQUEST['action']=='frontend_delete' ) {
    add_action('init','frontend_delete_post');
}
 
function frontend_delete_post($parent_url) {
 
    // Get the ID of the post.
    $post_id = (isset($_REQUEST['post']) ?  (int) $_REQUEST['post'] : 0);

    // No post? Oh well..
    if ( empty($post_id) )
        return;

	$page_data = get_page( $post_id ); // get post data BEFORE we delete it, Charlie...
	$parent_id = $page_data->post_parent; // Get Parent
	$redirect = get_permalink($parent_id);
 
    // Delete post
    wp_delete_post( $post_id,true );

    // Redirect to parent page
    wp_redirect( $redirect );
    exit;
} // end function


// Add PDF as attachment to post when created or edited via GF
// Based on http://tech.bandonrandon.com/2013/03/05/uploading-files-as-post-attachments-with-gravity-forms/
add_filter("gform_after_submission_1", "add_post_attachments", 10, 3); // declare the form ID in gform_after_submission_XX

function add_post_attachments($entry) {
	
	$input_id = 29;

	//you'll need this later, TRUST ME.
	if ( !function_exists('wp_generate_attachment_metadata') ) {
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}
	
	//do we even have a file?
	if ( isset($_FILES['input_'.$input_id.'']) ) { // the input NAME
	
		$file_url = $entry[$input_id]; // Input number will get the url
		
		if ($file_url != '' ) { // Check to see if anything has been submitted

			$upload_dir = wp_upload_dir(); // put it in the upload directory
			$file_data = file_get_contents($file_url); // show me what you're made of
			$filename = basename($file_url); // what's its file name?
			
			if(wp_mkdir_p($upload_dir['path'])) //can we put it there?
			$file = $upload_dir['path'] . '/' . $filename; //yes great
			else //or no, okay fine let's try somewhere else
			$file = $upload_dir['basedir'] . '/' . $filename; //get the whole location
			file_put_contents($file, $file_data); // tada home at last
			 
			$wp_filetype = wp_check_filetype($filename, array('pdf' => 'application/pdf','pdf' => 'application/x-pdf') ); //is it the right type of of file?
			$attachment = array( //set up the attachment
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
			);
		 
			$attach_id = wp_insert_attachment( $attachment, $file, $entry['post_id'] ); //insert attachment
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file ); //asign the meta
			wp_update_attachment_metadata( $attach_id, $attach_data ); //update the post
		}

	}
}

?>