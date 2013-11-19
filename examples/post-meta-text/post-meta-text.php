<?php

/**
 * The below example illustrates how to use the LiftPostMetaTextField
 * to add a field to lift based off of a text based post meta.
 */

define( 'lift_test_meta_text_version', 4 );
add_action( 'init', function() {
		if ( class_exists( 'Lift_Search' ) ) {
			require_once __DIR__ . '/TestLiftTextFilter.php';

			$field = new LiftPostMetaTextField( 'lift_meta_text', array( 'meta_key' => 'lift_meta_text' ) );
			new TestLiftTextFilter( $field, 'Test Text Contains', 'lift_meta_text' );

			//check if the version of this plugin changed and update the schema if needed.
			$installed_version = get_option( 'lift_test_meta_text_version', 0 );
			if ( $installed_version < 1 ) {
				Lift_Search::update_schema();
				update_option( 'lift_test_meta_text_version', lift_test_meta_text_version );
			}
		}
	}, 9 );
