<?php defined( 'WPINC' ) || die; ?>

<p>Hooks (also known as Filters &amp; Actions) are used to make changes to the plugin without modifying the core files of the plugin directly. In order to use the following hooks, you must add them to your theme's <code>functions.php</code> file.</p>

<div class="glsr-card card">
	<h3>Disable the plugin CSS</h3>
	<pre><code>add_filter( 'site-reviews/assets/css', '__return_false' );</code></pre>
	<p>Use this hook if you want to disable the plugin stylesheet from loading on your website.</p>
</div>

<div class="glsr-card card">
	<h3>Disable the plugin javascript</h3>
	<pre><code>add_filter( 'site-reviews/assets/js', '__return_false' );</code></pre>
	<p>Use this hook if you want to disable the plugin javascript from loading on your website.</p>
</div>

<div class="glsr-card card">
	<h3>Do something immediately after a review has been saved.</h3>
	<pre><code>add_action( 'site-reviews/local/review/create', function( $post_data, $meta ) {
	// do something here.
}, 10, 2 );</code></pre>
	<p>Use this hook if you want to do something immediately after a review has been saved to the database.</p>
</div>
