<?php defined( 'WPINC' ) || die;

$file = trailingslashit( __DIR__ ) . "{$view}.php";

$file = apply_filters( 'site-reviews/addon/views/file', $file, $view, $data );

if( file_exists( $file ) ) {
	include $file;
}
else {
	$log->error( sprintf( 'File not found: %s', $file ) );
}
