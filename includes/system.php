<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Load the framework instance.
 *
 * @since 0.1.0
 * @return Ev_Framework
 */
function ev_fw() {
	return Ev_Framework::instance();
}

/**
 * Return an array of defined field types.
 *
 * @since 0.1.0
 * @return array
 */
function ev_field_types() {
	return apply_filters( 'ev_field_types', array() );
}

/**
 * Return an array containing the field types that must be ignored when saving a
 * meta box or a page.
 *
 * @since 0.1.0
 * @return array
 */
function ev_skip_on_saving_field_types() {
	return apply_filters( 'ev_skip_on_saving_field_types', array() );
}

/**
 * Check if a particular field type should be skipped upon saving a meta box or
 * a page.
 *
 * @since 0.1.0
 * @param string $type The field type.
 * @return boolean
 */
function ev_is_skipped_on_saving( $type ) {
	$skip_field_types = ev_skip_on_saving_field_types();

	return in_array( $type, $skip_field_types );
}

/**
 * Return an array containing the groups admin pages are grouped into.
 * Group data returned by this function is structured as follows:
 * $groups['options'] = array(
 * 		'label' => 'Group label',
 * 		'pages' => array(
 * 			array(
 * 				'handle' => '',
 * 				'title' => '',
 * 				'url' => ''
 * 			)
 * 		)
 * );
 *
 * @since 0.1.0
 * @return array
 */
function ev_admin_pages_groups() {
	return apply_filters( 'ev_admin_pages_groups', array() );
}

/**
 * Determines whether or not the current user has the ability to save meta data
 * associated with this user.
 *
 * @since 0.2.0
 * @param int $user_id The ID of the user being saved.
 * @param string $action The submitted nonce action.
 * @param string $nonce The submitted nonce key.
 * @return boolean Whether or not the user has the ability to save this user information.
 */
function ev_user_can_save_user_meta( $user_id, $action = '', $nonce = 'ev' ) {
	/* Verify the validity of the supplied nonce. */
	$is_valid_nonce = ev_is_post_nonce_valid( $action, $nonce );

	/* Check the user has the capability to edit the user's information. */
	$is_valid_cap = current_user_can( 'edit_user', $user_id );

	/* Return true if the user is able to save; otherwise, false. */
    return $is_valid_nonce && $is_valid_cap;
}

/**
 * Determines whether or not the current user has the ability to save meta data
 * associated with this post.
 * Thanks to Tom McFarlin: https://gist.github.com/tommcfarlin/4468321
 *
 * @since 0.1.0
 * @param int $post_id The ID of the post being saved.
 * @param string $action The submitted nonce action.
 * @param string $nonce The submitted nonce key.
 * @return boolean Whether or not the user has the ability to save this post.
 */
function ev_user_can_save( $post_id, $action = '', $nonce = 'ev' ) {
	/* Verify the validity of the supplied nonce. */
	$is_valid_nonce = ev_is_post_nonce_valid( $action, $nonce );

	/* Preventing to do anything when autosaving, editing a revision or performing an AJAX request. */
	$is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
	$is_ajax     = defined( 'DOING_AJAX' ) && DOING_AJAX;

	/* Check the user has the capability to edit posts. */
	$is_valid_cap 	= current_user_can( get_post_type_object( get_post_type( $post_id ) )->cap->edit_post, $post_id );

	/* Return true if the user is able to save; otherwise, false. */
    return ! ( $is_autosave || $is_revision || $is_ajax ) && $is_valid_nonce && $is_valid_cap;
}

/**
 * Check if the current user can handle the display and saving on backend of
 * a set of data or a specific option/meta.
 *
 * @since 0.1.0
 * @param array $obj A field array structure or a group structure representing a collection of fields.
 * @return boolean
 */
function ev_user_can_handle_data( $obj ) {
	if ( isset( $obj['capability'] ) ) {
		return current_user_can( $obj['capability'] );
	}

	return true;
}

/**
 * Hooks a function on to a specific set of actions. The actions names are
 * obtained by combining a base tag and a set of variants.
 * Since the base tag name is processed by a "sprintf" function, it can contain
 * a "%s" marker, which is replaced by each variant in order to generate a
 * specific action tag name.
 *
 * @since 0.1.0
 * @see http://codex.wordpress.org/Function_Reference/add_action
 * @param string $tag The name of the action to which $function_to_add is hooked.
 * @param array $variants An array of variants for the action base tag.
 * @param string|array $function_to_add The name of the function you wish to be hooked.
 * @param integer $priority Used to specify the order in which the functions associated with a particular action are executed.
 * @param integer $accepted_args The number of arguments the hooked function accepts.
 */
function ev_add_actions( $tag, $variants, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	$variants = (array) $variants;

	foreach ( $variants as $variant ) {
		add_action( sprintf( $tag, $variant ), $function_to_add, $priority, $accepted_args );
	}
}

/**
 * Hooks a function on to a specific set of filters. The filters names are
 * obtained by combining a base tag and a set of variants.
 * Since the base tag name is processed by a "sprintf" function, it can contain
 * a "%s" marker, which is replaced by each variant in order to generate a
 * specific filter tag name.
 *
 * @since 0.1.0
 * @see http://codex.wordpress.org/Function_Reference/add_filter
 * @param string $tag The name of the filter to which $function_to_add is hooked.
 * @param array $variants An array of variants for the filter base tag.
 * @param string|array $function_to_add The name of the function you wish to be hooked.
 * @param integer $priority Used to specify the order in which the functions associated with a particular filter are executed.
 * @param integer $accepted_args The number of arguments the hooked function accepts.
 */
function ev_add_filters( $tag, $variants, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	$variants = (array) $variants;

	foreach ( $variants as $variant ) {
		add_filter( sprintf( $tag, $variant ), $function_to_add, $priority, $accepted_args );
	}
}

/**
 * Retrieve a configuration value.
 *
 * @since 0.1.0
 * @param string $key The configuration key.
 * @param string $subkey The configuration subkey.
 * @return mixed|bool
 */
function ev_config( $key, $subkey = false ) {
	$config = ev_fw()->config();

	if ( isset( $config[$key] ) ) {
		$config[$key] = apply_filters( "ev_config[key:$key]", $config[$key] );

		if ( $subkey !== false ) {
			if ( isset( $config[$key][$subkey] ) ) {
				return apply_filters( "ev_config[key:$key][subkey:$subkey]", $config[$key][$subkey] );
			}
			else {
				return false;
			}
		}

		return $config[$key];
	}

	return false;
}

/**
 * Print a JSON encoded structure composed by a message and a type (error, warning, etc.)
 * typically used in AJAX callbacks.
 *
 * @since 0.1.0
 * @param string $message The message text.
 * @param string $type The message type name.
 * @param string $heading The message heading.
 * @param array $args The message additional arguments.
 */
function ev_ajax_message( $message = '', $type = 'notice', $heading = '', $args = array() ) {
	$message = wptexturize( $message );
	$heading = wptexturize( $heading );
	$refresh = isset( $args['refresh'] ) && $args['refresh'] === true ? true : false;
	$types = array( 'notice', 'warning', 'success', 'error' );

	if ( ! in_array( $type, $types ) ) {
		$type = 'notice';
	}

	if ( $type != 'success' ) {
		$message = wpautop( $message );
	}

	$message_arr = array(
		'heading' => $heading,
		'message' => $message,
		'type'    => $type,
		'refresh' => $refresh
	);

	echo json_encode( $message_arr );
}

/**
 * Get the name of the key containing the options.
 *
 * @since 0.3.0
 * @return string
 */
function ev_get_options_key() {
	$key = 'ev_';

	if ( is_child_theme() ) {
		$theme = wp_get_theme();
		$key .= $theme->Template;
	}
	else {
		$key .= get_option( 'stylesheet' );
	}

	// if ( is_multisite() ) {
	// 	/* Makes sure the plugin is defined before trying to use it. */
	// 	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	// 	    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	// 	}

	// 	if ( ! is_plugin_active_for_network( EV_FRAMEWORK_MAIN_FILE_PATH ) ) {
	// 		$key .= '_' . get_current_blog_id();
	// 	}
	// }

	return apply_filters( 'ev_options_key', $key );
}

/**
 * Get a value of an option.
 *
 * @since 0.1.0
 * @param string $key The option key.
 * @return mixed
 */
function ev_get_option( $key ) {
	$options_key = ev_get_options_key();
	$options = get_option( $options_key );
	$value = is_array( $options ) && isset( $options[$key] ) ? $options[$key] : false;
	$value = apply_filters( "ev_get_option[key:{$key}]", $value );

	return $value;
}

/**
 * Delete a value of an option.
 *
 * @since 0.1.0
 * @param string $key The option key.
 */
function ev_delete_option( $key ) {
	$options_key = ev_get_options_key();
	$options = get_option( $options_key );

	if ( is_array( $options ) && isset( $options[$key] ) ) {
		unset( $options[$key] );
		update_option( $options_key, $options );
	}
}

/**
 * Update a value of an option.
 *
 * @since 0.1.0
 * @param string $key The option key.
 */
function ev_update_option( $key, $value ) {
	$options_key = ev_get_options_key();
	$options = get_option( $options_key );

	if ( ! $options ) {
		$options = array();
	}

	$options[$key] = $value;
	update_option( $options_key, $options );
}

/**
 * Get a post meta value. Return boolean false if the key wasn't found.
 *
 * @since 1.0.0
 * @param integer $post_id The post ID.
 * @param string $key The post meta key.
 * @return mixed
 */
function ev_get_post_meta( $post_id, $key ) {
	$custom_fields = get_post_custom( $post_id );

	if ( is_array( $custom_fields ) ) {
		if ( array_key_exists( $key, $custom_fields ) ) {
			return get_post_meta( $post_id, $key, true );
		}
	}

	return false;
}

/**
 * Remove a field with a particular handle value from the fields list.
 *
 * @since 0.1.0
 * @param array &$fields An array of fields.
 * @param string $handle A string representing the handle of the field to be removed.
 */
function ev_fields_remove( &$fields, $handle ) {
	foreach ( $fields as $index => $field ) {
		if ( isset( $field['handle'] ) && $field['handle'] !== $handle ) {
			continue;
		}
		else {
			unset( $fields[$index] );
			return;
		}
	}
}

/**
 * Insert a new field after another field with a particular handle value.
 *
 * @since 0.1.0
 * @param array $field_to_insert The field to insert.
 * @param array &$fields An array of fields.
 * @param string $handle A string representing the handle of the field to be removed.
 */
function ev_fields_insert_after( $field_to_insert, &$fields, $handle ) {
	foreach ( $fields as $index => $field ) {
		if ( isset( $field['handle'] ) && $field['handle'] !== $handle ) {
			continue;
		}
		else {
			$list = new Ev_List( $fields );
			$list->insert_at( $field_to_insert, $index + 1 );

			$fields = $list->get_all();

			return;
		}
	}

	$fields[] = $field_to_insert;
}

/**
 * Insert a new field before another field with a particular handle value.
 *
 * @since 0.1.0
 * @param array $field_to_insert The field to insert.
 * @param array &$fields An array of fields.
 * @param string $handle A string representing the handle of the field to be removed.
 */
function ev_fields_insert_before( $field_to_insert, &$fields, $handle ) {
	foreach ( $fields as $index => $field ) {
		if ( isset( $field['handle'] ) && $field['handle'] !== $handle ) {
			continue;
		}
		else {
			$list = new Ev_List( $fields );
			$list->insert_at( $field_to_insert, $index );
			$fields = $list->get_all();

			return;
		}
	}

	$fields[] = $field_to_insert;
}

/**
 * Check if we're currently in the login/registration screen.
 *
 * @since 0.3.0
 * @return boolean
 */
function ev_is_login_page() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
}

/**
 * Batch-export options and theme mods. Triggers file download.
 *
 * @since 0.3.0
 * @param array $export An array that determines what to export.
 */
function ev_backup_configuration( $export = array() ) {
	$export_options = true;
	$export_mods = true;

	if ( ! empty( $export ) ) {
		$export_options = isset( $export['options'] ) && $export['options'] == true;
		$export_mods = isset( $export['mods'] ) && $export['mods'] == true;
	}

	$data = array(
		'ev' => true
	);
	$filename = 'ev-export';

	if ( $export_options ) {
		$options_key = ev_get_options_key();
		$data['options'] = get_option( $options_key );
		$filename .= '-options';
	}

	if ( $export_mods ) {
		$data['mods'] = get_theme_mods();
		$filename .= '-mods';
	}

	$filename .= '.' . date( 'Y-m-d' ) . '.txt';

	header( 'Content-disposition: attachment; filename=' . $filename );
	header( 'Content-type: text/plain' );

	ob_start();
	echo serialize( $data );
	ob_end_flush();

	die();
}

/**
 * Import options and skin into the system.
 *
 * @since 0.3.0
 * @param array $data The serialized data.
 */
function ev_restore_configuration( $data = array() ) {
	$data = maybe_unserialize( $data );

	if ( ! is_array( $data ) ) {
		return;
	}

	$import_options = isset( $data['options'] );
	$import_mods    = isset( $data['mods'] );

	if ( ! isset( $data['ev'] ) || ! $data['ev'] ) {
		return;
	}

	if ( $import_options ) {
		$options_key = ev_get_options_key();
		update_option( $options_key, $data['options'] );
	}

	if ( $import_mods ) {
		$theme = get_option( 'stylesheet' );
		update_option( "theme_mods_$theme", $data['mods'] );
	}
}

/**
 * Check if a provided nonce is valid for requests performed via POST.
 *
 * @since 0.4.0
 * @param string $action The nonce action.
 * @param string $key The nonce key.
 * @return boolean
 */
function ev_is_post_nonce_valid( $action, $key = 'nonce' ) {
	$is_valid_nonce = isset( $_POST[$key] ) && wp_verify_nonce( $_POST[$key], $action );

	return $is_valid_nonce;
}

/**
 * Get the context of the current screen on frontend.
 *
 * @since 1.0.0
 * @return array
 */
function ev_get_context() {
	return ev_fw()->frontend()->context();
}

/**
 * Get the template of a page.
 *
 * @since 1.0.0
 * @param integer $post_id The page ID.
 * @return string
 */
function ev_get_page_template( $post_id ) {
	$page_template = '';

	if ( ! get_post_type( $post_id ) === 'page' ) {
		return false;
	}

	if ( $post_id ) {
		$page_template = get_post_meta( $post_id, '_wp_page_template', true );
	}

	if ( empty( $page_template ) ) {
		$page_template = 'default';
	}

	return $page_template;
}

/**
 * Perform action hooks according to the context and optionally external data.
 *
 * @since 1.0.7
 * @param string $key The hook key.
 * @param array $data The external data.
 */
function ev_do_action( $key, $data = array() ) {
	do_action( $key );

	/* Optionally filter the passed data. */
	$data = apply_filters( "ev_do_action_data", $data );
	$data = apply_filters( "${key}_data", $data );

	$context = ev_get_context();

	foreach ( $context as $k => $v ) {
		do_action( $key . "[context:$v]" );
	}

	foreach ( $data as $k => $v ) {
		do_action( $key . "[$k:$v]" );
	}
}