<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Integrating Bootstrap NAVs
 *
 * Tab and Pill navs require role='presentation' as an attribute to the <li> of a menu item
 * Currently, there is no filter to add an attribute to the <li> (only added id or class)
 * One way to do this would be to override the start_el function but seems like a lot of code to
 * copy to only add an attribute.  Will pursue a simple jQuery solution in script.js.
 *
 * fixed-top and fixed-bottom require padding-top and padding-bottom, respectively.  This is also
 * added via jQuery in script.js.
 */
//Menu variables:
//An admin interface could be created to change these values on the dashboard
$br_genesis_menu = false;
$br_menu_type = 'navbar'; //Possible values: tab, pill, navbar
$br_navbar_type = 'fixed-top'; //Possible values: static-top, fixed-top, fixed-bottom
$br_navbar_align = 'right';


// remove_action( 'genesis_after_header', 'genesis_do_subnav' );


if (!$br_genesis_menu) {

	//Use different hook if nav needs to be elsewhere
	remove_action( 'genesis_after_header', 'genesis_do_nav' );
	add_action( 'genesis_after_header', 'br_custom_primary_do_nav', 5 );


	add_filter('nav_menu_link_attributes', 'br_link_attributes',10,2);
	add_filter('nav_menu_css_class', 'br_add_dropdown_active',10,4);

	if ( $br_navbar_type === 'fixed-bottom' ) {
		add_filter( 'body_class', 'br_fixed_bottom_body_class' );
	}
	if ( $br_navbar_type === 'fixed-top' ) {
		add_filter( 'body_class', 'br_fixed_top_body_class' );
	}
}

function br_fixed_bottom_body_class( $classes) {
	$classes[] = 'fixed-bottom';
	return $classes;
}
function br_fixed_top_body_class( $classes) {
	$classes[] = 'fixed-top';
	return $classes;
}

/** Create primary nav using bootstrap.  If br_genesis_menu, then call genesis_do_nav  */
function br_custom_primary_do_nav() {
	global $br_genesis_menu, $br_menu_type, $br_navbar_type, $br_navbar_align;
	//If genesis menu - genesis_do_nav
	//else lookup type of bootstrap menu and create items_wrap string,container_class, and menu_class based on it
	//call wp_nav_menu

	//Default values
	$container_class = '';
	$menu_class = 'nav nav-pills';
	$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';

	switch ($br_menu_type) {
		case 'pill':
			$menu_class = 'nav nav-pills';
			$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
			break;
		case 'tab':
			$menu_class = 'nav nav-tabs';
			$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
			break;
		case 'navbar':
			$container_class = 'navbar navbar-default navbar-' . sanitize_text_field($br_navbar_type);
			$menu_class = 'nav navbar-nav navbar-' . sanitize_text_field($br_navbar_align);
			$items_wrap = '<div class="container-fluid"><div class="navbar-header"> <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> </div> <div class="collapse navbar-collapse" id="navbar-collapse-1"><ul id="%1$s" class="%2$s">%3$s</ul></div></div>';
			break;
	}
	    wp_nav_menu( array(
		    'container'       => 'nav',
		    'container_class' => $container_class,
		    'menu_class'      => $menu_class,
		    'items_wrap'      => $items_wrap,
		    'walker'          => new br_Walker_Nav_Menu(),
		    'theme_location'  => 'primary'
	    ) );
    }

/** Extends the Walker_Nav_menu and overrides the start_lvl function to add class to sub-menu */
class br_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu dropdown-menu\">\n";
	}
}

/** adds attributes to anchor, <a>, of a dropdown menu */
function br_link_attributes($atts,$item) {
	if(in_array('menu-item-has-children',$item->classes)) {
	    $atts["class"] = "dropdown-toggle";
		$atts["data-toggle"] = "dropdown";
	}
	return $atts;
}
/** adds the necessary classes bootstrap expects for menu items, <li>, with children and current-menu-* items */

function br_add_dropdown_active($classes, $item, $args, $depth) {

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
