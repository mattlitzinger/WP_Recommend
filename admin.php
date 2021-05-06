<?php 

/**
 * Output page HTML markup
 */
function wp_recommend_admin_page(){
	?>
	<div class="wrap">
		<h2>WP Recommend</h2>
		<form method="post" action="options.php"> 
			<?php settings_fields('wp_recommend_settings'); ?>
			<?php do_settings_sections('wp_recommend_settings'); ?>
			<?php submit_button(); ?>
		</form>
	</div>

	<script>
		(function($) {
			$(function() {
				$('input#likes_disable_labels').change(function() {
					console.log('Checked');
					if( $(this).is(':checked') ) {
						$('input#likes_singular_label, input#likes_plural_label').prop('disabled', true);
					} else {
						$('input#likes_singular_label, input#likes_plural_label').prop('disabled', false);
					}
				});
			});
		})(jQuery);
	</script>
	<?php
}

function wp_recommend_admin_menu(){
	add_options_page('WP Recommend', 'WP Recommend', 'manage_options', 'wp-recommend', 'wp_recommend_admin_page');
}
add_action( 'admin_menu', 'wp_recommend_admin_menu' );

/**
 * Register the settings
 */
function wp_recommend_register_settings() {

  // Add settings section
  add_settings_section( 'wp_recommend_settings_section', '', 'wp_recommend_settings_section_callback', 'wp_recommend_settings' );

  // Icon Type Field
  add_settings_field( 'wp_recommend_likes_icon_type', 'Icon Type', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'radio_button',
      'id' => 'icon_type',
      'values' => array(
      	'heart' => 'Heart',
      	'thumbs_up' => 'Thumbs Up',
      )
  ) );

  // Disable Labels Field
  add_settings_field( 'wp_recommend_likes_disable_label', 'Disable Labels', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'checkbox',
      'id' => 'likes_disable_labels',
      'desc' => 'This will disable all recommend/like labels.'
  ) );

  // Label (Singular) Field
  add_settings_field( 'wp_recommend_likes_singular_label', 'Label (Singular)', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'label_text',
      'id' => 'likes_singular_label',
  ) );

  // Label (Plural) Field
  add_settings_field( 'wp_recommend_likes_plural_label', 'Label (Plural)', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'label_text',
      'id' => 'likes_plural_label',
  ) );

  // Remove CSS Field
  add_settings_field( 'wp_recommend_remove_css', 'Remove CSS', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'checkbox',
      'id' => 'remove_css',
      'desc' => 'This will disable all plugin CSS. Custom styles below will still be used.'
  ) );

  // Custom Styles Field
  add_settings_field( 'wp_recommend_custom_styles', 'Custom Styles', 'wp_recommends_display_setting', 'wp_recommend_settings', 'wp_recommend_settings_section', array(
      'type' => 'textarea',
      'id' => 'custom_styles',
  ) );

  // Register the settings with validation callback
  register_setting( 'wp_recommend_settings', 'wp_recommend_settings', 'wp_recommend_sanitize' );
}
add_action( 'admin_init', 'wp_recommend_register_settings' );

/**
 * Add extra text to display on each section
 */
function wp_recommend_settings_section_callback(){}

/**
 * Display the settings on the page
 */
function wp_recommends_display_setting($args){
  extract( $args );

  $option_name = 'wp_recommend_settings';
  $options = get_option( $option_name );

  switch ( $type ) {  
    case 'label_text':  
      $options[$id] = stripslashes($options[$id]);  
      $options[$id] = esc_attr( $options[$id]);  
      echo '<input class="regular-text" type="text" id="'.$id.'" name="'.$option_name.'['.$id.']" value="'.$options[$id].'" '. disabled(1, $options['likes_disable_labels'], false ) .'/>'; 
      break; 
    case 'textarea':  
      $options[$id] = stripslashes($options[$id]);  
      $options[$id] = esc_attr( $options[$id]);  
      echo '<textarea class="textarea" id="'.$id.'" name="'.$option_name.'['.$id.']">'.esc_textarea($options[$id]).'</textarea>'; 
      echo '<style>textarea#'.$id.'{width:25em;min-height:200px;font-family: Consolas,Monaco,monospace;} @media screen and (max-width: 782px) { textarea#'.$id.'{width:100%;} }</style>';
      break; 
    case 'checkbox':
    	$options[$id] = stripslashes($options[$id]);  
      $options[$id] = esc_attr( $options[$id]); 
      echo '<label><input type="checkbox" id="'.$id.'" name="'.$option_name.'['.$id.']" value="1" ' . checked(1, $options[$id], false ) . ' /> '.$desc.'</label>';
    	break; 
    case 'radio_button':
    	$options[$id] = stripslashes($options[$id]);  
      $options[$id] = esc_attr( $options[$id]); 
    	foreach($values as $key => $value) {
      	echo '<label><input type="radio" id="'.$id.'" name="'.$option_name.'['.$id.']" value="'.$key.'" ' . checked($key, $options[$id], false ) . ' /> '.$value.'</label><br>';
      }
    	break; 
  }
}

function wp_recommend_sanitize($input){
	$new_input = array();

  if( isset( $input['icon_type'] ) )
    $new_input['icon_type'] = sanitize_text_field( $input['icon_type'] );

  if( !empty( $input['likes_disable_labels'] ) ){
    $new_input['likes_disable_labels'] = absint( $input['likes_disable_labels'] );
  } else {
    $new_input['likes_disable_labels'] = 0;
  }

  if( !empty( $input['likes_singular_label'] ) ){
    $new_input['likes_singular_label'] = sanitize_text_field( $input['likes_singular_label'] );
  } else { 
    $new_input['likes_singular_label'] = '';
  }

  if( !empty( $input['likes_plural_label'] ) ){
    $new_input['likes_plural_label'] = sanitize_text_field( $input['likes_plural_label'] );
  } else { 
    $new_input['likes_plural_label'] = '';
  }

  if( !empty( $input['remove_css'] ) ){
    $new_input['remove_css'] = absint( $input['remove_css'] );
  } else { 
    $new_input['remove_css'] = 0;
  }

  if( isset( $input['custom_styles'] ) )
    $new_input['custom_styles'] = sanitize_textarea_field( $input['custom_styles'] );

  return $new_input;
}

/**
 * Add like count as column in admin list view
 */
function wp_recommend_admin_columns( $columns ) {
  $columns['like_count'] = '<svg fill="#3c434a" width="14px" height="14px" class="wp-recommend-likes-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" viewBox="0 0 492.7 492.7" xml:space="preserve"><path d="M492.7 166c0-73.5-59.6-133.1-133.1-133.1 -48 0-89.9 25.5-113.3 63.6 -23.4-38.1-65.3-63.6-113.3-63.6C59.6 33 0 92.5 0 166c0 40 17.7 75.8 45.7 100.2l188.5 188.6c3.2 3.2 7.6 5 12.1 5 4.6 0 8.9-1.8 12.1-5l188.5-188.6C475 241.8 492.7 206 492.7 166z"/></svg>';
  return $columns;
}
add_filter( 'manage_post_posts_columns', 'wp_recommend_admin_columns' );
add_filter( 'manage_page_posts_columns', 'wp_recommend_admin_columns' );
add_filter( 'manage_work_posts_columns', 'wp_recommend_admin_columns' );

/**
 * Add content to the like count column
 */
function wp_recommend_admin_column_data( $column, $post_id ) {
  switch ( $column ) {
    case 'like_count':
    	$current_like_count = wp_recommend_get_like_count($post_id);
    	echo $current_like_count;
      break;
  }
}
add_action( 'manage_post_posts_custom_column', 'wp_recommend_admin_column_data', 10, 2 );
add_action( 'manage_page_posts_custom_column', 'wp_recommend_admin_column_data', 10, 2 );
add_action( 'manage_work_posts_custom_column', 'wp_recommend_admin_column_data', 10, 2 );

/**
 * Add style block for like count column
 */
function wp_recommend_admin_column_width() {
  echo '<style type="text/css">';
  echo '.column-like_count { text-align:center !important; width:60px !important; overflow:hidden }';
  echo '</style>';
}
add_action('admin_head', 'wp_recommend_admin_column_width');

/**
 * Add settings link to plugin in installed/activated list
 */
function wp_recommend_settings_link( $links ) {
  // Build the URL
  $url = esc_url( add_query_arg(
    'page',
    'wp-recommend',
    get_admin_url() . 'options-general.php'
  ) );
  // Create the link HTML
  $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
  // Adds the link to the beginning of the array
  array_unshift(
    $links,
    $settings_link
  );
  return $links;
}
add_filter( 'plugin_action_links_wp-recommend/wp-recommend.php', 'wp_recommend_settings_link' );
