<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter( 'gallery_style', 'bfg_gallery_style' );
/**
 * Remove the injected styles for the [gallery] shortcode
 *
 * @since 1.x
 */
function bfg_gallery_style( $css ) {

	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );

}

/**
 * Allow pages to have excerpts
 *
 * @since 2.2.5
 */
// add_post_type_support( 'page', 'excerpt' );

// add_filter( 'the_content_more_link', 'bfg_more_tag_excerpt_link' );
/**
 * Customize the excerpt text, when using the <!--more--> tag
 *
 * See: http://my.studiopress.com/snippets/post-excerpts/
 *
 * @since 2.0.16
 */
function bfg_more_tag_excerpt_link() {

	return ' <a class="more-link" href="' . get_permalink() . '">Read more &rarr;</a>';

}

// add_filter( 'excerpt_more', 'bfg_truncated_excerpt_link' );
// add_filter( 'get_the_content_more_link', 'bfg_truncated_excerpt_link' );
/**
 * Customize the excerpt text, when using automatic truncation
 *
 * See: http://my.studiopress.com/snippets/post-excerpts/
 *
 * @since 2.0.16
 */
function bfg_truncated_excerpt_link() {

	return '... <a class="more-link" href="' . get_permalink() . '">Read more &rarr;</a>';

}

// remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
// add_filter( 'genesis_post_info', 'bfg_post_info' );
/**
 * Customize the post info text
 *
 * See:http://www.briangardner.com/code/customize-post-info/
 *
 * @since 2.0.0
 */
function bfg_post_info() {

	return '[post_date] by [post_author_posts_link] [post_comments] [post_edit]';
	// Friendly note: use [post_author] to return the author's name, without an archive link

}

// remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
// add_filter( 'genesis_post_meta', 'bfg_post_meta' );
/**
 * Customize the post meta text
 *
 * See:http://www.briangardner.com/code/customize-post-meta/
 *
 * @since 2.0.0
 */
function bfg_post_meta() {

	return '[post_categories before="Filed Under: "] [post_tags before="Tagged: "]';

}

// add_filter ( 'genesis_prev_link_text' , 'bfg_prev_link_text' );
/**
 * Customize the post navigation prev text
 * (Only applies to the 'Previous/Next' Post Navigation Technique, set in Genesis > Theme Options)
 *
 * @since 2.0.0
 */
function bfg_prev_link_text( $text ) {

	return html_entity_decode('&#10216;') . ' ';

}

// add_filter ( 'genesis_next_link_text' , 'bfg_next_link_text' );
/**
 * Customize the post navigation next text
 * (Only applies to the 'Previous/Next' Post Navigation Technique, set in Genesis > Theme Options)
 *
 * @since 2.0.0
 */
function bfg_next_link_text( $text ) {

	return ' ' . html_entity_decode('&#10217;');

}

/**
 * Remove the post title
 *
 * @since 2.0.9
 */
// remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

/**
 * Remove the post edit links (maybe you just want to use the admin bar)
 *
 * @since 2.0.9
 */
add_filter( 'edit_post_link', '__return_false' );

/**
 * Hide the author box
 *
 * @since 2.0.18
 */
// add_filter( 'get_the_author_genesis_author_box_single', '__return_false' );
// add_filter( 'get_the_author_genesis_author_box_archive', '__return_false' );

/**
 * Adjust the default WP password protected form to support keeping the input and submit on the same line
 *
 * @since 2.2.18
 */
add_filter( 'the_password_form', 'bfg_password_form' );
function bfg_password_form( $post = 0 ) {

	$post = get_post( $post );
	$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">';
		$output .= '<input name="post_password" id="' . $label . '" type="password" size="20" placeholder="Password">';
		$output .= '<input type="submit" name="Submit" value="' . esc_attr__( 'Submit' ) . '">';
	$output .= '</form>';
	return $output;

}


// Pagination formatted as Bootstrap expects

remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action('genesis_after_endwhile', 'bfg_posts_nav');

/**
 * Replaces genesis_posts_nav() to direct code to our bootstrap pagination code
 *
 * Original code does not contain necessary hooks to alter html markup so we will
 * overwrite them.
 *
 *
 * @uses genesis_get_option()            Get theme setting value.
 * @uses bfg_prev_next_posts_nav()   Prev and Next links.
 * @uses bfg_numeric_posts_nav()     Numbered links.
 */
function bfg_posts_nav() {

	if ( 'numeric' === genesis_get_option( 'posts_nav' ) )
		bfg_numeric_posts_nav();
	else
		bfg_prev_next_posts_nav();

}

function bfg_prev_next_posts_nav() {

	$prev_link = get_previous_posts_link( apply_filters( 'genesis_prev_link_text', '&#x000AB;' . __( 'Previous Page', 'genesis' ) ) );
	$next_link = get_next_posts_link( apply_filters( 'genesis_next_link_text', __( 'Next Page', 'genesis' ) . '&#x000BB;' ) );

	$prev = $prev_link ? '<li class="previous">' . $prev_link . '</li>' : '';
	$next = $next_link ? '<li class="next">' . $next_link . '</li>' : '';

	$nav = genesis_markup( array(
		'html5'   => '<nav>',
		'xhtml'   => '<div class="navigation">',
	//	'context' => 'archive-pagination',
		'echo'    => false,
	) );
    $nav .= '<div class="container-fluid"><ul class="pager">';
	$nav .= $prev;
	$nav .= $next;
	$nav .= '</div></ul>';
	$nav .= genesis_markup( array(
		'html5' => '</nav>',
		'xhtml' => '</div>',
		'echo'  => false
	) );

	if ( $prev || $next )
		echo $nav;
}

function bfg_numeric_posts_nav() {
    //$range determines how many pages to show on pagination
	//TODO Possible admin Interface to add this
	$range = 5;

	if( is_singular() )
		return;

	global $wp_query;

	//* Stop execution if there's only 1 page
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );
	$mid = round($range/2);
	$midf = floor($range/2);
	if( $max <= $range ) {
		$start = 1;
		$end = $max;
	} elseif ( $paged > $mid && ($paged+$midf) <= $max) {
		$start = $paged - $midf;
		$end = $range % 2 == 0 ? $paged + $midf - 1 : $paged + $midf;
	}elseif ($paged <= $mid ){
		$start = 1;
		$end = $range;
	} else {
		$start = $max - $range + 1;
		$end = $max;
	}
    for( $i = $start; $i <= $end ; $i++) {
	    $links[] = $i;
    }
	genesis_markup( array(
		'html5'   => '<nav>',
		'xhtml'   => '<div class="navigation">',
		'context' => '',
	) );

	echo '<div class="container-fluid"><ul class="pagination">';

	if ( ! in_array( 1, $links )) {
		printf( '<li><a href="%s">%s</a></li>' . "\n", esc_url( get_pagenum_link( 1 ) ), '&#x000AB;' );
	}

	//* Previous Post Link
	$class = get_previous_posts_link() ? '' : 'class="disabled"';
	printf( '<li ' . $class . '>%s</li>' . "\n", get_previous_posts_link( apply_filters( 'genesis_prev_link_text', __( 'Previous', 'genesis' ) ) ) );


	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li %s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	//* Next Post Link
	$class = get_next_posts_link() ? '' : 'class="disabled"';
	printf( '<li ' . $class .'>%s</li>' . "\n", get_next_posts_link( apply_filters( 'genesis_next_link_text', __( 'Next', 'genesis' ) ) ) );

	if ( ! in_array( $max, $links )) {
		printf( '<li><a href="%s">%s</a></li>' . "\n", esc_url( get_pagenum_link( $max ) ), '&#x000BB;' );
	}
	echo '</ul></div>' . "\n";

	genesis_markup( array(
		'html5'   => '</nav>',
		'xhtml'   => '</div>',
		'context' => '',
	) );
}