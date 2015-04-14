<?php
/**
 * Template that forgoes calling the default genesis() function as we want normal processing of each page except only
 * want get_header() and get_footer() called once.
 *  We also want to add an extra div between pages (sections) with the site_layout class
 */
// List of pages
//TODO - admin menu to select pages and create this array
$br_pages = [7, 9, 14, 16, 18, 20];
$br_page = 0;

//take out the site_layout classes from body since they will be in the singe-page-section div
remove_filter( 'body_class', 'genesis_layout_body_classes' );

//using standard loop since we are performing the WP_Query
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'genesis_standard_loop' );

// add the pages layout as a class to the singe-page-section div
add_filter( "genesis_attr_single-page-section",'br_genesis_attr_single_page_section'); // $attributes, $context );

// remove default sidebar functions and use our own as the get_sidebar() function uses require_once which prevents
// each sections sidebars from showing except for the first section
remove_action('genesis_after_content', 'genesis_get_sidebar');
remove_action('genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');
add_action('genesis_after_content', 'br_genesis_get_sidebar');
add_action('genesis_after_content_sidebar_wrap', 'br_genesis_get_sidebar_alt');

function br_genesis_attr_single_page_section($attributes) {
	global $section, $br_page;
	$site_layout = br_get_site_layout($br_page);
	$attributes['class'] = $attributes['class'] . ' section'. $section . ' ' . $site_layout;
	return $attributes;
}

global $wp_query;

$orig_wp_query = $wp_query;

get_header();

$section = 0;
foreach ($br_pages as $page) {

	global $wp_query;
	$section += 1;
	$br_page = $page;
	$wp_query = new WP_Query(array('page_id' => $br_page, 'post_type' => 'page'));
	// div to separate sections of this single page web site
	genesis_markup( array(
		'html5'   => '<div %s id="single-page-section-' . $section . '">',
		'xhtml'   => '<div id="single-page-section-' . $section . '">',
		'context' => 'single-page-section',
	) );

		do_action( 'genesis_before_content_sidebar_wrap' );
		genesis_markup( array(
			'html5'   => '<div %s>',
			'xhtml'   => '<div>',
			'context' => 'content-sidebar-wrap',
		) );

		do_action( 'genesis_before_content' );
		genesis_markup( array(
			'html5'   => '<main %s>',
			'xhtml'   => '<div>',
			'context' => 'content',
		) );
		do_action( 'genesis_before_loop' );
		do_action( 'genesis_loop' );
		do_action( 'genesis_after_loop' );

		genesis_markup( array(
			'html5' => '</main>', //* end .content
			'xhtml' => '</div>', //* end #content
		) );
		do_action( 'genesis_after_content' );

		echo '</div>'; //* end .content-sidebar-wrap or #content-sidebar-wrap
		do_action( 'genesis_after_content_sidebar_wrap' );

	//end of section
	genesis_markup( array(
		'html5' => '</div>', //* end .single-page-section-#
		'xhtml' => '</div>', //* end #single-page-section-#
	) );

}
wp_reset_postdata();
$wp_query = $orig_wp_query;

get_footer();


/**
 * br_reset_sidebars() - was used to remove any hooks to the sidebars between pages
 * see br_site_sidebars() for details on why we changed this and no longer use it.
 *
 * Left for reference
 *
 */

/*function br_reset_sidebars () {
	remove_action( 'genesis_before_content_sidebar_wrap', 'br_genesis_get_sidebar_alt' );

	remove_action( 'genesis_after_content_sidebar_wrap', 'br_genesis_get_sidebar_alt' );

	remove_action( 'genesis_after_content', 'br_genesis_get_sidebar' );

	remove_action( 'genesis_before_content', 'br_genesis_get_sidebar' );
}*/



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