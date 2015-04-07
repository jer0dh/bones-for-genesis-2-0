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