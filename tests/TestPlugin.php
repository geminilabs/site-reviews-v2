<?php

/**
 * @package   GeminiLabs\SiteReviews\Tests
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Commands\FetchUrls;
use GeminiLabs\SiteReviews\Tests\Setup;
use WP_UnitTestCase;

/**
 * Test case for the Plugin
 *
 * @group plugin
 */
class TestPlugin extends WP_UnitTestCase
{
	use Setup;

	public function test_toggle_pinned()
	{}

	public function test_toggle_publish()
	{}

	public function test_revert_review()
	{}
}
