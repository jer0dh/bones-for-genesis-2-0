<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Initialize Genesis
require_once( get_template_directory() . '/lib/init.php' );

// Child theme definitions
define( 'CHILD_THEME_NAME', 'Bones for Genesis 2.0' );
define( 'CHILD_THEME_URL', 'http://bonesforgenesis.com/' );
define( 'CHILD_THEME_VERSION', '2.3.1' );

// Developer Tools
require_once( CHILD_DIR . '/includes/developer-tools.php' );		// DO NOT USE THESE ON A LIVE SITE

// Genesis
require_once( CHILD_DIR . '/includes/genesis.php' );				// Customizations to Genesis-specific functions

// Admin
require_once( CHILD_DIR . '/includes/admin/admin-functions.php' );	// Customization to admin functionality
require_once( CHILD_DIR . '/includes/admin/admin-views.php' );		// Customizations to the admin area display
require_once( CHILD_DIR . '/includes/admin/admin-branding.php' );	// Admin view customizations that specifically involve branding
require_once( CHILD_DIR . '/includes/admin/admin-options.php' );	// For adding/editing theme options to Genesis

// Structure (corresponds to Genesis's lib/structure)
require_once( CHILD_DIR . '/includes/structure/archive.php' );
require_once( CHILD_DIR . '/includes/structure/comments.php' );
require_once( CHILD_DIR . '/includes/structure/footer.php' );
require_once( CHILD_DIR . '/includes/structure/header.php' );
require_once( CHILD_DIR . '/includes/structure/layout.php' );
require_once( CHILD_DIR . '/includes/structure/loops.php' );
require_once( CHILD_DIR . '/includes/structure/menu.php' );
require_once( CHILD_DIR . '/includes/structure/post.php' );
require_once( CHILD_DIR . '/includes/structure/search.php' );
require_once( CHILD_DIR . '/includes/structure/sidebar.php' );

// Shame
require_once( CHILD_DIR . '/includes/shame.php' );					// For new code snippets that haven't been sorted and commented yet

/*add_action('genesis_after_header', 'br_site_layout',10,0);
remove_action('genesis_after_content', 'genesis_get_sidebar');
remove_action('genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');*/

/**
 * br_site_layout - used to change the order of the sidebar HTML markup in an attempt to use bootstrap grid
 * Determined though, that the original placement of sidebars was the correct placement for mobile:
 * ie Secondary sidebar below content and primary sidebar.   Also, primary sidebar below content.
 * Discovered we could use Bootstrap's push and pull classes to change order in how they are viewed for
 * desktop
 *
 * So this function is not used.  Left for reference.
 */
/*function br_site_layout() {
	if (func_num_args() > 0) {
		$site_layout = br_get_site_layout(func_get_arg(0));
	} else {
		$site_layout = br_get_site_layout();
	}
// Secondary Sidebar
	if ( in_array( $site_layout, array( 'sidebar-content-sidebar', 'sidebar-sidebar-content' ) ) ) {
		add_action( 'genesis_before_content_sidebar_wrap', 'br_genesis_get_sidebar_alt' );
	}
	if ( $site_layout == 'content-sidebar-sidebar' ) {
		add_action( 'genesis_after_content_sidebar_wrap', 'br_genesis_get_sidebar_alt' );
	}
// Primary Sidebar
	if ( in_array( $site_layout, array( 'sidebar-content-sidebar', 'content-sidebar', 'content-sidebar-sidebar' ) ) ) {
		add_action( 'genesis_after_content', 'br_genesis_get_sidebar' );
	}
	if ( in_array( $site_layout, array( 'sidebar-sidebar-content', 'sidebar-content' ) ) ) {
		add_action( 'genesis_before_content', 'br_genesis_get_sidebar' );
	}
}*/

/**
 * br_get_site_layout() - used to obtain the layout of a page because genesis_site_layout() would only return
 * the page's layout if within the loop, but we need the page's layout before the_post() was called in the loop
 *
 * If no Post ID is supplied when called, then acts like genesis_site_layout()
 * Optional Parameter: Integer (Post ID)
 * @return mixed|string
 */

function br_get_site_layout() {
	if (func_num_args() > 0) {

		$custom_layout = get_post_meta( func_get_arg(0), '_genesis_layout', true);

		$site_layout = $custom_layout ? $custom_layout : genesis_get_option( 'site_layout' );
	} else {
		$site_layout = genesis_site_layout();
	}

	return $site_layout;
}


function br_get_sidebar( $name = null ) {
	/**
	 * Fires before the sidebar template file is loaded.
	 *
	 * Replacing WordPress get_slider so locate_template can be called with Require_once = false
	 * otherwise only the first section will get the sidebars.
	 */
	do_action( 'get_sidebar', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "sidebar-{$name}.php";

	$templates[] = 'sidebar.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true, false))  // added false to change default of requireOnce
		load_template( ABSPATH . WPINC . '/theme-compat/sidebar.php');
}

/**
 * Replacing the genesis_get_sidebar() so to call the br_get_sidebar() instead of the WordPress get_slider()
 *
 */
function br_genesis_get_sidebar() {

	// false says no caching of the site_layout
	$site_layout = genesis_site_layout(false);
	//* Don't load sidebar on pages that don't need it
	if ( 'full-width-content' === $site_layout )
		return;
	br_get_sidebar();

}
/**
 * Replacing the genesis_get_sidebar_alt() so to call the br_get_sidebar() instead of the WordPress get_slider()
 *
 */
function br_genesis_get_sidebar_alt() {

	$site_layout = genesis_site_layout(false);

	//* Don't load sidebar-alt on pages that don't need it
	if ( in_array( $site_layout, array( 'content-sidebar', 'sidebar-content', 'full-width-content' ) ) )
		return;
	br_get_sidebar( 'alt' );

}