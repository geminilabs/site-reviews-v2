<?php defined( 'WPINC' ) or die;

$file = trailingslashit( __DIR__ ) . "{$view}.php";

if( file_exists( $file ) ) {
	include $file;
}
else {
	$log->error( sprintf( 'File not found: %s', $file ) );
}
