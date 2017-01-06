<?php defined( 'WPINC' ) || die; ?>

<p>Hooks (also known as Filters &amp; Actions) are used to make changes to the plugin without modifying the core files of the plugin directly. In order to use the following hooks, you must add them to your theme's <code>functions.php</code> file.</p>

<div class="glsr-card card">
	<h3>Change the review excerpt length</h3>
	<pre><code>add_filter( 'site-reviews/reviews/excerpt_length', function() { return 55; });</code></pre>
	<p>Use this hook if you want to modify the excerpt word length (the default excerpt length is 55 words).</p>
</div>

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
	<h3>Display the full review instead of an excerpt</h3>
	<pre><code>add_filter( 'site-reviews/reviews/use_excerpt', '__return_false' );</code></pre>
	<p>Use this hook if you want to show the full review content instead of just an excerpt.</p>
</div>

