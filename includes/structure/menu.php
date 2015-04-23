<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Breadcrumbs */
//* Reposition the breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );

//Changing breadcrumbs to Bootstrap's format
add_filter('genesis_breadcrumb_args', 'bfg_breadcrumb_args');
function bfg_breadcrumb_args($args){
	//create a separator of 5 spaces.  This won't be seen in rendered HTML
	//and allows me to search inside of a crumb when code adds two crumbs together
	//such as a single post where title is also added to category
	$args['sep'] = '     ';
	$args['prefix'] = sprintf( '<ol %s>', genesis_attr( 'breadcrumb' ) );
    $args['suffix'] = '</ol>';
	return $args;
}
add_filter('genesis_build_crumbs', 'bfg_build_crumbs',10,2 );
function bfg_build_crumbs($crumbs){

	foreach($crumbs as &$crumb){
		//Finds a double crumb and replaced separator with <li> tags
		$crumb = str_replace('     ','</li><li class="active">', $crumb);
        //If no anchor tag, then end of breadcrumb so add class of active
		$class = strpos($crumb, '</a>') ? '' : 'class="active"';
		$crumb = '<li ' . $class .'>' . $crumb . '</li>';
	}
	return $crumbs;
}

/** Integrating Bootstrap NAVs
 *
 * Tab and Pill navs require role='presentation' as an attribute to the <li> of a menu item
 * Currently, there is no filter to add an attribute to the <li> (only added id or class)
 * One way to do this would be to override the start_el function but seems like a lot of code to
 * copy to only add an attribute.  Created a simple jQuery solution in script.js.
 *
 * fixed-top and fixed-bottom require padding-top and padding-bottom, respectively.  This is also
 * added via jQuery in script.js since we can get the computed height of the nav and add the appropriate
 * padding.
 */
//Menu variables:
//An admin interface could be created to change these values on the dashboard
$bfg_genesis_menu = false;
$bfg_menu_type = 'navbar'; //Possible values: tab, pill, navbar
$bfg_navbar_type = 'static-top'; //Possible values: static-top, fixed-top, fixed-bottom
$bfg_navbar_align = 'right';


// remove_action( 'genesis_after_header', 'genesis_do_subnav' );


if (!$bfg_genesis_menu) {

	//Use different hook if nav needs to be elsewhere
	remove_action( 'genesis_after_header', 'genesis_do_nav' );
	add_action( 'genesis_after_header', 'bfg_custom_primary_do_nav', 5 );


	add_filter('nav_menu_link_attributes', 'bfg_link_attributes',10,2);
	add_filter('nav_menu_css_class', 'bfg_add_dropdown_active',10,4);

	add_filter('bfg_navbar_brand_content', 'bfg_navbar_brand');
	add_filter('bfg_navbar_content', 'bfg_navbar_search_form');

	if ( $bfg_navbar_type === 'fixed-bottom' ) {
		add_filter( 'body_class', 'bfg_fixed_bottom_body_class' );
	}
	if ( $bfg_navbar_type === 'fixed-top' ) {
		add_filter( 'body_class', 'bfg_fixed_top_body_class' );
	}
}

function bfg_fixed_bottom_body_class( $classes) {
	$classes[] = 'fixed-bottom';
	return $classes;
}
function bfg_fixed_top_body_class( $classes) {
	$classes[] = 'fixed-top';
	return $classes;
}

/** Create primary nav using bootstrap.   */
function bfg_custom_primary_do_nav() {
	global $bfg_menu_type, $bfg_navbar_type, $bfg_navbar_align;

	//If menu has not been assigned to Primary Navigation, abort.
	if ( ! has_nav_menu( 'primary' ) ) {
		return;
	}
	//Default values
	$container_class = '';
	$menu_class = 'nav nav-pills';
	$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
	switch ($bfg_menu_type) {
		case 'pill':
			$menu_class = 'nav nav-pills';
			$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
			break;
		case 'tab':
			$menu_class = 'nav nav-tabs';
			$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
			break;
		case 'navbar':
			$container_class = 'navbar navbar-default navbar-' . sanitize_text_field($bfg_navbar_type);
			$menu_class = 'nav navbar-nav navbar-' . sanitize_text_field($bfg_navbar_align);
			$navbar_content = '';
			$items_wrap = '<div class="container-fluid"><div class="navbar-header"> <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> ';
			$items_wrap .= apply_filters('bfg_navbar_brand_content',$navbar_content) . '</div> <div class="collapse navbar-collapse" id="navbar-collapse-1">';
			$items_wrap .= apply_filters('bfg_navbar_content', $navbar_content) . '<ul id="%1$s" class="%2$s">%3$s</ul></div></div>';
			break;
	}
	    wp_nav_menu( array(
		    'container'       => 'nav',
		    'container_class' => $container_class,
		    'menu_class'      => $menu_class,
		    'items_wrap'      => $items_wrap,
		    'walker'          => new bfg_Walker_Nav_Menu(),
		    'theme_location'  => 'primary'
	    ) );
    }

/** Extends the Walker_Nav_menu and overrides the start_lvl function to add class to sub-menu */
class bfg_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu dropdown-menu\">\n";
	}
}

/** adds attributes to anchor, <a>, of a dropdown menu */
function bfg_link_attributes($atts,$item) {
	if(in_array('menu-item-has-children',$item->classes)) {
	    $atts["class"] = "dropdown-toggle";
		$atts["data-toggle"] = "dropdown";
	}
	return $atts;
}
/** adds the necessary classes bootstrap expects for menu items, <li>, with children and current-menu-* items */

function bfg_add_dropdown_active($classes, $item, $args, $depth) {

	if(in_array('menu-item-has-children',$classes) ){
		$classes[] = 'dropdown';
	}
	if(in_array('current-menu-item',$classes) ){
		$classes[] = 'active';
	}
	if(in_array('current-menu-parent',$classes) ){
		$classes[] = 'active';
	}
	return $classes;
}
//TODO search submit button is slightly off if navbar-brand
//TODO need media query to change line-height when responsive menu is active
function bfg_navbar_search_form($navbar_content) {
	$url = get_home_url();

	$navbar_content .= '<form method="get" class="navbar-form navbar-left" action="' .  $url . '" role="search">';
	$navbar_content .= '<div class="form-group">';
	$navbar_content .= '<input class="form-control" name="s" placeholder="Search" type="text">';
	$navbar_content .= '</div>';
	$navbar_content .= '<button class="btn btn-default" value="Search" type="submit">Submit</button>';
	$navbar_content .= '</form>';

	return $navbar_content;
}

function bfg_navbar_brand($navbar_content) {
	//TODO admin interface to add image, brand, url to options
	$image = get_stylesheet_directory_uri() . '/images/logo.png';
	list($width, $height, $type, $attr) = getimagesize($image);

	$url = get_home_url();
    $brand = get_bloginfo('name');
	$navbar_content .= '<a class="navbar-brand" href="' . $url . '">';
	$navbar_content .= '   <img alt="' . $brand . '" src = "' . $image .'"> </a>';

	if ($height > 50) {
		$navbar_content .= '<style> .navbar-nav li a, .navbar-form { line-height: ' . $height . 'px;}';
		$navbar_content .= '.navbar-brand {height: inherit;}</style>';
	}

	return $navbar_content;
}