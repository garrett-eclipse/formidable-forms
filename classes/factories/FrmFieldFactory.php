<?php

/**
 * @since 2.03.05
 */
class FrmFieldFactory {

	/**
	 * Create an instance of an FrmFieldValueSelector object
	 *
	 * @since 2.03.05
	 *
	 * @param int $field_id
	 * @param array $args
	 *
	 * @return FrmFieldValueSelector
	 */
	public static function create_field_value_selector( $field_id, $args ) {
		$selector = null;

		if ( $field_id > 0 ) {
			$selector = apply_filters( 'frm_create_field_value_selector', $selector, $field_id, $args );
		}

		if ( ! is_object( $selector ) ) {
			$selector = new FrmFieldValueSelector( $field_id, $args );
		}

		return $selector;
	}

	/**
	 * @since 3.0
	 */
	public static function get_field_type( $field_type ) {
		$class = self::get_field_type_class( $field_type );
		if ( empty( $class ) ) {
			$field = new FrmFieldText( $field_type );
		} else {
			$field = new $class();
		}

		return $field;
	}

	/**
	 * @since 3.0
	 */
	private static function get_field_type_class( $field_type ) {
		$type_classes = array(
			'text'     => 'FrmFieldText',
			'textarea' => 'FrmFieldTextarea',
			'select'   => 'FrmFieldSelect',
			'radio'    => 'FrmFieldRadio',
			'checkbox' => 'FrmFieldCheckbox',
			'number'   => 'FrmFieldNumber',
			'phone'    => 'FrmFieldPhone',
			'url'      => 'FrmFieldUrl',
			'website'  => 'FrmFieldUrl',
			'email'    => 'FrmFieldEmail',
			'user_id'  => 'FrmFieldUserID',
			'html'     => 'FrmFieldHTML',
			'hidden'   => 'FrmFieldHidden',
			'captcha'  => 'FrmFieldCaptcha',
		);

		$class = isset( $type_classes[ $field_type ] ) ? $type_classes[ $field_type ] : '';
		$class = apply_filters( 'frm_get_field_type_class', $class, $field_type );

		return $class;
	}

	/**
	 * @since 3.0
	 */
	public static function field_has_html( $type ) {
		$field = self::get_field_type( $type );
		$has_html = $field->has_html;

		// this hook is here for reverse compatibility since 3.0
		$has_html = apply_filters( 'frm_show_custom_html', $has_html, $type );

		return $has_html;
	}
}