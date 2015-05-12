<?php


//* Include widget class files
require_once( CHILD_DIR . '/includes/widgets/nav-menu-widget.php' );


add_action( 'widgets_init', 'bfg_load_widgets' );


function bfg_load_widgets() {

	register_widget( 'Bootstrap_Custom_Menu_Widget' );

}