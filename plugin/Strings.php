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
			'add_new_item'          => __( 'Add New Review', 'geminilabs-site-reviews' ),
			'all_items'             => __( 'All Reviews', 'geminilabs-site-reviews' ),
			'archives'              => __( 'Review Archives', 'geminilabs-site-reviews' ),
			'edit_item'             => __( 'Edit Review', 'geminilabs-site-reviews' ),
			'insert_into_item'      => __( 'Insert into review', 'geminilabs-site-reviews' ),
			'new_item'              => __( 'New Review', 'geminilabs-site-reviews' ),
			'not_found'             => __( 'No Reviews found', 'geminilabs-site-reviews' ),
			'not_found_in_trash'    => __( 'No Reviews found in Trash', 'geminilabs-site-reviews' ),
			'search_items'          => __( 'Search Reviews', 'geminilabs-site-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this review', 'geminilabs-site-reviews' ),
			'view_item'             => __( 'View Review', 'geminilabs-site-reviews' ),
		];
	}

	public function post_updated_messages()
	{
		return [
			'approved'      => __( 'Review has been approved and published.', 'geminilabs-site-reviews' ),
			'draft_updated' => __( 'Review draft updated.', 'geminilabs-site-reviews' ),
			'preview'       => __( 'Preview review', 'geminilabs-site-reviews' ),
			'published'     => __( 'Review published.', 'geminilabs-site-reviews' ),
			'restored'      => __( 'Review restored to revision from %s.', 'geminilabs-site-reviews' ),
			'reverted'      => __( 'Review has been reverted to its original submission state.', 'geminilabs-site-reviews' ),
			'saved'         => __( 'Review saved.', 'geminilabs-site-reviews' ),
			'scheduled'     => __( 'Review scheduled for: %s.', 'geminilabs-site-reviews' ),
			'submitted'     => __( 'Review submitted.', 'geminilabs-site-reviews' ),
			'unapproved'    => __( 'Review has been unapproved and is now pending.', 'geminilabs-site-reviews' ),
			'updated'       => __( 'Review updated.', 'geminilabs-site-reviews' ),
			'view'          => __( 'View review', 'geminilabs-site-reviews' ),
		];
	}

	public function validation()
	{
		return [
			'accepted'        => _x( 'The :attribute must be accepted.', ':attribute is a placeholder and should not be translated.', 'geminilabs-site-reviews' ),
			'between.numeric' => _x( 'The :attribute must be between :min and :max.', ':attribute, :min, and :max are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'between.string'  => _x( 'The :attribute must be between :min and :max characters.', ':attribute, :min, and :max are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'email'           => _x( 'The :attribute must be a valid email address.', ':attribute is a placeholder and should not be translated.', 'geminilabs-site-reviews' ),
			'max.numeric'     => _x( 'The :attribute may not be greater than :max.', ':attribute and :max are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'max.string'      => _x( 'The :attribute may not be greater than :max characters.', ':attribute and :max are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'min.numeric'     => _x( 'The :attribute must be at least :min.', ':attribute and :min are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'min.string'      => _x( 'The :attribute must be at least :min characters.', ':attribute and :min are placeholders and should not be translated.', 'geminilabs-site-reviews' ),
			'regex'           => _x( 'The :attribute format is invalid.', ':attribute is a placeholder and should not be translated.', 'geminilabs-site-reviews' ),
			'required'        => _x( 'The :attribute field is required.', ':attribute is a placeholder and should not be translated.', 'geminilabs-site-reviews' ),
		];
	}
}
