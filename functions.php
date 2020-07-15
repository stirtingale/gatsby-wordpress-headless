<?php

add_theme_support( 'custom-logo' );
add_theme_support( 'menus' );

/* ========================================================================================================================

Register Navigation 

======================================================================================================================== */

register_nav_menus(array('primary' => 'Primary Navigation'));

/* ========================================================================================================================

Plugin Requirements 

======================================================================================================================== */

require_once( 'tgm/class-tgm-plugin-activation.php' );

add_action( 'tgmpa_register', 'stirtingale_required_register_required_plugins' );

function stirtingale_required_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'        => 'Advanced Custom Fields PRO',
			'slug'        => 'advanced-custom-fields-pro',
			'is_callable' => 'wpseo_init',
		),
		array(
			'name'        => 'WP API Menus',
			'slug'        => 'wp-api-menus'
		),
		// array(
		// 	'name'        => 'Intagrate',
		// 	'slug'        => 'instagrate-pro'
		// ),
	);

	$config = array(
		'id'           => 'stirtingale',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
/* ========================================================================================================================

ACF Options

======================================================================================================================== */

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	// acf_add_options_sub_page(array(
	// 	'page_title' 	=> 'Theme Header Settings',
	// 	'menu_title'	=> 'Header',
	// 	'parent_slug'	=> 'theme-general-settings',
	// ));
	
	// acf_add_options_sub_page(array(
	// 	'page_title' 	=> 'Theme Footer Settings',
	// 	'menu_title'	=> 'Footer',
	// 	'parent_slug'	=> 'theme-general-settings',
	// ));
	
}

/* ========================================================================================================================

ACF JSON

======================================================================================================================== */

add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point( $path ) {
    // update path
    $path = get_stylesheet_directory() . '/acf-json';
    // return
    return $path;
}

add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point( $paths ) {
    // remove original path (optional)
    unset($paths[0]);
    // append path
    $paths[] = get_stylesheet_directory() . '/acf-json';
    // return
    return $paths;
}

/* ========================================================================================================================

Hide Backend

======================================================================================================================== */

add_filter('acf/settings/show_admin', 'my_acf_show_admin');

function my_acf_show_admin( $show ) { 
	// where X equals tjhole user id
	if ( get_current_user_id() == "1" ){
		return true; // show it
	}
	else {
		return false; // hide it
	}

}

/* ========================================================================================================================

Customise Gut.

======================================================================================================================== */

//  EXPAND BACKEND

function custom_admin_css() {
echo '<style type="text/css">
.wp-block { max-width: calc(100% - 3.6rem ); }
</style>';
}
add_action('admin_head', 'custom_admin_css');

// CREATE CUSTOM CAT

function custom_blocks_dsa( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'stirtingale',
				'title' => __( 'Stirtingale Custom Blocks', 'Stirtingale Custom' ),
			),
		)
	);
}
add_filter( 'block_categories', 'custom_blocks_dsa', 10, 2);


add_filter( 'allowed_block_types', 'acf_restrict_default_block_types' );
 
function acf_restrict_default_block_types( $allowed_blocks ) {
	
	global $post;
	
	$allowed_blocks = array(
		'core/image',
		'core/paragraph',
		'core/heading',
		'core/list',
	);
 
	if( $post->post_type === 'page' ) {
		$allowed_blocks[] = 'acf/custom';
	}
 
	return $allowed_blocks;

}

/* ========================================================================================================================

Custom Blocks

======================================================================================================================== */

add_action('acf/init', 'block_custom');
function block_custom() {

    // check function exists.
    if( function_exists('acf_register_block_type') ) {

        acf_register_block_type(array(


            'name'              => 'custom',
            'title'             => __('Custom Block Title'),
			'icon' => '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M1.604 24c1.853-2.784 7.647-8.21 13.919-9.494l.525 3.276c-3.773.264-9.01 2.523-14.444 6.218zm-1.604-1c2.037-2.653 6.013-6.906 6.226-15.092l-3.271.561c.418 4.888-1.546 10.626-2.955 14.531zm20.827-11.423l.802 2.4 2.371.883-2.035 1.504-.107 2.528-2.06-1.471-2.437.68.763-2.413-1.4-2.109 2.531-.02 1.572-1.982zm-11.911 3.677h-.018c-.268 0-.49-.213-.499-.483-.098-2.877.511-4.87 3.798-5.24 1.953-.219 2.029-1.116 2.135-2.357.099-1.171.235-2.775 2.737-2.959 1.23-.09 1.908-.307 2.267-.725.407-.475.528-1.357.403-2.948-.022-.275.184-.516.459-.538.254-.019.516.184.537.46.151 1.906-.035 2.972-.64 3.678-.556.647-1.411.957-2.953 1.07-1.651.122-1.712.846-1.814 2.046-.106 1.247-.251 2.956-3.02 3.267-2.33.262-3.011 1.247-2.91 4.212.01.276-.207.507-.482.517zm12.084-9.254c1.104 0 2 .896 2 2s-.896 2-2 2-2-.896-2-2 .896-2 2-2zm-13.715-4.058l-2.531.017-1.601-1.959-.766 2.412-2.359.918 2.058 1.473.144 2.527 2.037-1.501 2.447.643-.798-2.401 1.369-2.129zm3.715.058c1.104 0 2 .896 2 2s-.896 2-2 2-2-.896-2-2 .896-2 2-2z"/></svg>',
            'mode'				=> 'edit',
            'render_template'   => 'acf-blocks/custom.php',
            'category'          => 'stirtingale',
            'supports' => array(
	            'align' => false,
	            'multiple' => false,
	            'mode' => false,
            ),

        ));
    }
}