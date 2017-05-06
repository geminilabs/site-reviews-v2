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
	<h3>Do something immediately after a review has been submitted.</h3>
	<pre><code>add_action( 'site-reviews/local/review/submitted', function( $message, $request ) {
	// do something here.
}, 10, 2 );</code></pre>
	<p>Use this hook if you want to do something immediately after a review has been successfully submitted.</p>
	<p>The <code>$message</code> is the "successfully submitted" message returned to the user.</p>
	<p>The <code>$request</code> is the PHP object used to create the review. With this you can also determine the current referrer URI (<code>$request->referrer</code>) or whether the request is an AJAX request or not (<code>$request->ajaxRequest</code>).</p>

</div>

<div class="glsr-card card">
	<h3>Change the default <a href="https://developers.google.com/recaptcha/docs/language" target="_blank">reCAPTCHA language</a>.</h3>
	<pre><code>add_filter( 'site-reviews/recaptcha/language', function( $locale ) {
	// return a language code here (e.g. "en")
	return $locale;
});</code></pre>
	<p>This hook will only work when using "Custom Integration" reCAPTCHA setting.
</div>
