<?php

/**
 * Meta helpers should hold some grunt work for making
 * custom meta. This class should contain global work that should work
 * for any environment (that's the idea anyway).
 */
class MDG_Meta_Helper extends MDG_Meta_Form_Fields {
	/** @var string Sets the meta box title */
	public $meta_box_title;
	/** @var string Sets the meta box position */
	public $meta_box_position;
	/** @var string Sets the meta box priority */
	public $meta_box_priority;
	/** @var string Renames the featured image meta box */
	public $featured_image_title;
	/** @var array Meta box id(s) to be removed */
	public $meta_boxes_to_remove;



	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_add_actions();

		$this->meta_box_title       = ( isset( $this->meta_box_title ) ) ? $this->meta_box_title : "{$this->post_type_single} Details";
		$this->meta_box_position    = ( isset( $this->meta_box_position ) ) ? $this->meta_box_position : 'normal';
		$this->meta_box_priority    = ( isset( $this->meta_box_priority ) ) ? $this->meta_box_priority : 'high';
		$this->meta_boxes_to_remove = ( isset( $this->meta_boxes_to_remove ) ) ? $this->meta_boxes_to_remove : array();
		$this->featured_image_title = ( isset( $this->featured_image_title ) ) ? $this->featured_image_title : 'Featured Image';
	} // __construct()



	/**
	 * All of the allowed tags when outputting meta form fields.
	 *
	 * @return array Allowed HTML tags.
	 */
	private function _get_meta_output_kses_allowed_html() {
		$allowed_tags          = wp_kses_allowed_html( 'post' );
		$allowed_tags['<hr>']  = array();
		$allowed_tags['input'] = array(
			'type'        => array(),
			'name'        => array(),
			'id'          => array(),
			'value'       => array(),
			'size'        => array(),
			'class'       => array(),
			'placeholder' => array(),
			'checked'     => array(),
		);
		$allowed_tags['option'] = array(
			'value'    => array(),
			'selected' => array(),
		);
		$allowed_tags['select'] = array(
			'name'     => array(),
			'id'       => array(),
			'class'    => array(),
			'style'    => array(),
			'multiple' => array()
		);
		$allowed_tags['span'] = array(
			'class' => array(),
			'id'    => array(),
		);
		$allowed_tags['textarea'] = array(
			'name'        => array(),
			'id'          => array(),
			'cols'        => array(),
			'rows'        => array(),
			'class'       => array(),
		);
		return $allowed_tags;
	} // _get_meta_output_kses_allowed_html()



	/**
	 * All action hooks that are required by the class using add_action.
	 *
	 * @return Void
	 */
	protected function _add_actions() {
		// Save custom meta action hook
		add_action( 'save_post', array( &$this, 'save_meta' ) );

		// Make meta box action hook
		add_action( 'add_meta_boxes', array( &$this, 'make_meta_box' ) );

		// Remove metaboxes action hook
		add_action( 'admin_menu' , array( &$this, 'remove_metaboxes' ) );

		// Renames the featured image meta box
		add_action( 'do_meta_boxes', array( &$this, 'rename_featured_image_meta_box' ) );
	} // _add_actions()



	/**
	 * Removes unwanted meta boxes
	 *
	 * @return Void
	 */
	public function remove_metaboxes() {
		// Remove from all post types
		$post_types = get_post_types();
		foreach ( $post_types as $post_type )
			remove_meta_box( 'postcustom', $post_type, 'normal' );

		// Remove from specific post type
		foreach ( $this->meta_boxes_to_remove as $meta_box ) {
			extract( $meta_box );
			if ( $id != '' and $context != '' and $page != '' )
				remove_meta_box( $id, $page, $context );
		} // foreach();
	} // remove_metaboxes()



	/**
	 * Renames the featured image meta box.
	 *
	 * @return Void
	 */
	public function rename_featured_image_meta_box() {
		$post_type_supports_thumbnail = post_type_supports( get_post_type(), 'thumbnail' );
		if ( ! $this->featured_image_title or $this->featured_image_title == '' or ! $post_type_supports_thumbnail ) {
			return;
		} // if()
		remove_meta_box( 'postimagediv', $this->post_type, 'side' );
		add_meta_box( 'postimagediv', __( $this->featured_image_title ), 'post_thumbnail_meta_box', $this->post_type, 'side', 'low' );
	} // rename_featured_image_meta_box



	/**
	 * Will cycle through your fields array, and create your form
	 *
	 * Your fields array should look something like the example provided
	 * and you can pass this array via $args (e.g. $helper->mdg_make_form(array('meta_fields' => $fields_array);
	 * array(
	 *  array(
	 *   'label' => 'Field one',
	 *   'desc' => 'helper text,
	 *   'id' => 'fieldOneID',
	 *   'type' => 'text'
	 *  ),
	 *  array(
	 *   'label' => 'Field Two',
	 *   'desc' => 'helper text,
	 *   'id' => 'fieldTwoID',
	 *   'type' => 'text'
	 *  )
	 * );
	 *
	 * @param array   $args [description]
	 *
	 * @return [type]       [description]
	 */
	public function mdg_make_form( $args = array() ) {
		global $post;
		$meta_fields  = isset( $args['meta_fields'] ) ? $args['meta_fields'] : '';
		$allowed_tags = $this->_get_meta_output_kses_allowed_html();

		// Output description information
		foreach ( $meta_fields as $field ){
			if ( $field['type'] == 'info' ) {
				echo wp_kses( $field['desc'], $allowed_tags );
			} // if()
		} // foreach()

			// Use nonce for verification
			echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce( basename( __FILE__ ) ).'" />';

		// Begin the field table and loop
		echo '<table class="form-table">';

		foreach ( $meta_fields as $field ) {
			extract( $field );

			// get value of this field if it exists for this post
			$meta = get_post_meta( $post->ID, esc_attr( $id ), true );
			// begin a table row with
			echo '<tr>
			<th><label for="'.esc_attr( $id ).'">'.esc_attr( $label ).'</label></th>
				<td>';

			switch ( $type ) {
				case 'divider':
					echo wp_kses( '<hr>', $allowed_tags );
					break;
				case 'markup':
					echo wp_kses( $desc, $allowed_tags );
					break;
				case 'text':
					$text_field = $this->text_field( $id, $meta, $desc );
					echo wp_kses( $text_field, $allowed_tags );
					break;
				case 'file':
					$file_upload = $this->file_upload_field( $id, $meta, $desc );
					echo wp_kses( $file_upload, $allowed_tags );
					break;
				case 'textarea':
					$textarea = $this->textarea( $id, $meta, $desc );
					echo wp_kses( $textarea, $allowed_tags );
					break;
				case 'checkbox':
					$checkbox = $this->checkbox( $id, $meta, $desc );
					echo wp_kses( $checkbox, $allowed_tags );
					break;
				case 'radio':
					$radio = $this->radio( $id, $meta, $desc, $options );
					echo wp_kses( $radio, $allowed_tags );
					break;
				case 'select':
					$select = $this->select( $id, $meta, $desc, $options );
					echo wp_kses( $select, $allowed_tags );
					break;
				case 'chosen_select':
					$chosen_select = $this->chosen_select( $id, $meta, $desc, $options );
					echo wp_kses( $chosen_select, $allowed_tags );
					break;
				case 'chosen_select_multi':
					$chosen_select_multi = $this->chosen_select_multi( $id, $meta, $desc, $options );
					echo wp_kses( $chosen_select_multi, $allowed_tags );
					break;
				case 'date':
					$datepicker = $this->datepicker( $id, $meta, $id );
					echo wp_kses( $datepicker, $allowed_tags );
					break;
				case 'line':
					echo wp_kses( '</td></tr></table><hr/><table class="form-table">', $allowed_tags );
					break;
				case 'title':
					echo wp_kses( '<div class="form-group-title">'.esc_attr( $label ).'</div>', $allowed_tags );
					break;
				case 'wysiwg_editor':
					$meta = get_post_meta( $post->ID, $id, true );
					$wysiwg_editor = $this->wysiwg_editor( $id, $meta, $desc );
					echo wp_kses( $wysiwg_editor, $allowed_tags );
					break;

				case 'multi_input':
					$this->multi_input_field(
						array(
							'multi_fields' => $multi_fields,
							'id'           => $id,
							'desc'         => $desc,
							'meta'         => $meta,
						)
					);
					break;
				case 'color_picker':
					$color_picker = $this->color_picker( $id, $meta, $desc );
					echo $color_picker;
					break;
			} // switch()
			echo '</td></tr>';
		} // foreach()
		echo '</table>'; // end table
	} // mdg_make_form()



	/**
	 * Handles sanitizing the post meta value dependent of the field type.
	 *
	 * @param   string  $field_type  The field id/type.
	 * @param   mixed   $value       The meta value to be sanitized.
	 *
	 * @return  mixed               The sanitized meta data.
	 */
	public function sanitize_post_meta( $field_type, $value ) {
		switch ( $field_type ) {
			case 'text':
				$value = sanitize_text_field( $value );
				break;

			case 'file':
				$value = esc_url_raw( $value, $protocols );
				break;

			case 'url':
				$value = esc_url_raw( $value, $protocols );
				break;

			case 'email':
				$value = sanitize_email( $value );
				break;

			case 'textarea':
				$value = wp_kses( $value, 'post' );
				break;

			case 'wysiwg_editor':
				$value = wp_kses( $value, 'post' );
				break;

			case 'multi_input':
				$value = wp_kses( $value, 'post' );
				break;

			default:
				$value = esc_attr( $value );
				break;
		} // switch()

		return $value;
	} // sanitize_post_meta()



	/*
	 * Saves your custom meta when the post is saved
	 * You should pass it the post_id and the meta fields in an array as an argument
	 * something like...
	 *	$meta_helper->save_custom_meta(array(
	 *		'post_id'				=> 12,
	 *		'custom_meta_fields'	=> $meta_fields_array
	 *	));
	 *
	 */
	public function save_custom_meta( $args = array() ) {
		$post_id    = isset( $args['post_id'] ) ? $args['post_id'] : '';
		$custom_meta_fields = isset( $args['custom_meta_fields'] ) ? $args['custom_meta_fields'] : '';

		// verify nonce
		$mb_nonce = isset( $_POST['custom_meta_box_nonce'] ) ? $_POST['custom_meta_box_nonce'] : '';
		if ( ! wp_verify_nonce( $mb_nonce, basename( __FILE__ ) ) )
			return $post_id;

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// loop through fields and save the data
		foreach ( $custom_meta_fields as $field ) {
			extract( $field );

			$old = get_post_meta( $post_id, esc_attr( $id ), true );
			$new = isset( $_POST[esc_attr( $id )] ) ? $_POST[esc_attr( $id )] : '';

			$new = $this->sanitize_post_meta( $id, $new );

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, esc_attr( $id ), $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, esc_attr( $id ), $old );
			} // if/elseif()
		} // end foreach
	} // save_custom_meta()



	public function get_custom_meta( $args ) {

		// this method is for use on the front end
		// it will iterated through the fields in the backend ->
		// then match those to fields that have content ->
		// then return an array of the custom meta ->

		// We need to look at all fields first to get the titles from them

		// initialize args
		$post_id   = isset( $args['post_id'] )   ? $args['post_id'] : '';
		$meta_fields  = isset( $args['meta_fields'] )  ? $args['meta_fields'] : '';

		// get possible available custom meta (see inc/custom-meta.php)
		$custom_meta_fields = $meta_fields;

		// get actual saved custom meta
		$custom_meta_data  = get_post_custom( $post_id );

		// create array of custom meta based on what's
		// available and whats been entered
		$actual_meta = array();

		// iterate through the available meta, adding data to our array
		// if it exists as saved meta
		foreach ( $custom_meta_fields as $meta_field ) {
			if ( array_key_exists( $meta_field['id'], $custom_meta_data ) ) {
				$value = isset( $custom_meta_data[ $meta_field['id'] ][0] ) ? $custom_meta_data[ $meta_field['id'] ][0] : '';
				$visible = isset( $meta_field['visible'] ) ? $meta_field['visible'] : true;
				$type    = isset( $meta_field['type'] ) ? $meta_field['type'] : '';
				$item = array(
					'id'      => $meta_field['id'],
					'title'   => $meta_field['label'],
					'value'   => $value,
					'visible' => $visible,
					'type'    => $type,
				);

				array_push( $actual_meta, $item );

			} // end if

		} // end foreach

		return $actual_meta;
	} // get_custom_meta()



	/**
	 * Override this method to create custom meta fields.
	 *
	 * By returning an empty array in this method, we're telling the class to not to
	 * do anything will custom meta (e.g. meta boxes, and saving meta etc...)
	 * The overridden method should return an array like..
	 * return array(
	 *  array(
	 *   'label' => 'Title/Position',
	 *   'desc'  => '',
	 *   'id'    => $prefix.'Title',
	 *   'type'  => 'text'
	 *  ),
	 *  array(
	 *   'label' => 'Quote',
	 *   'desc'  => '',
	 *   'id'    => $prefix.'Quote',
	 *   'type'  => 'textarea'
	 *  )
	 * );
	 *
	 * @return array Custom meta fields
	 */
	public function get_custom_meta_fields() {
		return array();
	} // get_custom_meta_fields()



	/**
	 * Handles registering and generating the custom meta box
	 *
	 * @return Void
	 */
	public function make_meta_box() {
		$custom_meta_fields = $this->get_custom_meta_fields();
		if ( empty( $custom_meta_fields ) )
			return;

		add_meta_box(
			"{$this->post_type}_id_meta_box", // $id
			$this->meta_box_title,            // $title
			array( $this, 'show_meta_box' ),  // $callback
			$this->post_type,                 // $page
			$this->meta_box_position,         // $context
			$this->meta_box_priority          // $priority
		);
	} // make_meta_box()



	/**
	 * Handles outputting of the metabox form
	 *
	 * @return Void
	 */
	public function show_meta_box() {
		$custom_meta_fields = $this->get_custom_meta_fields();
		if ( empty( $custom_meta_fields ) )
			return;

		global $post;
		$this->mdg_make_form( array( 'meta_fields' => $custom_meta_fields ) );
	} // show_meta_box()



	/**
	 * Handles the saving of custom meta
	 *
	 * @param integer $post_id ID of the post that you wish to save custom meta for
	 *
	 * @return Void
	 */
	public function save_meta( $post_id ) {
		$custom_meta_fields = $this->get_custom_meta_fields();
		if ( empty( $custom_meta_fields ) )
			return;

		$this->save_custom_meta(
			array(
				'post_id'            => $post_id,
				'custom_meta_fields' => $custom_meta_fields,
			)
		);
	} // save_meta()
} // END Class MDG_Meta_Helper
