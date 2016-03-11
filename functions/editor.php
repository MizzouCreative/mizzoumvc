<?php
/**
 * I have no idea what this is. Must be a left-over from truman.  Not needed/used in the framework.
 * The tinyMCE stuff should probably be moved to theme options?
 * @deprecated
 */

// Remove tools from editor
if (isset($wp_version)) {
add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);
}

/**
 * I assume to change what buttons are present in TinyMCE
 * @param array $buttons
 * @return array
 * @deprecated
 */
function extended_editor_mce_buttons($buttons) {
return array(
"pastetext", "formatselect", "separator",
"bullist", "numlist", "blockquote", "separator", "outdent",  "indent", 
"separator", "removeformat", "fullscreen","wp_adv");
}

/**
 * same as @see extended_editor_mce_buttons but #2?  I have no idea. I didnt write this
 * @param array $buttons
 * @return array
 * @deprecated
 */
function extended_editor_mce_buttons_2($buttons) {
// the second toolbar line
return array(
"bold", "italic", "strikethrough", "separator", "link", "unlink", "anchor","charmap", "separator", "wp_help");
}


// Remove the ability to Open link in a new window/tab 
add_action('admin_head', 'my_custom_fonts');
/**
 * No clue, but it uses !important so it must NOT be important. LOL!
 * @return void
 * @deprecated
 */
function my_custom_fonts() {
  echo '<style>
			.link-target {
				display: none !important;
			}
		</style>';
}
