<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

class Strings
{
	public function post_type_labels()
	{
		return [
			'add_new_item'          => __( 'Add New Review', 'site-reviews' ),
			'all_items'             => __( 'All Reviews', 'site-reviews' ),
			'archives'              => __( 'Review Archives', 'site-reviews' ),
			'edit_item'             => __( 'Edit Review', 'site-reviews' ),
			'insert_into_item'      => __( 'Insert into review', 'site-reviews' ),
			'new_item'              => __( 'New Review', 'site-reviews' ),
			'not_found'             => __( 'No Reviews found', 'site-reviews' ),
			'not_found_in_trash'    => __( 'No Reviews found in Trash', 'site-reviews' ),
			'search_items'          => __( 'Search Reviews', 'site-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this review', 'site-reviews' ),
			'view_item'             => __( 'View Review', 'site-reviews' ),
		];
	}

	public function post_updated_messages()
	{
		return [
			'approved'      => __( 'Review has been approved and published.', 'site-reviews' ),
			'draft_updated' => __( 'Review draft updated.', 'site-reviews' ),
			'preview'       => __( 'Preview review', 'site-reviews' ),
			'published'     => __( 'Review approved and published.', 'site-reviews' ),
			'restored'      => __( 'Review restored to revision from %s.', 'site-reviews' ),
			'reverted'      => __( 'Review has been reverted to its original submission state.', 'site-reviews' ),
			'saved'         => __( 'Review saved.', 'site-reviews' ),
			'scheduled'     => __( 'Review scheduled for: %s.', 'site-reviews' ),
			'submitted'     => __( 'Review submitted.', 'site-reviews' ),
			'unapproved'    => __( 'Review has been unapproved and is now pending.', 'site-reviews' ),
			'updated'       => __( 'Review updated.', 'site-reviews' ),
			'view'          => __( 'View review', 'site-reviews' ),
		];
	}

	/**
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function review_types()
	{
		$types = [
			'local' => __( 'Local Reviews', 'site-reviews' ),
		];

		return apply_filters( 'site-reviews/addon/types', $types );
	}

	public function validation()
	{
		return [
			'accepted'        => _x( 'The :attribute must be accepted.', ':attribute is a placeholder and should not be translated.', 'site-reviews' ),
			'between.numeric' => _x( 'The :attribute must be between :min and :max.', ':attribute, :min, and :max are placeholders and should not be translated.', 'site-reviews' ),
			'between.string'  => _x( 'The :attribute must be between :min and :max characters.', ':attribute, :min, and :max are placeholders and should not be translated.', 'site-reviews' ),
			'email'           => _x( 'The :attribute must be a valid email address.', ':attribute is a placeholder and should not be translated.', 'site-reviews' ),
			'max.numeric'     => _x( 'The :attribute may not be greater than :max.', ':attribute and :max are placeholders and should not be translated.', 'site-reviews' ),
			'max.string'      => _x( 'The :attribute may not be greater than :max characters.', ':attribute and :max are placeholders and should not be translated.', 'site-reviews' ),
			'min.numeric'     => _x( 'The :attribute must be at least :min.', ':attribute and :min are placeholders and should not be translated.', 'site-reviews' ),
			'min.string'      => _x( 'The :attribute must be at least :min characters.', ':attribute and :min are placeholders and should not be translated.', 'site-reviews' ),
			'regex'           => _x( 'The :attribute format is invalid.', ':attribute is a placeholder and should not be translated.', 'site-reviews' ),
			'required'        => _x( 'The :attribute field is required.', ':attribute is a placeholder and should not be translated.', 'site-reviews' ),
		];
	}
}
