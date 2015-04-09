<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Remove the primary and secondary menus
 *
 * @since 2.0.9
 */
remove_action( 'genesis_after_header', 'genesis_do_nav' );
// remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_after_header', 'br_custom_do_nav' ,5);
//Use different hook if nav needs to be elsewhere

//add_action('genesis_after_header', 'br_test_nav');

//TODO add bootstrap.js in a better way than this
add_action('wp_enqueue_scripts','br_load_bootstrap');

function br_load_bootstrap(){
	wp_enqueue_script("bootstrap", get_stylesheet_directory_uri() . '/build/bootstrap.js', array('jquery'));
}

function br_test_nav(){
	?>

<nav class="navbar navbar-default">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Brand</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
				<li><a href="#">Link</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Action</a></li>
						<li><a href="#">Another action</a></li>
						<li><a href="#">Something else here</a></li>
						<li class="divider"></li>
						<li><a href="#">Separated link</a></li>
						<li class="divider"></li>
						<li><a href="#">One more separated link</a></li>
					</ul>
				</li>
			</ul>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="Search">
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#">Link</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Action</a></li>
						<li><a href="#">Another action</a></li>
						<li><a href="#">Something else here</a></li>
						<li class="divider"></li>
						<li><a href="#">Separated link</a></li>
					</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<?php
}

function br_custom_do_nav() {
	wp_nav_menu(array(
		'menu' => 'Primary Navigation',
		'container' => 'nav',
		'container_class' => 'navbar navbar-default navbar-static-top',
		'menu_class' => 'nav navbar-nav navbar-right',
		'menu_id' => 'navigation',
		'items_wrap' => ' <div class="container-fluid"> <div class="navbar-header"> <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> </div> <div class="collapse navbar-collapse" id="navbar-collapse-1"><ul id="%1$s" class="%2$s">%3$s</ul></div></div>',
		'walker' => new br_Walker_Nav_Menu() ));
}

class br_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu dropdown-menu\">\n";
	}
}
add_filter('nav_menu_link_attributes', 'br_link_attributes',10,2);
function br_link_attributes($atts,$item) {
	if(in_array('menu-item-has-children',$item->classes)) {
	    $atts["class"] = "dropdown-toggle";
		$atts["data-toggle"] = "dropdown";
	}
	return $atts;
}
add_filter('nav_menu_css_class', 'br_add_dropdown');
function br_add_dropdown($classes) {
	if(in_array('menu-item-has-children',$classes) ){
		$classes[] = 'dropdown';
	}
	return $classes;
}