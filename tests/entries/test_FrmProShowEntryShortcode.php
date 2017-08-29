<?php

/**
 * @since 2.04
 *
 * @group shortcodes
 * @group entries
 * @group show-entry-shortcode
 * @group pro
 * TODO: DRY
 *
 */
include( 'test_FrmShowEntryShortcode.php' );
class test_FrmProShowEntryShortcode extends test_FrmShowEntryShortcode {

	// TODO: try including a field from inside a repeating section. It's not yet possible to display a single field from inside a repeating section
	// TODO: try including a field from inside an embedded form
	// TODO: section with no fields in it
	// TODO: add is_visible for for sections and maybe page breaks in default HTML or just if [if x]
	// TODO: what about conditional page breaks?
	// TODO: figure out if this was important: $filter_value = ( ! isset( $atts['filter'] ) || $atts['filter'] !== false );
	// TODO: ***add test for value of 0 and include_blank=false***, using jamie_entry_key_2
	// TODO: add test for section with no values in it, plain text
	// TODO: unit test for frm_email_value hook

	private $text_field_id = '';

	public function setUp() {
		parent::setUp();

		$this->text_field_id = FrmField::get_id_by_key( 'text-field' );
	}

	/**
	 * Tests [default-message]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 *
	 * @group show-entry-for-post-entry-email
	 */
	public function test_basic_default_message_parameters_create_post_form() {
		$entry = FrmEntry::getOne( 'post-entry-1', true );

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_html_content_for_post_entry( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message include_fields="x,y"]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 */
	public function test_default_message_with_repeating_field_id_included() {
		$this->markTestSkipped( 'Functionality not added yet.' );
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$this->include_fields = array(
			'text-field' => FrmField::get_id_by_key( 'text-field' ),
			'repeating-text' => FrmField::get_id_by_key( 'repeating-text' ),
			'user-id-field' => FrmField::get_id_by_key( 'user-id-field' ),
		);

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'include_fields' => implode( ',', $this->include_fields ),
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_content_for_include_fields( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message exclude_fields="x,y"]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04.02
	 */
	public function test_default_message_with_repeating_field_id_excluded() {
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$this->exclude_fields = array(
			'text-field' => FrmField::get_id_by_key( 'text-field' ),
			'repeating-text' => FrmField::get_id_by_key( 'repeating-text' ),
			'user-id-field' => FrmField::get_id_by_key( 'user-id-field' ),
		);

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'exclude_fields' => implode( ',', $this->exclude_fields ),
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_content_for_exclude_fields( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message include_extras="section"]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 *
	 * @group show-entry-shortcode-conditional-section
	 */
	public function test_default_message_with_conditionally_hidden_sections() {
		$this->hide_and_clear_section();
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'include_extras' => 'section',
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_html_content( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message include_extras="section"]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 *
	 * @group show-entry-shortcode-conditional-section
	 */
	public function test_default_message_with_conditionally_hidden_sections_and_include_fields() {
		$this->hide_and_clear_section();
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$this->include_fields = array(
			'text-field' => FrmField::get_id_by_key( 'text-field' ),
			'repeating-section' => FrmField::get_id_by_key( 'repeating-section' ),
			'embed-form-field' => FrmField::get_id_by_key( 'embed-form-field' ),
			'user-id-field' => FrmField::get_id_by_key( 'user-id-field' ),
			'pro-fields-divider' => FrmField::get_id_by_key( 'pro-fields-divider' ),
		);

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'include_extras' => 'section',
			'include_fields' => implode( ',', $this->include_fields ),
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_content_for_include_fields( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message plain_text=1 include_extras="section"]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 3.0
	 *
	 * @group show-entry-shortcode-conditional-section
	 */
	public function test_plain_text_content_with_conditionally_hidden_sections() {
		$this->hide_and_clear_section();
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => true,
			'user_info' => false,
			'include_extras' => 'section'
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_plain_text_content( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Hide section and clear values in it
	 */
	private function hide_and_clear_section() {
		$entry_id = FrmEntry::get_id_by_key( 'jamie_entry_key' );

		// Update conditional logic field
		FrmEntryMeta::update_entry_meta( $entry_id, $this->text_field_id, null, 'Hide Fields' );

		// Clear all conditionally hidden fields
		$rich_text_field_id = FrmField::get_id_by_key( 'rich-text-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $rich_text_field_id );

		$single_file_field_id = FrmField::get_id_by_key( 'single-file-upload-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $single_file_field_id );

		$multi_file_field_id = FrmField::get_id_by_key( 'multi-file-upload-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $multi_file_field_id );

		$number_field_id = FrmField::get_id_by_key( 'number-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $number_field_id );

		$phone_number_field_id = FrmField::get_id_by_key( 'n0d580' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $phone_number_field_id );

		$time_field_id = FrmField::get_id_by_key( 'time-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $time_field_id );

		$date_field_id = FrmField::get_id_by_key( 'date-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $date_field_id );

		$image_url_field_id = FrmField::get_id_by_key( 'zwuclz' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $image_url_field_id );

		$scale_field_id = FrmField::get_id_by_key( 'qbrd2o' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $scale_field_id );

		$tags_field_id = FrmField::get_id_by_key( 'tags-field' );
		FrmEntryMeta::delete_entry_meta( $entry_id, $tags_field_id );
	}

	/**
	 * Tests [default-message include_extras="section"]
	 * This tests the situation where an entry was submitted and a repeating section was left blank
	 * The repeating section may have child entries, but those entries have no values
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 */
	public function test_default_message_with_no_values_in_repeating_section() {
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );
		$repeating_section = FrmField::get_id_by_key( 'repeating-section' );

		foreach ( $entry->metas[ $repeating_section ] as $child_id ) {
			// Delete all meta with an item_id of $entry->id
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'frm_item_metas', array( 'item_id' => $child_id ) );
		}

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'include_extras' => 'section',
		);

		$content = $this->get_formatted_content( $atts );

		$atts['is_repeat_empty'] = true;
		$expected_content = $this->expected_html_content( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests [default-message include_extras="section" include_blank="1"]
	 * This tests the situation where an entry was submitted and a repeating section was left blank
	 * The repeating section may have child entries, but those entries have no values
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 */
	public function test_default_message_with_no_values_in_repeating_section_include_blank() {
		$this->markTestSkipped( 'Make this pass for second beta' );
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );
		$repeating_section = FrmField::get_id_by_key( 'repeating-section' );

		foreach ( $entry->metas[ $repeating_section ] as $child_id ) {
			// Delete all meta with an item_id of $entry->id
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'frm_item_metas', array( 'item_id' => $child_id ) );
		}

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'include_extras' => 'section',
			'include_blank' => true,
		);

		$content = $this->get_formatted_content( $atts );

		$atts['is_repeat_empty'] = true;
		$expected_content = $this->expected_html_content( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests array for API
	 * This tests the situation where an entry was submitted and a repeating section was left blank
	 * The repeating section may have child entries, but those entries have no values
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 */
	public function test_array_with_no_values_in_repeating_section() {
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$repeating_section = FrmField::get_id_by_key( 'repeating-section' );

		foreach ( $entry->metas[ $repeating_section ] as $child_id ) {
			// Delete all meta with an item_id of $entry->id
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'frm_item_metas', array( 'item_id' => $child_id ) );
		}

		$atts = array(
			'id' => $entry->id,
			'format' => 'array',
			'user_info' => false,
			'include_blank' => true,
		);

		$data_array = FrmEntriesController::show_entry_shortcode( $atts );

		$atts['is_repeat_empty'] = true;
		$expected_array = $this->expected_array( $entry, $atts );

		$this->assertSame( $expected_array, $data_array );
	}

	/**
	 * Tests [default-message clickable=1]
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 */
	public function test_default_message_with_clickable() {
		$entry = FrmEntry::getOne( 'jamie_entry_key', true );

		$atts = array(
			'id' => $entry->id,
			'entry' => $entry,
			'plain_text' => false,
			'user_info' => false,
			'clickable' => 1,
		);

		$content = $this->get_formatted_content( $atts );
		$expected_content = $this->expected_html_content( $atts );

		$this->assertSame( $expected_content, $content );
	}

	/**
	 * Tests the way an API action gets entry data
	 *
	 * @covers FrmEntriesController::show_entry_shortcode
	 *
	 * @since 2.04
	 *
	 * @group show-entry-array-format
	 */
	public function test_array_format_for_api_post_entry() {
		$entry = FrmEntry::getOne( 'post-entry-1', true );

		$atts = array(
			'id' => $entry->id,
			'user_info' => false,
			'format' => 'array',
			'include_blank' => true,
		);

		$data_array = FrmEntriesController::show_entry_shortcode( $atts );
		$expected_array = $this->expected_post_array( $entry, $atts );

		$this->assertSame( $expected_array, $data_array );
	}

	protected function expected_plain_text_content( $atts ) {
		$content = $this->text_field_plain_text( $atts );
		$content .= $this->paragraph_to_website_plain_text();
		$content .= $this->page_break_plain_text( $atts );
		$content .= $this->pro_fields_divider_plain_text( $atts );
		$content .= $this->dynamic_field_plain_text();
		$content .= $this->embedded_form_plain_text( $atts );
		$content .= $this->user_id_plain_text();
		$content .= $this->html_field_plain_text( $atts );
		$content .= $this->tags_plain_text( $atts );
		$content .= $this->signature_plain_text();
		$content .= $this->repeating_section_header_plain_text( $atts );
		$content .= $this->repeating_field_plain_text();
		$content .= $this->separate_values_checkbox_plain_text();
		$content .= $this->user_info_plain_text( $atts );

		return $content;
	}

	protected function expected_html_content( $atts ) {
		$table = $this->table_header( $atts );

		$table .= $this->text_field_html( $atts );
		$table .= $this->paragraph_to_website_html( $atts );
		$table .= $this->page_break_html( $atts );
		$table .= $this->pro_fields_divider_html( $atts );
		$table .= $this->dynamic_field_html();
		$table .= $this->embedded_form_html( $atts );
		$table .= $this->user_id_html();
		$table .= $this->html_field_html( $atts );
		$table .= $this->tags_html( $atts );
		$table .= $this->signature_html();
		$table .= $this->repeating_section_header( $atts );
		$table .= $this->repeating_field_html( $atts );
		$table .= $this->separate_values_checkbox_html();
		$table .= $this->user_info_html( $atts );

		$table .= $this->table_footer();

		return $table;
	}

	protected function expected_content_for_include_fields( $atts ) {
		$table = $this->table_header( $atts );

		if ( isset( $this->include_fields['text-field'] ) ) {
			$table .= $this->text_field_html( $atts );
		}

		if ( isset( $this->include_fields['embed-form-field'] ) ) {
			$table .= $this->embedded_form_html( $atts );
		}

		if ( isset( $this->include_fields['user-id-field'] ) ) {
			$table .= $this->user_id_html();
		}

		if ( isset( $this->include_fields['repeating-section'] ) ) {
			$table .= $this->repeating_section_header( $atts );
			$table .= $this->repeating_field_html( $atts );
		}

		if ( isset( $this->include_fields['repeating-text'] ) ) {
			$table .= $this->repeating_text_field_html();
		}
		$table .= $this->user_info_html( $atts );
		$table .= $this->table_footer();

		return $table;
	}

	protected function expected_content_for_exclude_fields( $atts ) {

		$table = $this->table_header( $atts );

		if ( ! isset( $exclude_fields['text-field'] ) ) {
			$table .= $this->text_field_html( $atts );
		}

		$table .= $this->paragraph_to_website_html( $atts );
		$table .= $this->pro_fields_divider_html( $atts );
		$table .= $this->dynamic_field_html();

		if ( ! isset( $exclude_fields['embed-form-field'] ) ) {
			$table .= $this->embedded_form_html( $atts );
		}

		if ( ! isset( $exclude_fields['user-id-field'] ) ) {
			$table .= $this->user_id_html();
		}

		$table .= $this->tags_html( $atts );
		$table .= $this->signature_html();

		if ( ! isset( $exclude_fields['repeating-section'] ) ) {
			$table .= $this->repeating_section_header( $atts );
			$table .= $this->repeating_field_html( $atts, $exclude_fields );
		}

		$table .= $this->separate_values_checkbox_html();
		$table .= $this->user_info_html( $atts );
		$table .= $this->table_footer();

		return $table;
	}

	private function text_field_html( $atts ) {
		//TODO: parent::two_cell_field_row( );

		$html = '<tr' . $this->tr_style . '>';

		if ( isset( $atts['direction'] ) && $atts['direction'] == 'rtl' ) {
			$html .= '<td' . $this->td_style . '>' . $atts['entry']->metas[ $this->text_field_id ] . '</td>';
			$html .= '<td' . $this->td_style . '>Single Line Text</td>';
		} else {
			$html .= '<td' . $this->td_style . '>Single Line Text</td>';
			$html .= '<td' . $this->td_style . '>' . $atts['entry']->metas[ $this->text_field_id ] . '</td>';
		}

		$html .= '</tr>' . "\r\n";

		return $html;
	}

	private function paragraph_to_website_html( $atts ) {
		$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Paragraph Text</td><td' . $this->td_style . '>';
		$html .= "Jamie\nRebecca\nWahlin</td></tr>\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Checkboxes - colors</td><td' . $this->td_style . '>Red, Green</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Radio Buttons - dessert</td><td' . $this->td_style . '>cookies</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Dropdown</td><td' . $this->td_style . '>Ace Ventura</td></tr>' . "\r\n";

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Email Address</td><td' . $this->td_style . '>';
			$html .= '<a href="mailto:jamie@mail.com">jamie@mail.com</a></td></tr>' . "\r\n";

			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Website/URL</td><td' . $this->td_style . '>';
			$html .= '<a href="http://www.jamie.com" rel="nofollow">http://www.jamie.com</a></td></tr>' . "\r\n";
		} else {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Email Address</td><td' . $this->td_style . '>jamie@mail.com</td></tr>' . "\r\n";
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Website/URL</td><td' . $this->td_style . '>http://www.jamie.com</td></tr>' . "\r\n";
		}

		return $html;
	}

	private function page_break_html( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'page' ) !== false ) {
			$html = '<tr' . $this->tr_style . '><td colspan="2"' . $this->td_style . '><br/><br/></td></tr>' . "\r\n";

		} else {
			$html = '';
		}

		return $html;
	}

	private function pro_fields_divider_html( $atts ) {
		if ( $atts['entry']->metas[ $this->text_field_id ] == 'Hide Fields' ) {
			return '';
		}

		$html = $this->pro_fields_divider_heading( $atts );
		$html .= $this->fields_within_pro_fields_divider( $atts );

		return $html;
	}

	private function pro_fields_divider_heading( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$html = '<tr' . $this->tr_style . '><td colspan="2"' . $this->td_style . '><h3>Pro Fields</h3></td></tr>' . "\r\n";
		} else {
			$html = '';
		}

		return $html;
	}


	private function fields_within_pro_fields_divider( $atts ) {
		$html = $this->rich_text_html();
		$html .= $this->single_file_upload_html( $atts );
		$html .= $this->multi_file_upload_html( $atts );
		$html .= $this->number_to_scale_field_html( $atts );

		return $html;
	}

	private function rich_text_html() {
		return '<tr' . $this->tr_style . '><td' . $this->td_style . '>Rich Text</td><td' . $this->td_style . '><strong>Bolded text</strong></td></tr>' . "\r\n";
	}

	private function single_file_upload_html( $atts ) {
		$file_field_id = FrmField::get_id_by_key( 'single-file-upload-field' );
		$single_file_url = wp_get_attachment_url( $atts['entry']->metas[ $file_field_id ] );

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single File Upload</td><td' . $this->td_style . '>';
			$html .= '<a href="' . $single_file_url . '" rel="nofollow">' . $single_file_url . '</a></td></tr>';
			$html .= "\r\n";
		} else {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single File Upload</td><td' . $this->td_style . '>' . $single_file_url . '</td></tr>';
			$html .= "\r\n";
		}

		return $html;
	}

	private function multi_file_upload_html( $atts ) {
		$multi_file_urls = $this->get_multi_file_urls( $atts['entry'] );

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Multiple File Upload</td><td' . $this->td_style . '>';

			foreach ( $multi_file_urls as $multi_file_url ) {
				$html .= '<a href="' . $multi_file_url . '" rel="nofollow">' . $multi_file_url . '</a><br/><br/>';
			}

			$html = preg_replace('/<br\/><br\/>$/', '', $html);
			$html .= '</td></tr>';
			$html .= "\r\n";
		} else {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Multiple File Upload</td><td' . $this->td_style . '>';

			$formatted_urls = implode( '<br/><br/>', $multi_file_urls );

			$html .= $formatted_urls . '</td></tr>';
			$html .= "\r\n";
		}

		return $html;
	}

	private function get_multi_file_urls( $entry ) {
		$file_field_id = FrmField::get_id_by_key( 'multi-file-upload-field' );

		$multi_file_urls = array();
		foreach ( $entry->metas[ $file_field_id ] as $file_id ) {
			$multi_file_urls[] = wp_get_attachment_url( $file_id );
		}

		return $multi_file_urls;
	}

	private function number_to_scale_field_html( $atts ) {
		$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Number</td><td' . $this->td_style . '>11</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Phone Number</td><td' . $this->td_style . '>1231231234</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Time</td><td' . $this->td_style . '>12:30 AM</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Date</td><td' . $this->td_style . '>August 16, 2015</td></tr>' . "\r\n";

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Image URL</td><td' . $this->td_style . '>';
			$html .= '<a href="http://www.test.com" rel="nofollow">http://www.test.com</a></td></tr>' . "\r\n";
		} else {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Image URL</td><td' . $this->td_style . '>http://www.test.com</td></tr>' . "\r\n";
		}

		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Scale</td><td' . $this->td_style . '>5</td></tr>' . "\r\n";

		return $html;
	}

	private function dynamic_field_html() {
		return '<tr' . $this->tr_style . '><td' . $this->td_style . '>Dynamic Field - level 1</td><td' . $this->td_style . '>United States</td></tr>' . "\r\n";
	}



	private function html_field_html( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'html' ) !== false ) {
			$html = '<tr' . $this->tr_style . '><td colspan="2"' . $this->td_style . '>Lorem ipsum.</td></tr>' . "\r\n";

		} else {
			$html = '';
		}

		return $html;
	}

	private function repeating_section_header( $atts ) {
		if ( isset( $atts['is_repeat_empty'] ) && $atts['is_repeat_empty'] && ! isset( $atts['include_blank'] ) ) {
			return '';
		}

		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$html = '<tr' . $this->tr_style . '><td colspan="2"' . $this->td_style . '><h3>Repeating Section</h3></td></tr>' . "\r\n";
		} else {
			$html = '';
		}

		return $html;
	}

	private function user_id_html() {
		return '<tr' . $this->tr_style . '><td' . $this->td_style . '>User ID</td><td' . $this->td_style . '>admin</td></tr>' . "\r\n";
	}

	private function tags_html( $atts ) {
		if ( $atts['entry']->metas[ $this->text_field_id ] == 'Hide Fields' ) {
			$html = '';
		} else {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Tags</td><td' . $this->td_style . '>Jame</td></tr>' . "\r\n";

		}

		return $html;
	}

	private function signature_html() {
		return '<tr' . $this->tr_style . '><td' . $this->td_style . '>Signature</td><td' . $this->td_style . '>398, 150</td></tr>' . "\r\n";
	}

	private function embedded_form_html( $atts ) {
		$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Name</td><td' . $this->td_style . '>Embedded name</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Last</td><td' . $this->td_style . '>test</td></tr>' . "\r\n";

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Email</td><td' . $this->td_style . '>';
			$html .= '<a href="mailto:test@mail.com">test@mail.com</a></td></tr>' . "\r\n";

		} else {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Email</td><td' . $this->td_style . '>test@mail.com</td></tr>' . "\r\n";
		}

		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$html .= '<tr' . $this->tr_style . '><td colspan="2"' . $this->td_style . '><h3>Email Information</h3></td></tr>' . "\r\n";
		}

		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Subject</td><td' . $this->td_style . '>test</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Message</td><td' . $this->td_style . '>test</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Date</td><td' . $this->td_style . '>May 21, 2015</td></tr>' . "\r\n";

		return $html;
	}

	private function repeating_field_html( $atts, $exclude_fields = array() ) {
		if ( isset( $atts['is_repeat_empty'] ) && $atts['is_repeat_empty'] && ! isset( $atts['include_blank'] ) ) {
			return '';
		}

		$html = '';

		if ( ! isset( $exclude_fields['repeating-text'] ) ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>First</td></tr>' . "\r\n";
		}
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Checkboxes</td><td' . $this->td_style . '>Option 1, Option 2</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Date</td><td' . $this->td_style . '>May 27, 2015</td></tr>' . "\r\n";

		if ( ! isset( $exclude_fields['repeating-text'] ) ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>Second</td></tr>' . "\r\n";
		}
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Checkboxes</td><td' . $this->td_style . '>Option 1, Option 2</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Date</td><td' . $this->td_style . '>May 29, 2015</td></tr>' . "\r\n";

		if ( ! isset( $exclude_fields['repeating-text'] ) ) {
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>Third</td></tr>' . "\r\n";
		}
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Checkboxes</td><td' . $this->td_style . '>Option 2</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Date</td><td' . $this->td_style . '>June 19, 2015</td></tr>' . "\r\n";

		return $html;
	}

	private function repeating_text_field_html() {
		$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>First</td></tr>' . "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>Second</td></tr>'. "\r\n";
		$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Single Line Text</td><td' . $this->td_style . '>Third</td></tr>' . "\r\n";

		return $html;
	}

	private function separate_values_checkbox_html() {
		return '<tr' . $this->tr_style . '><td' . $this->td_style . '>Checkboxes - separate values</td><td' . $this->td_style . '>Option 1, Option 2</td></tr>' . "\r\n";
	}

	private function user_info_html( $atts ) {
		if ( isset( $atts['user_info'] ) && $atts['user_info'] == true ) {
			$html = '<tr' . $this->tr_style . '><td' . $this->td_style . '>IP Address</td><td' . $this->td_style . '>127.0.0.1</td></tr>' . "\r\n";
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>User-Agent (Browser/OS)</td><td' . $this->td_style . '>Mozilla Firefox 37.0 / OS X</td></tr>' . "\r\n";
			$html .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Referrer</td><td' . $this->td_style . '>http://localhost:8888/features/wp-admin/admin-ajax.php?action=frm_forms_preview&form=boymfd</td></tr>' . "\r\n";
		} else {
			$html = '';
		}

		return $html;
	}

	private function text_field_plain_text( $atts ) {
		if ( isset( $atts['direction'] ) && $atts['direction'] == 'rtl' ) {
			$content = $atts['entry']->metas[ $this->text_field_id ] . ': Single Line Text';
		} else {
			$content = 'Single Line Text: ' . $atts['entry']->metas[ $this->text_field_id ];
		}

		$content .= "\r\n";

		return $content;
	}

	private function paragraph_to_website_plain_text() {
		$content = "Paragraph Text: Jamie\nRebecca\nWahlin\r\n";
		$content .= "Checkboxes - colors: Red, Green\r\n";
		$content .= "Radio Buttons - dessert: cookies\r\n";
		$content .= "Dropdown: Ace Ventura\r\n";
		$content .= "Email Address: jamie@mail.com\r\n";
		$content .= "Website/URL: http://www.jamie.com\r\n";

		return $content;
	}

	private function pro_fields_divider_plain_text( $atts ) {
		if ( $atts['entry']->metas[ $this->text_field_id ] == 'Hide Fields' ) {
			return '';
		}

		$content = '';
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$content .= "\r\nPro Fields\r\n";
		}

		$content .= "Rich Text: Bolded text\r\n";

		$file_field_id = FrmField::get_id_by_key( 'single-file-upload-field' );
		$single_file_url = wp_get_attachment_url( $atts['entry']->metas[ $file_field_id ] );
		$content .= "Single File Upload: " . $single_file_url . "\r\n";

		$multiple_file_urls = $this->get_multi_file_urls( $atts['entry'] );
		$content .= "Multiple File Upload: " . implode( ', ', $multiple_file_urls ) . "\r\n";

		$content .= "Number: 11\r\n";
		$content .= "Phone Number: 1231231234\r\n";
		$content .= "Time: 12:30 AM\r\n";
		$content .= "Date: August 16, 2015\r\n";
		$content .= "Image URL: http://www.test.com\r\n";
		$content .= "Scale: 5\r\n";

		return $content;
	}


	private function page_break_plain_text( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'page' ) !== false ) {
			$html = "\r\n\r\n";

		} else {
			$html = '';
		}

		return $html;
	}

	private function html_field_plain_text( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'html' ) !== false ) {
			$html = "Lorem ipsum.\r\n";
		} else {
			$html = '';
		}

		return $html;
	}

	private function dynamic_field_plain_text() {
		return "Dynamic Field - level 1: United States\r\n";
	}

	private function embedded_form_plain_text( $atts ) {
		$content = "Name: Embedded name\r\n";
		$content .= "Last: test\r\n";

		if ( isset( $atts['clickable'] ) && $atts['clickable'] ) {
			$content .= "Email: test@mail.com</a>\r\n";

		} else {
			$content .= "Email: test@mail.com\r\n";
		}

		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$content .= "\r\nEmail Information\r\n";
		}

		$content .= "Subject: test\r\n";
		$content .= "Message: test\r\n";
		$content .= "Date: May 21, 2015\r\n";

		return $content;
	}

	private function repeating_section_header_plain_text( $atts ) {
		if ( isset( $atts['include_extras'] ) && strpos( $atts['include_extras'], 'section' ) !== false ) {
			$content = "\r\nRepeating Section\r\n";
		} else {
			$content = '';
		}

		return $content;
	}

	private function user_id_plain_text() {
		return "User ID: admin\r\n";
	}

	private function tags_plain_text( $atts ) {
		if ( $atts['entry']->metas[ $this->text_field_id ] == 'Hide Fields' ) {
			$content = '';
		} else {
			$content = "Tags: Jame\r\n";

		}

		return $content;
	}

	private function signature_plain_text() {
		return "Signature: 398, 150\r\n";
	}

	private function repeating_field_plain_text() {
		$content = "Single Line Text: First\r\n";
		$content .= "Checkboxes: Option 1, Option 2\r\n";
		$content .= "Date: May 27, 2015\r\n";
		$content .= "Single Line Text: Second\r\n";
		$content .= "Checkboxes: Option 1, Option 2\r\n";
		$content .= "Date: May 29, 2015\r\n";
		$content .= "Single Line Text: Third\r\n";
		$content .= "Checkboxes: Option 2\r\n";
		$content .= "Date: June 19, 2015\r\n";

		return $content;
	}

	private function separate_values_checkbox_plain_text() {
		$content = "Checkboxes - separate values: Option 1, Option 2\r\n";

		return $content;
	}

	private function user_info_plain_text( $atts ) {
		if ( isset( $atts['user_info'] ) && $atts['user_info'] == true ) {
			$content = "IP Address: 127.0.0.1\r\n";
			$content .= "User-Agent (Browser/OS): Mozilla Firefox 37.0 / OS X\r\n";
			$content .= "Referrer: http://localhost:8888/features/wp-admin/admin-ajax.php?action=frm_forms_preview&form=boymfd\r\n";
		} else {
			$content = '';
		}

		return $content;
	}

	private function expected_html_content_for_post_entry( $atts ) {
		$table = $this->table_header( $atts );
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Title</td><td' . $this->td_style . '>Jamie\'s Post</td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Content</td><td' . $this->td_style . '>Hello! My name is Jamie.</td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Author</td><td' . $this->td_style . '>Jamie Wahlin</td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>User ID</td><td' . $this->td_style . '>admin</td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Category</td><td' . $this->td_style . '><a href="http://example.org/?cat=1" title="View all posts filed under Uncategorized">Uncategorized</a></td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Parent Dynamic Field</td><td' . $this->td_style . '><a href="http://example.org/?cat=1" title="View all posts filed under Uncategorized">Uncategorized</a></td></tr>' . "\r\n";
		$table .= '<tr' . $this->tr_style . '><td' . $this->td_style . '>Post Status</td><td' . $this->td_style . '>Published</td></tr>' . "\r\n";
		$table .= $this->table_footer();

		return $table;
	}

	protected function get_expected_default_html( $atts ) {
		$fields = FrmField::get_all_for_form( $atts['form_id'], '', 'include' );

		$html = $this->table_header( $atts );

		$in_repeating_section = 0;
		foreach ( $fields as $field ) {

			if ( in_array( $field->type, array( 'html', 'form', 'divider', 'break', 'end_divider', 'password', 'captcha' ) ) ) {
				if ( $field->type == 'divider' ) {

					$html .= '[if ' . $field->id . ']<tr style="[frm-alt-color]">';
					$html .= '<td colspan="2"' . $this->td_style . '>';
					$html .= '<h3>[' . $field->id . ' show=description]</h3>';
					$html .= '</td>';
					$html .= '</tr>' . "\r\n" . '[/if ' . $field->id . ']' . "\r\n";

					if ( FrmField::is_repeating_field( $field ) ) {
						$in_repeating_section = $field->id;
						$html .= '[foreach ' . $field->id . ']';
					}
				} else if ( $in_repeating_section > 0 && $field->type == 'end_divider' ) {
					$html .= '[/foreach ' . $in_repeating_section . ']';
					$in_repeating_section = 0;
				} else if ( $field->type == 'break' ) {
					$html .= '[if ' . $field->id . ']<tr style="[frm-alt-color]">';
					$html .= '<td colspan="2"' . $this->td_style . '><br/><br/></td>';
					$html .= '</tr>' . "\r\n" . '[/if ' . $field->id . ']' . "\r\n";
				}

				continue;
			}

			$html .= '[if ' . $field->id . ']<tr style="[frm-alt-color]">';
			$html .= '<td' . $this->td_style . '>[' . $field->id . ' show=field_label]</td>';

			if ( $field->type == 'data' && $field->field_options['data_type'] == 'data' ) {
				$html .= '<td' . $this->td_style . '>';
				$html .= '[' . $field->field_options['hide_field'][0] . ' show=' . $field->field_options['form_select'] . ']';
				$html .= '</td>';
			} else {
				$html .= '<td' . $this->td_style . '>[' . $field->id . ']</td>';
			}
			$html .= '</tr>' . "\r\n" . '[/if ' . $field->id . ']' . "\r\n";;
		}

		$html .= $this->table_footer();

		return $html;
	}

	protected function expected_array( $entry, $atts ) {

		// Single file upload field
		$file_field_id = FrmField::get_id_by_key( 'single-file-upload-field' );
		$single_file_url = wp_get_attachment_url( $entry->metas[ $file_field_id ] );

		// Multi file upload field
		$multi_file_field_id = FrmField::get_id_by_key( 'multi-file-upload-field' );
		$multi_file_urls = $this->get_multi_file_urls( $entry );

		// Dynamic Country
		$where = array( 'meta_value' => 'United States', 'field_id' => FrmField::get_id_by_key( '2atiqt' ) );
		$dynamic_country_id = FrmDb::get_var( 'frm_item_metas', $where, 'item_id' );

		// TODO: do I need field label?


		$expected = array(
			'text-field' => 'Jamie',
			'p3eiuk' => "Jamie\nRebecca\nWahlin",
			'uc580i' => array ( 'Red', 'Green' ),
			'radio-button-field' => 'cookies',
			'dropdown-field' => 'Ace Ventura',
			'email-field' => 'jamie@mail.com',
			'website-field' => 'http://www.jamie.com',
			'rich-text-field' => 'Bolded text',
			'rich-text-field-value' => '<strong>Bolded text</strong>',
			'single-file-upload-field' => $single_file_url,
			'single-file-upload-field-value' => $entry->metas[ $file_field_id ],
			'multi-file-upload-field' => $multi_file_urls,// TODO: check purpose of extra space in FrmProEntryMetaHelper
			'multi-file-upload-field-value' => $entry->metas[ $multi_file_field_id ],
			'number-field' => '11',
			'n0d580' => '1231231234',
			'time-field' => '12:30 AM',
			'time-field-value' => '00:30',
			'date-field' => 'August 16, 2015',
			'date-field-value' => '2015-08-16',
			'zwuclz' => 'http://www.test.com',
			'qbrd2o' => '5',
			'dynamic-country' => 'United States',
			'dynamic-country-value' => $dynamic_country_id,
			'dynamic-state' => '',
			'dynamic-city' => '',
			'qfn4lg' => '',
			'contact-name' => 'Embedded name',
			'contact-last-name' => 'test',
			'contact-email' => 'test@mail.com',
			'contact-website' => '',
			'contact-subject' => 'test',
			'contact-message' => 'test',
			'contact-date' => 'May 21, 2015',
			'contact-date-value' => '2015-05-21',
			'contact-user-id' => '',
			'hidden-field' => '',
			'user-id-field' => 'admin',
			'user-id-field-value' => '1',
			'tags-field' => 'Jame',
			'ggo4ez' => array(
				'width' => '398',
				'height' => '150',
			),
			'repeating-section' => array(
				0 => array(
					'repeating-text' => 'First',
					'repeating-checkbox' => array( 'Option 1', 'Option 2' ),
					'repeating-date' => 'May 27, 2015',
					'repeating-date-value' => '2015-05-27',
				),
				1 => array(
					'repeating-text' => 'Second',
					'repeating-checkbox' => array( 'Option 1', 'Option 2' ),
					'repeating-date' => 'May 29, 2015',
					'repeating-date-value' => '2015-05-29',
				),
				2 => array(
					'repeating-text' => 'Third',
					'repeating-checkbox' => array( 'Option 2' ),
					'repeating-date' => 'June 19, 2015',
					'repeating-date-value' => '2015-06-19',
				),
			),
			'repeating-text' => array( 'First', 'Second', 'Third' ),
			'repeating-checkbox' => array( array( 'Option 1', 'Option 2' ), array( 'Option 1', 'Option 2' ), array( 'Option 2') ),
			'repeating-date' => array( 'May 27, 2015', 'May 29, 2015', 'June 19, 2015' ),
			'repeating-date-value' => array( '2015-05-27', '2015-05-29', '2015-06-19' ),
			'lookup-country' => '',
			'cb-sep-values' => array( 'Option 1', 'Option 2' ),
			'cb-sep-values-value' => array( 'Red', 'Orange' ),
		);

		$this->remove_repeating_fields( $atts, $expected );

		if ( ! isset( $atts['include_blank'] ) || $atts['include_blank'] == false ) {
			foreach ( $expected as $field_key => $value ) {
				if ( $value == '' || empty( $value ) ) {
					unset( $expected[ $field_key ] );
				}
			}
		}

		return $expected;
	}

	private function remove_repeating_fields( $atts, &$expected ) {
		if ( isset( $atts['is_repeat_empty'] ) && $atts['is_repeat_empty'] ) {

			$child_values = array(
				'repeating-text' => '',
				'repeating-checkbox' => '',
				'repeating-date' => '',
			);

			$expected['repeating-section'] = array( $child_values, $child_values, $child_values );
			$expected['repeating-text'] = array( '', '', '' );
			$expected['repeating-checkbox'] = array( '', '', '' );
			$expected['repeating-date'] = array( '', '', '' );
			unset( $expected['repeating-date-value'] );
		}
	}

	private function expected_post_array( $entry, $atts ) {
		$expected = array(
			'yi6yvm' => 'Jamie\'s Post',
			'knzfvv' => 'Hello! My name is Jamie.',
			'8j2k9i' => '',
			'37pxx2' => 'Jamie Wahlin',
			'ml8awj' => 'admin',
			'ml8awj-value' => '1',
			'rs4jgc' => '',
			'izzcad' => 'Uncategorized',
			'izzcad-value' => array( 1 ),
			// TODO: displayed value for categories. Should categories by in array?
			'parent-dynamic-taxonomy' => 'Uncategorized',
			'parent-dynamic-taxonomy-value' => array( 1 ),
			'child-dynamic-taxonomy' => '',
			'grandchild-dynamic-taxonomy' => '',
			'post-status-dropdown' => 'Published',
			'post-status-dropdown-value' => 'publish',
		);

		return $expected;
	}

	protected function expected_default_array( $atts ) {
		$fields = FrmField::get_all_for_form( $atts['form_id'], '', 'include' );

		$expected = array();

		foreach ( $fields as $field ) {

			if ( in_array( $field->type, array( 'html', 'form', 'divider', 'break', 'end_divider', 'password', 'captcha' ) ) ) {
				if ( FrmField::is_repeating_field( $field ) ) {
					// TODO: do something different for repeating sections?
				}
				continue;
			}

			if ( $field->type == 'data' && $field->field_options['data_type'] == 'data' ) {

				$expected[ $field->id ] = array(
					'label' => '[' . $field->id . ' show=field_label]',
					'val' => '[' . $field->field_options['hide_field'][0] . ' show=' . $field->field_options['form_select'] . ']',
					'type' => $field->type,
				);

			} else {
				$expected[ $field->id ] = array(
					'label' => '[' . $field->id . ' show=field_label]',
					'val' => '[' . $field->id . ']',
					'type' => $field->type,
				);
			}

		}

		return $expected;
	}

	protected function get_test_entry( $include_meta ) {
		return FrmEntry::getOne( 'jamie_entry_key', $include_meta );
	}

	protected function get_included_fields( $type ) {
		$include_fields = array(
			'text-field' => 'text-field',
			'repeating-section' => 'repeating-section',
			'embed-form-field' => 'embed-form-field',
			'user-id-field' => 'user-id-field',
		);

		$this->convert_field_array( $type, $include_fields );

		return $include_fields;
	}

	protected function get_single_included_field( $type ) {
		$include_fields = array(
			'text-field' => 'text-field',
		);

		$this->convert_field_array( $type, $include_fields );

		return $include_fields;
	}

	protected function get_form_id_for_test() {
		return FrmForm::getIdByKey( 'all_field_types' );
	}
}