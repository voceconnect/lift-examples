<?php

/**
 * The below example illustrates how to use the LiftTaxonomyField
 * to add a taxonomy to lift based off of a non-core taxonomy.
 */
define( 'lift_test_taxonomy_version', 3 );
add_action( 'init', function() {
		if ( class_exists( 'Lift_Search' ) ) {

			$labels = array(
				'name' => _x( 'Genres', 'Taxonomy General Name', 'lift_examples' ),
				'singular_name' => _x( 'Genre', 'Taxonomy Singular Name', 'lift_examples' ),
				'menu_name' => __( 'Genres', 'lift_examples' ),
				'all_items' => __( 'All Genres', 'lift_examples' ),
				'parent_item' => __( 'Parent Genre', 'lift_examples' ),
				'parent_item_colon' => __( 'Parent Genre:', 'lift_examples' ),
				'new_item_name' => __( 'New Genre', 'lift_examples' ),
				'add_new_item' => __( 'Add New Genre', 'lift_examples' ),
				'edit_item' => __( 'Edit Genre', 'lift_examples' ),
				'update_item' => __( 'Update Genre', 'lift_examples' ),
				'separate_items_with_commas' => __( 'Separate genres with commas', 'lift_examples' ),
				'search_items' => __( 'Search Genres', 'lift_examples' ),
				'add_or_remove_items' => __( 'Add or remove genres', 'lift_examples' ),
				'choose_from_most_used' => __( 'Choose from the most used genres', 'lift_examples' ),
				'not_found' => __( 'Not Found', 'lift_examples' ),
			);
			$args = array(
				'labels' => $labels,
				'hierarchical' => true,
				'public' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud' => true,
			);
			register_taxonomy( 'genre', 'post', $args );

			$field = new LiftTaxonomyField( 'genre' );

			new LiftIntersectFilter( $field, 'In Genre' );

			//check if the version of this plugin changed and update the schema if needed.
			$installed_version = get_option( 'lift_test_taxonomy_version', 0 );
			if ( $installed_version < lift_test_taxonomy_version ) {
				Lift_Search::update_schema();
				update_option( 'lift_test_taxonomy_version', lift_test_taxonomy_version );
			}
		}
	}, 9 );
