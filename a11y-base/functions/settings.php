<?php 
// Add tracking code option to general settings
// Add all your sections, fields and settings during admin_init
// 

function custom_settings_api_init() {
	// Add the section to reading settings so we can add our
	// fields to it
	add_settings_section(
		'tracking_setting',
		'Tracking',
		'tracking_setting_callback_function',
		'general'
);
	
	// Add the field with the names and function to use for our new
	// settings, put it in our new section
	add_settings_field(
		'tracking_input',
		'Analytics Code',
		'custom_setting_callback_function',
		'general',
		'tracking_setting'
);
	
	// Register our setting so that $_POST handling is done for us and
	// our callback function just has to echo the <inputs>
	register_setting( 'general', 'tracking_input' );
} // custom_settings_api_init()

add_action( 'admin_init', 'custom_settings_api_init' );


// ------------------------------------------------------------------
// Settings section callback function
// This function is needed if we added a new section. This function will be run at the start of our section

function tracking_setting_callback_function() {
	echo '<p>Enter your tracking code for this site</p>';
}
//

function custom_setting_callback_function() {
	echo '<textarea id="tracking_input" name="tracking_input" rows="5" cols="50">' . get_option( 'tracking_input' ) . '</textarea>';  

}