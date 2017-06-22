<?php defined( 'WPINC' ) || die; ?>

<form method="post" action="" name="glsr-<?= $form_id; ?>" class="<?= $class; ?>">
<?php

	$html->renderField([
		'type'       => 'select',
		'name'       => 'rating',
		'class'      => 'glsr-star-rating',
		'errors'     => $errors,
		'label'      => $db->getOption( 'settings.reviews-form.rating.label' ),
		'prefix'     => false,
		'render'     => !in_array( 'rating', $exclude ),
		'suffix'     => $form_id,
		'value'      => $values['rating'],
		'options'    => [
			''  => __( 'Select a Rating', 'site-reviews' ),
			'5' => __( 'Excellent', 'site-reviews' ),
			'4' => __( 'Very good', 'site-reviews' ),
			'3' => __( 'Average', 'site-reviews' ),
			'2' => __( 'Poor', 'site-reviews' ),
			'1' => __( 'Terrible', 'site-reviews' ),
		],
	]);

	$html->renderField([
		'type'        => 'text',
		'name'        => 'title',
		'errors'      => $errors,
		'label'       => $db->getOption( 'settings.reviews-form.title.label' ),
		'placeholder' => $db->getOption( 'settings.reviews-form.title.placeholder' ),
		'prefix'      => false,
		'render'      => !in_array( 'title', $exclude ),
		'required'    => true,
		'suffix'      => $form_id,
		'value'       => $values['title'],
	]);

	$html->renderField([
		'type'        => 'textarea',
		'name'        => 'content',
		'errors'      => $errors,
		'label'       => $db->getOption( 'settings.reviews-form.content.label' ),
		'placeholder' => $db->getOption( 'settings.reviews-form.content.placeholder' ),
		'prefix'      => false,
		'rows'        => 5,
		'render'      => !in_array( 'content', $exclude ),
		'required'    => true,
		'suffix'      => $form_id,
		'value'       => $values['content'],
	]);

	$html->renderField([
		'type'        => 'text',
		'name'        => 'name',
		'errors'      => $errors,
		'label'       => $db->getOption( 'settings.reviews-form.name.label' ),
		'placeholder' => $db->getOption( 'settings.reviews-form.name.placeholder' ),
		'prefix'      => false,
		'render'      => !in_array( 'name', $exclude ),
		'required'    => true,
		'suffix'      => $form_id,
		'value'       => $values['name'],
	]);

	$html->renderField([
		'type'        => 'email',
		'name'        => 'email',
		'errors'      => $errors,
		'label'       => $db->getOption( 'settings.reviews-form.email.label' ),
		'placeholder' => $db->getOption( 'settings.reviews-form.email.placeholder' ),
		'prefix'      => false,
		'render'      => !in_array( 'email', $exclude ),
		'required'    => true,
		'suffix'      => $form_id,
		'value'       => $values['email'],
	]);

	$html->renderField([
		'type'       => 'checkbox',
		'name'       => 'terms',
		'errors'     => $errors,
		'options'    => $db->getOption( 'settings.reviews-form.terms.label' ),
		'prefix'     => false,
		'render'     => !in_array( 'terms', $exclude ),
		'required'   => true,
		'suffix'     => $form_id,
		'value'      => $values['terms'],
	]);

	$html->renderField([
		'type'   => 'hidden',
		'name'   => 'action',
		'prefix' => false,
		'value'  => 'post-review',
	]);

	$html->renderField([
		'type'   => 'hidden',
		'name'   => 'form_id',
		'prefix' => false,
		'value'  => $form_id,
	]);

	$html->renderField([
		'type'   => 'hidden',
		'name'   => 'assign_to',
		'prefix' => false,
		'value'  => $assign_to,
	]);

	$html->renderField([
		'type'   => 'hidden',
		'name'   => 'category',
		'prefix' => false,
		'value'  => $category,
	]);

	$html->renderField([
		'type'   => 'hidden',
		'name'   => 'excluded',
		'prefix' => false,
		'value'  => esc_attr( json_encode( $exclude )),
	]);

	wp_nonce_field( 'post-review' );

	if( $message ) {
		printf( '<div class="glsr-form-messages%s">%s</div>', ( $errors ? ' gslr-has-errors' : '' ), wpautop( $message ));
	}

	$html->renderField([
		'type'   => 'submit',
		'prefix' => false,
		'value'  => $db->getOption( 'settings.reviews-form.submit.label' ),
	]);

?>
</form>
