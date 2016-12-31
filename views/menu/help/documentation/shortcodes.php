<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>[site_reviews]</h3>
	<p>This shortcode displays your most recently submitted reviews.</p>

	<code>class="my-reviews full-width"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>

	<code>count=10</code>
	<p>By default, the shortcode displays the latest 10 reviews. Include the "count" attribute to change the number of reviews that are displayed.</p>

	<code>hide=author,content,date,rating,title,url</code>
	<p>By default the shortcode displays all review fields. Include the "hide" attribute to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>

	<code>pagination=true</code>
	<p>Include the "pagination" attribute to display reviews in multiple pages (i.e. Page 1, Page 2, etc.). When using pagination, only one [site_reviews] shortcode can be used on a page at a time.</p>

	<code>rating=4</code>
	<p>By default, the shortcode displays only 5-star reviews. Include the "rating" attribute to set the minimum star-rating of reviews to display.</p>

	<code>title="Our Reviews"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
</div>

<div class="glsr-card card">
	<h3>[site_reviews_form]</h3>
	<p>This shortcode displays the review submission form.</p>

	<code>class="my-reviews-form"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode form.</p>

	<code>description="Required fields are marked &lt;span&gt;*&lt;/span&gt;"</code>
	<p>By default, the shortcode displays no description. Include the "description" attribute to display a custom shortcode description.</p>

	<code>hide=email,name,terms,title</code>
	<p>Add the "hide" attribute to exclude certain fields in the form. The rating and review fields cannot be excluded.</p>

	<code>id="type some random text here"</code>
	<p>This shortcode should only be used on a page once. However, if for any reason you need to include more than one on a page, add the "id" attribute to each with some random text to make it a unique shortcode form.</p>

	<code>title="Submit a Review"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
</div>
