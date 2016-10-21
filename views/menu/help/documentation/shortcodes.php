<?php defined( 'WPINC' ) or die; ?>

<div class="glsr-card card">
	<h3>[site_reviews_form]</h3>
	<p>This shortcode displays the review submission form.</p>
	<code>id="type some random text here"</code>
	<p>This shortcode should only be used on a page once. However, if for any reason you need to include more than one on a page, add the "id" attribute to each with some random text to make it a unique shortcode form.</p>
	<code>title="Submit a Review"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
	<code>description="Required fields are marked &lt;span&gt;*&lt;/span&gt;"</code>
	<p>By default, the shortcode displays no description. Include the "description" attribute to display a custom shortcode description.</p>
	<code>hide=title,reviewer,email,terms</code>
	<p>Add the "hide" attribute to exclude certain fields in the form.</p>
	<code>class="my-reviews-form"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode form.</p>
</div>

<div class="glsr-card card">
	<h3>[site_reviews]</h3>
	<p>This shortcode displays your most recent submitted reviews.</p>
	<code>title="Our Reviews"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
	<code>display=title,excerpt,author,date,rating,url</code>
	<p>By default, the shortcode displays all review fields. Include the "display" attribute to display only the specific fields you want.</p>
	<code>count=10</code>
	<p>By default, the shortcode displays the latest 10 reviews. Include the "count" attribute to change the number of reviews that are displayed.</p>
	<code>rating=4</code>
	<p>By default, the shortcode displays only 5-star reviews. Include the "rating" attribute to set the minimum star-rating of reviews to display.</p>
	<code>class="my-reviews full-width"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>
</div>
