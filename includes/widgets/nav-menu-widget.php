<?php
/**
* Navigation Menu widget class
*
* @since 3.0.0
*/
class Bootstrap_Custom_Menu_Widget extends WP_Widget {

	private static $menu_types = array('nav-pills', 'nav-tabs', 'navbar-nav');

	public function __construct() {
		$widget_ops = array( 'description' => __( 'Add a Bootstrap menu to your sidebar.' ) );
		parent::__construct( 'bootstrap_nav_menu', __( 'Bootstrap Custom Menu' ), $widget_ops );
	}

	public function widget( $args, $instance ) {

		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}
		$raw_title = sanitize_title_with_dashes (! empty( $instance['title'] ) ? $instance['title'] : 'no-title') ;
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['show_title'] = $instance['show_title'] ? true : false;
		echo $args['before_widget'];

		if ( $instance['show_title'] && ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$nav_menu_args = array(
			'fallback_cb' => '',
			'menu'        => $nav_menu
		);
		$bfg_menu_type = isset($instance['menu_type']) ? $instance['menu_type'] : self::$menu_types[0];
		$bfg_navbar_type = 'static-top';
		$bfg_navbar_align = isset($instance['navbar_align']) ? $instance['navbar_align'] : 'left';
		$container_class = '';
		$menu_class      = 'nav nav-pills';
		$items_wrap      = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
		switch ( $bfg_menu_type ) {
			case 'nav-pills':
				$menu_class = 'nav nav-pills';
				$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
				break;
			case 'nav-tabs':
				$menu_class = 'nav nav-tabs';
				$items_wrap = ' <div class="container-fluid"><ul id="%1$s" class="%2$s">%3$s</ul></div>';
				break;
			case 'navbar-nav':
				$container_class = 'navbar navbar-default navbar-' .  $bfg_navbar_type;
				$menu_class      = 'nav navbar-nav navbar-' . sanitize_text_field( $bfg_navbar_align );
				$navbar_content  = '';
				$items_wrap      = '<div class="container-fluid"><div class="navbar-header"> <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> ';
				$items_wrap .= apply_filters( 'bfg_navbar_brand_content-'.$raw_title, $navbar_content ) . '</div> <div class="collapse navbar-collapse" id="navbar-collapse-1">';
				$items_wrap .= apply_filters( 'bfg_navbar_content-'.$raw_title, $navbar_content ) . '<ul id="%1$s" class="%2$s">%3$s</ul></div></div>';
				break;
		}
		/**
		 * Filter the arguments for the Bootstrap Nav Menu widget.  Filter name based on widget title
		 *
		 * @since 4.2.0
		 *
		 * @param array $nav_menu_args {
		 *     An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
		 *
		 * @type callback|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
		 * @type mixed $menu Menu ID, slug, or name.
		 * }
		 *
		 * @param stdClass $nav_menu Nav menu object for the current menu.
		 * @param array $args Display arguments for the current widget.
		 */
		wp_nav_menu( apply_filters( 'widget_bootstrap_nav_menu_args-'.$raw_title, array(
			'menu'            => $nav_menu_args['menu'],
			'fallback_cb'     => $nav_menu_args['fallback_cb'],
			'container'       => 'nav',
			'container_class' => $container_class,
			'menu_class'      => $menu_class,
			'items_wrap'      => $items_wrap,
			'walker'          => new bfg_Walker_Nav_Menu(),
			'theme_location'  => 'widget'
		), $nav_menu, $args ) );

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		}
		if ( ! empty( $new_instance['nav_menu'] ) ) {
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		}
		if ( ! empty( $new_instance['show_title'] ) ) {
			$instance['show_title'] = $new_instance['show_title'];
		}

		if ( ! empty( $new_instance['menu_type'] ) ) {
			$sanitized_new = sanitize_text_field($new_instance['menu_type']);
		    if (in_array($sanitized_new,self::$menu_types) ) {
			    $instance['menu_type'] = $new_instance['menu_type'];
		    } else {
			    $instance['menu_type'] = self::$menu_types[0];
		    }
		}
		if ( ! empty( $new_instance['navbar_align'] ) ) {
			$sanitized_new = sanitize_text_field($new_instance['navbar_align']);
			if (in_array($sanitized_new,array('left','right')) ) {
				$instance['navbar_align'] = $new_instance['navbar_align'];
			} else {
				$instance['navbar_align'] = 'left';
			}
		}

		return $instance;
	}

	public function form( $instance ) {
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		$menu_type = isset( $instance['menu_type'] ) ? $instance['menu_type'] : self::$menu_types[0];
		$navbar_align = isset( $instance['navbar_align'] ) ? $instance['navbar_align'] : 'left';

		$instance['show_title'] =  $instance['show_title'] ? true : false;
// Get menus
		$menus = wp_get_nav_menus();

// If no menus exists, direct the user to go and create some.
		if ( ! $menus ) {
			echo '<p>' . sprintf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), admin_url( 'nav-menus.php' ) ) . '</p>';

			return;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"/>
			<label for="<?php echo $this->get_field_id( 'show_title'); ?>"><?php _e( 'Show?:') ?></label>
			<input type="checkbox" class="checkbox" <?php checked($instance['show_title'],true);?> name="<?php echo $this->get_field_name( 'show_title');?>" id="<?php echo $this->get_field_id('show_title');?>" />
			For php filter hooks: <strong><?php echo sanitize_title_with_dashes($instance['title']);?></strong>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>"
			        name="<?php echo $this->get_field_name( 'nav_menu' ); ?>">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
				<?php
				foreach ( $menus as $menu ) {
					echo '<option value="' . $menu->term_id . '"'
					     . selected( $nav_menu, $menu->term_id, false )
					     . '>' . esc_html( $menu->name ) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_type' ); ?>"><?php _e( 'Select Menu Type:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'menu_type' ); ?>"
			        name="<?php echo $this->get_field_name( 'menu_type' ); ?>"
			        onchange="bfg_navbar_change(this.options[this.selectedIndex].value)">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
				<?php

				foreach ( self::$menu_types as $type ) {
					echo '<option value="' . $type . '"'
					     . selected( $menu_type, $type, false )
					     . '>' . esc_html( $type ) . '</option>';
				}
				?>
				</select>
		</p>
		<p class="navAlign" style="display: <?php echo ($menu_type == self::$menu_types[2]) ? 'inline;' : 'none;' ; ?>">
			<label for="<?php echo $this->get_field_id( 'navbar_align' ); ?>"><?php _e( 'Select Navbar alignment:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'navbar_align' ); ?>"
			        name="<?php echo $this->get_field_name( 'navbar_align' ); ?>">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
				<?php

				foreach ( array('left','right') as $align ) {
					echo '<option value="' . $align . '"'
					     . selected( $navbar_align, $align, false )
					     . '>' . esc_html( $align ) . '</option>';
				}
				?>
			</select>
			<script>function bfg_navbar_change(val) {
					console.log('bfg_navbar_change: '+ val);
					if(val == '<?php echo self::$menu_types[2];?>'){
						jQuery('.navAlign').show();
					} else {
						jQuery('.navAlign').hide();
					}

					//if $menu_type changes.  If = navbar then show() navAlign, else hide()
				}

			</script>
		</p>

	<?php
	}
}
