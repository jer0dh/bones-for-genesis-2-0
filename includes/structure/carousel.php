<?php
/**
 * Carousel code.
 * Integrate Twitter Bootstraps carousel code into a shortcode
 * Ability to provide a list of image id's, post id's, or a category.
 * If category, all posts with that category and the "slide" category, will be selected.
 * Feature image is used from the post
 *
 * Since version: 2.4.3
 */
add_shortcode( 'carousel', 'bfg_carousel');

$bfg_slideId = get_cat_ID('slide');

function bfg_carousel($atts, $content=null) {
	global $bfg_slideId;

	$options = shortcode_atts(array (
		'name'                  =>      'carousel',  //multiple carousel's on same page need diff names
		'posts'                 =>      '',
		'category'              =>      '',     //posts in this and 'slide' category
		'title'                 =>      false,
		'excerpt'               =>      false,
		'content'               =>      false,
		'indicators'            =>      true,
		'controls'              =>      true,
		'images'                =>      '',
		'left_control_class'    =>      'glyphicon glyphicon-chevron-left',
		'right_control_class'   =>      'glyphicon glyphicon-chevron-right',
		'image_link_to_post'    =>      true,
		'title_link_to_post'    =>      true,
		'link_to_same_window'   =>      true,
		'excerpt_link_to_post'  =>      true,
		'interval'              =>      5000, //ms
		'pause'                 =>      'hover',
		'wrap'                  =>      true,
		'keyboard'              =>      true
	), $atts, 'bfg_carousel');  //3rd arg for 'shortcode_atts_{$shortcode} filtering

	$bool_values = array('title','excerpt','content','indicators','controls','image_link_to_post','title_link_to_post', 'link_to_same_window','excerpt_link_to_post','wrap','keyboard');

	foreach($bool_values as $key) {
		$options[$key] = filter_var( $options[$key], FILTER_VALIDATE_BOOLEAN);  //thx to kaiser http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
	}


	//check category, posts, and images.
	$image = false;
	if ($options['category'] !== '') {
		$cat = get_cat_ID( $options['category']);
		$the_args = array('category__and' => array( $bfg_slideId, $cat), 'posts_per_page' => -1, 'post_type' => 'post');
	} elseif ($options['posts'] !== '') {
		$posts = array_map( 'intval', array_filter( explode(',', $options['posts']), 'is_numeric' ) ); //thx Dmitry Dubovitsky
		$the_args = array( 'post_type' => 'post', 'post__in' => $posts, 'orderby' => 'post__in'); //'post_mime_type' => 'image',
	} elseif ($options['images'] !== ''){

		$images = array_map( 'intval', array_filter( explode(',', $options['images']), 'is_numeric' ) );
		$the_args = array( 'post_type' => 'attachment',  'post_status' => 'inherit', 'post__in' => $images, 'orderby' => 'post__in'); //'post_mime_type' => 'image',
		$image = true;
	} else {

		return "<p>carousel shortcode needs either category, posts, or images options filled out</p>";
	}

	//create wpquery loop based on above
	$custom_query = new WP_Query($the_args);
	$output = '<div id="'. $options['name'].'" class="carousel slide" data-ride="carousel"';
	$output .= ' data-interval="' . $options['interval'] . '"';
    $output .= ' data-pause="' . $options['pause'] . '"';
	$output .= ' data-wrap="' . $options['wrap'] . '"';
    $output .= ' data-keyboard="' . $options['keyboard'] . '">';
	if($options['indicators']) {
		$output .= ' <ol class="carousel-indicators"> ';
		$postCount = 0;
		while ( $custom_query->have_posts() ) : $custom_query->the_post();

			$output .= '<li data-target="#' . $options['name'] . '" data-slide-to="' . $postCount . '"';
			if ( $postCount == 0 ) {
				$output .= ' class="active" ';
			}
			$output .= '></li>';

			++ $postCount;
		endwhile;
		$custom_query->rewind_posts();
		$output .= '</ol> ';
	}
	$output .= '<div class="carousel-inner" role="listbox"> ';
	$postCount = 0;
	while ($custom_query->have_posts()) : $custom_query->the_post();
	    $output .= '<div class="item ' . (($postCount++ == 0)?'active':'' ). '">';
		if ($image) {
			$image_src = wp_get_attachment_url();
		} else {
			$image_src = wp_get_attachment_url( get_post_thumbnail_id());
		}
		if($options['image_link_to_post']) {
			$output .= '<a href="'. get_the_permalink() . '"' . (($options['link_to_same_window'])?'':' target="_blank" ') . '>';
			$output .= '<img src="' . $image_src . '">';
			$output .= '</a>';
		} else {
			$output .= '<img src="' . $image_src . '">';
		}
		if($options['title'] || $options['excerpt'] || $options['content']) {
			$output .= '<div class="carousel-caption">';
			if($options['title']){
				if($options['title_link_to_post']){
					$output .= '<a href="'. get_the_permalink() . '"' . (($options['link_to_same_window'])?'':' target="_blank" ') . '>';
					$output .= '<h3>' . get_the_title() . '</h3>';
					$output .= '</a>';
				} else {
					$output .= '<h3>' . get_the_title() . '</h3>';
				}
			}
			if($options['excerpt']){
				if($options['excerpt_link_to_post']){
					$output .= '<a href="'. get_the_permalink() . '"' . (($options['link_to_same_window'])?'':' target="_blank" ') . '>';
					$output .= '<p>' . get_the_excerpt() . '</p>';
					$output .= '</a>';
				} else {
					$output .= '<p>' . get_the_excerpt() . '</p>';
				}
			}
			if($options['content']){
				if($options['excerpt_link_to_post']){
					$output .= '<a href="'. get_the_permalink() . '"' . (($options['link_to_same_window'])?'':' target="_blank" ') . '>';
					$output .= '<p>' . get_the_content() . '</p>';
					$output .= '</a>';
				} else {
					$output .= '<p>' . get_the_content() . '</p>';
				}
			}

			$output .= '</div>';
		}
		$output .= '</div>';
	endwhile;
	$output .= '</div>';

	if($options['controls']){
		$output .= '<a class="left carousel-control" href="#' . $options['name'] .'" role="button" data-slide="prev">';
		$output .= '<span class="'. $options['left_control_class'].'" aria-hidden="true"></span>';
		$output .= '<span class="sr-only">Previous</span> </a>';
		$output .= '<a class="right carousel-control" href="#' . $options['name'] .'" role="button" data-slide="next">';
		$output .= '<span class="'. $options['right_control_class'].'" aria-hidden="true"></span>';
		$output .= '<span class="sr-only">Next</span> </a>';
	}
	$output .= '</div>';
	//reset wpquery after loop done
	wp_reset_postdata();
	return $output;
}