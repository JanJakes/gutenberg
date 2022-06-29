<?php

/**
 * Test WP_Theme_JSON_Gutenberg class.
 *
 * @package Gutenberg
 */

class WP_Theme_JSON_Gutenberg_Test extends WP_UnitTestCase {

	function test_get_stylesheet_handles_whitelisted_element_pseudo_selectors() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'link' => array(
							'color'  => array(
								'text'       => 'green',
								'background' => 'red',
							),
							':hover' => array(
								'color'      => array(
									'text'       => 'red',
									'background' => 'green',
								),
								'typography' => array(
									'textTransform' => 'uppercase',
									'fontSize'      => '10em',
								),
							),
							':focus' => array(
								'color' => array(
									'text'       => 'yellow',
									'background' => 'black',
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = 'a{background-color: red;color: green;}a:hover{background-color: green;color: red;font-size: 10em;text-transform: uppercase;}a:focus{background-color: black;color: yellow;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_get_stylesheet_handles_only_psuedo_selector_rules_for_given_property() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'link' => array(
							':hover' => array(
								'color'      => array(
									'text'       => 'red',
									'background' => 'green',
								),
								'typography' => array(
									'textTransform' => 'uppercase',
									'fontSize'      => '10em',
								),
							),
							':focus' => array(
								'color' => array(
									'text'       => 'yellow',
									'background' => 'black',
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = 'a:hover{background-color: green;color: red;font-size: 10em;text-transform: uppercase;}a:focus{background-color: black;color: yellow;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_get_stylesheet_ignores_pseudo_selectors_on_non_whitelisted_elements() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'h4' => array(
							'color'  => array(
								'text'       => 'green',
								'background' => 'red',
							),
							':hover' => array(
								'color' => array(
									'text'       => 'red',
									'background' => 'green',
								),
							),
							':focus' => array(
								'color' => array(
									'text'       => 'yellow',
									'background' => 'black',
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = 'h4{background-color: red;color: green;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_get_stylesheet_ignores_non_whitelisted_pseudo_selectors() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'link' => array(
							'color'     => array(
								'text'       => 'green',
								'background' => 'red',
							),
							':hover'    => array(
								'color' => array(
									'text'       => 'red',
									'background' => 'green',
								),
							),
							':levitate' => array(
								'color' => array(
									'text'       => 'yellow',
									'background' => 'black',
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = 'a{background-color: red;color: green;}a:hover{background-color: green;color: red;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
		$this->assertStringNotContainsString( 'a:levitate{', $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_get_stylesheet_handles_priority_of_elements_vs_block_elements_pseudo_selectors() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'blocks' => array(
						'core/group' => array(
							'elements' => array(
								'link' => array(
									'color'  => array(
										'text'       => 'green',
										'background' => 'red',
									),
									':hover' => array(
										'color'      => array(
											'text'       => 'red',
											'background' => 'green',
										),
										'typography' => array(
											'textTransform' => 'uppercase',
											'fontSize' => '10em',
										),
									),
									':focus' => array(
										'color' => array(
											'text'       => 'yellow',
											'background' => 'black',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = '.wp-block-group a{background-color: red;color: green;}.wp-block-group a:hover{background-color: green;color: red;font-size: 10em;text-transform: uppercase;}.wp-block-group a:focus{background-color: black;color: yellow;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_get_stylesheet_handles_whitelisted_block_level_element_pseudo_selectors() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'link' => array(
							'color'  => array(
								'text'       => 'green',
								'background' => 'red',
							),
							':hover' => array(
								'color' => array(
									'text'       => 'red',
									'background' => 'green',
								),
							),
						),
					),
					'blocks'   => array(
						'core/group' => array(
							'elements' => array(
								'link' => array(
									':hover' => array(
										'color' => array(
											'text'       => 'yellow',
											'background' => 'black',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$base_styles = 'body { margin: 0; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';

		$element_styles = 'a{background-color: red;color: green;}a:hover{background-color: green;color: red;}.wp-block-group a:hover{background-color: black;color: yellow;}';

		$expected = $base_styles . $element_styles;

		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
		$this->assertEquals( $expected, $theme_json->get_stylesheet( array( 'styles' ) ) );
	}

	function test_remove_invalid_element_pseudo_selectors() {
		$actual = WP_Theme_JSON_Gutenberg::remove_insecure_properties(
			array(
				'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
				'styles'  => array(
					'elements' => array(
						'link' => array(
							'color'  => array(
								'text'       => 'hotpink',
								'background' => 'yellow',
							),
							':hover' => array(
								'color' => array(
									'text'       => 'red',
									'background' => 'blue',
								),
							),
						),
					),
				),
			),
			true
		);

		$expected = array(
			'version' => WP_Theme_JSON_Gutenberg::LATEST_SCHEMA,
			'styles'  => array(
				'elements' => array(
					'link' => array(
						'color'  => array(
							'text'       => 'hotpink',
							'background' => 'yellow',
						),
						':hover' => array(
							'color' => array(
								'text'       => 'red',
								'background' => 'blue',
							),
						),
					),
				),
			),
		);

		$this->assertEqualSetsWithIndex( $expected, $actual );
	}

	function test_get_element_class_name_button() {
		$expected = 'wp-element-button';
		$actual   = WP_Theme_JSON_Gutenberg::get_element_class_name( 'button' );

		$this->assertEquals( $expected, $actual );
	}

	function test_get_element_class_name_invalid() {
		$expected = '';
		$actual   = WP_Theme_JSON_Gutenberg::get_element_class_name( 'unknown-element' );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Testing that dynamic properties in theme.json return the value they refrence, e.g.
	 * array( 'ref' => 'styles.color.background' ) => "#ffffff".
	 */
	function test_get_property_value_valid() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => 2,
				'styles'  => array(
					'color'    => array(
						'background' => '#ffffff',
						'text'       => '#000000',
					),
					'elements' => array(
						'button' => array(
							'color' => array(
								'background' => array( 'ref' => 'styles.color.text' ),
								'text'       => array( 'ref' => 'styles.color.background' ),
							),
						),
					),
				),
			)
		);

		$expected = 'body { margin: 0; }body{background-color: #ffffff;color: #000000;}.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }.wp-element-button, .wp-block-button__link{background-color: #000000;color: #ffffff;}';
		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
	}

	/**
	 * Testing that dynamic properties in theme.json that
	 * refer to other dynamic properties in a loop
	 * then they should be left untouched.
	 *
	 * @expectedIncorrectUsage get_property_value
	 */
	function test_get_property_value_loop() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => 2,
				'styles'  => array(
					'color'    => array(
						'background' => '#ffffff',
						'text'       => array( 'ref' => 'styles.elements.button.color.background' ),
					),
					'elements' => array(
						'button' => array(
							'color' => array(
								'background' => array( 'ref' => 'styles.color.text' ),
								'text'       => array( 'ref' => 'styles.color.background' ),
							),
						),
					),
				),
			)
		);

		$expected = 'body { margin: 0; }body{background-color: #ffffff;}.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }.wp-element-button, .wp-block-button__link{color: #ffffff;}';
		$this->assertSame( $expected, $theme_json->get_stylesheet() );
	}

	/**
	 * Testing that dynamic properties in theme.json that
	 * refer to other dynamic properties then they should be left unprocessed.
	 *
	 * @expectedIncorrectUsage get_property_value
	 */
	function test_get_property_value_recursion() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => 2,
				'styles'  => array(
					'color'    => array(
						'background' => '#ffffff',
						'text'       => array( 'ref' => 'styles.color.background' ),
					),
					'elements' => array(
						'button' => array(
							'color' => array(
								'background' => array( 'ref' => 'styles.color.text' ),
								'text'       => array( 'ref' => 'styles.color.background' ),
							),
						),
					),
				),
			)
		);

		$expected = 'body { margin: 0; }body{background-color: #ffffff;color: #ffffff;}.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }.wp-element-button, .wp-block-button__link{color: #ffffff;}';
		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
	}

	/**
	 * Testing that dynamic properties in theme.json that
	 * refer to themselves then they should be left unprocessed.
	 *
	 * @expectedIncorrectUsage get_property_value
	 */
	function test_get_property_value_self() {
		$theme_json = new WP_Theme_JSON_Gutenberg(
			array(
				'version' => 2,
				'styles'  => array(
					'color' => array(
						'background' => '#ffffff',
						'text'       => array( 'ref' => 'styles.color.text' ),
					),
				),
			)
		);

		$expected = 'body { margin: 0; }body{background-color: #ffffff;}.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }';
		$this->assertEquals( $expected, $theme_json->get_stylesheet() );
	}
}
