<?php defined( 'WPINC' ) or die; ?>

<div class="wrap">

	<h1 class="page-title"><?= esc_html( get_admin_page_title() ); ?></h1>

<?php

	printf( '<div id="glsr-notices">%s</div>', $notices->show( false ) );

	$html->renderPartial( 'tabs' , [
		'page' => $page,
		'tabs' => $tabs,
		'tab'  => $tabView,
	]);

	$html->renderPartial( 'subsubsub' , [
		'page'    => $page,
		'tabs'    => $tabs,
		'tab'     => $tabView,
		'section' => $tabViewSection,
	]);

	$view = $tabViewSection ? "{$tabView}/{$tabViewSection}" : $tabView;
	$view = trailingslashit( __DIR__ ) . "{$page}/{$view}.php";

	// Allow addons to set their own section view locations
	$view = apply_filters( 'site-reviews/addon/section/view', $view, $page, $tabView, $tabViewSection );

	if( file_exists( $view ) ) {
		include $view;
	}
	else {
		$log->error( sprintf( 'File not found: %s', $view ) );
	}

?>

</div>
