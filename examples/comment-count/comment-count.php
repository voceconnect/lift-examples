<?php
/**
 * The following is an example of how to use the liftDelegateField
 * to add a custom field to Lift.  The below example uses the post's
 * comment count to create a new field and add it to the search form.
 * 
 */

define( 'lift_comment_facet_version', 1 );
add_action( 'init', function() {
		if ( class_exists( 'Lift_Search' ) ) {
			//field name, field type
			$comment_field = liftDelegatedField( 'comment_count', 'uint' )
				//add field to parse_request handling so it gets passed to the global WP_Query
				->addPublicRequestVars( 'comment_count' )
				//set the delegate for adding the field value to the document sent to CS
				->delegate( 'getDocumentValue', function($post_id) {
						return intval( get_comments_number( $post_id ) );
					} )
				//set the delegate that converts request vars to WP_Query query_vars
				->delegate( 'requestToWP', function($request) {
						if ( isset( $request['comment_count'] ) ) {
							if ( false === strpos( $request['comment_count'], '..' ) ) {
								$request['min_comments'] = $request['max_comments'] = abs( intval( $request['comment_count'] ) );
							} else {
								$count_parts = explode( '..', $request['comment_count'] );

								if ( $count_parts[0] )
									$request['min_comments'] = $count_parts[0];

								if ( $count_parts[1] )
									$request['max_comments'] = $count_parts[1];
							}
							unset( $request['comment_count'] );
						}
						return $request;
					} )
				//set the delegate that converts WP_Query query_vars to a boolean query
				->delegate( 'wpToBooleanQuery', function($query_vars) {
						$min_comments = !empty( $query_vars['min_comments'] ) ? intval( $query_vars['min_comments'] ) : '';
						$max_comments = !empty( $query_vars['max_comments'] ) ? intval( $query_vars['max_comments'] ) : '';
						if ( $min_comments || $max_comments ) {
							if ( $min_comments == $max_comments ) {
								return "comment_count:$min_comments";
							} else {
								return "comment_count:$min_comments..$max_comments";
							}
						}
						return null;
					} )
				->delegate( 'wpToLabel', function($query_vars) {
					$min = isset( $query_vars['min_comments'] ) ? intval( $query_vars['min_comments'] ) : '';
					$max = isset( $query_vars['max_comments'] ) ? intval( $query_vars['max_comments'] ) : '';
					if ( $min && $max ) {
						return sprintf( '%d to %d', $min, $max );
					} elseif ( $min ) {
						return sprintf( "More than %d", $min );
					} elseif ( $max ) {
						return sprintf( "Less than %d", $max );
					} else {
						return "Any";
					}
				} );
				
			$facets = array(
				array('max_comments' => 5),
				array('min_comments' => 6, 'max_comments' => 10),
				array('min_comments' => 11, 'max_comments' => 20),
				array('min_comments' => 21)
			);

			new LiftSingleSelectFilter($comment_field, 'Comment Count', $facets);

			//enqueue comment changes to mark documents as changed
			add_action( 'wp_update_comment_count', function($post_id) {
					Lift_Document_Update_Queue::queue_field_update( $post_id, 'comment_count' );
				} );

			//check if the version of this plugin changed and update the schema if needed.
			$installed_version = get_option( 'lift_comment_facet_version', 0 );
			if ( $installed_version < 1 ) {
				Lift_Search::update_schema();
			}
		}
	}, 9 );
