<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use Sepia\PoParser;

class Translation
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $entries;

	/**
	 * @var PoParser
	 */
	protected $po;

	/**
	 * @var array
	 */
	protected $results;

	public function __construct( App $app, Database $db, PoParser $po )
	{
		$this->app = $app;
		$this->db = $db;
		$this->po = $po;
		$this->entries = $this->normalize( $po->parse() );
		$this->reset();
	}

	/**
	 * @return array
	 */
	public function all()
	{
		$translations = $this->getSettings();
		$entries = $this->filter( $translations, $this->entries )->results();
		array_walk( $translations, function( &$entry ) use( $entries ) {
			$entry['desc'] = $this->getEntryString( $entries[$entry['id']], 'msgctxt' );
			$entry['msgid'] = $this->getEntryString( $entries[$entry['id']], 'msgid' );
			if( isset( $entries[$entry['id']]['msgid_plural'] )) {
				$entry['msgid_plural'] = $entries[$entry['id']]['msgid_plural'];
			}
		});
		return $translations;
	}

	/**
	 * @return array
	 */
	public function entries()
	{
		return $this->entries;
	}

	/**
	 * @param null|array $entriesToExclude
	 * @param null|array $entries
	 * @return Translation
	 */
	public function exclude( $entriesToExclude = null, $entries = null )
	{
		return $this->filter( $entriesToExclude, $entries, false );
	}

	/**
	 * @param null|array $filterWith
	 * @param null|array $entries
	 * @param bool $intersect
	 * @return Translation
	 */
	public function filter( $filterWith = null, $entries = null, $intersect = true )
	{
		if( !is_array( $entries )) {
			$entries = $this->results;
		}
		if( !is_array( $filterWith )) {
			$filterWith = $this->getSettings();
		}
		$keys = array_flip( array_column( $filterWith, 'id' ));
		$this->results = $intersect
			? array_intersect_key( $entries, $keys )
			: array_diff_key( $entries, $keys );
		return $this;
	}

	/**
	 * @param string $template
	 * @return string
	 */
	public function render( $template, array $entry )
	{
		ob_start();
		include sprintf( '%sviews/strings/%s.php', $this->app->path, $template );
		$template = ob_get_clean();
		foreach( $entry as $key => $value ) {
			if( is_array( $value ))continue;
			$template = str_replace( sprintf( '{{ data.%s }}', $key ), esc_attr( $value ), $template );
		}
		return $template;
	}

	/**
	 * @return string
	 */
	public function renderAll()
	{
		$rendered = '';
		foreach( $this->all() as $index => $entry ) {
			$entry['index'] = $index;
			$entry['prefix'] = $this->db->getOptionName();
			$template = isset( $entry['plural'] ) ? 'plural' : 'single';
			$rendered .= $this->render( $template, $entry );
		}
		return $rendered;
	}

	/**
	 * @param bool $resetAfterRender
	 * @return string
	 */
	public function renderResults( $resetAfterRender = true )
	{
		$rendered = '';
		foreach( $this->results as $id => $entry ) {
			$data = [
				'desc' => $this->getEntryString( $entry, 'msgctxt' ),
				'id' => $id,
				'plural' => $this->getEntryString( $entry, 'msgid_plural' ),
				'single' => $this->getEntryString( $entry, 'msgid' ),
			];
			$text = !empty( $data['plural'] )
				? sprintf( '%s | %s', $data['single'], $data['plural'] )
				: $data['single'];
			$rendered .= $this->render( 'result', [
				'entry' => wp_json_encode( $data ),
				'text' => $text,
			]);
		}
		if( $resetAfterRender ) {
			$this->reset();
		}
		return $rendered;
	}

	/**
	 * @return void
	 */
	public function reset()
	{
		$this->results = [];
	}

	/**
	 * @return array
	 */
	public function results()
	{
		$results = $this->results;
		$this->reset();
		return $results;
	}

	/**
	 * @param string $needle
	 * @param int $threshold
	 * @param bool $caseSensitive
	 * @return Translation
	 */
	public function search( $needle = '', $threshold = 3, $caseSensitive = false )
	{
		$this->reset();
		$needle = trim( $needle );
		foreach( $this->entries as $key => $entry ) {
			$single = $this->getEntryString( $entry, 'msgid' );
			$plural = $this->getEntryString( $entry, 'msgid_plural' );
			if( !$caseSensitive ) {
				$needle = strtolower( $needle );
				$single = strtolower( $single );
				$plural = strtolower( $plural );
			}
			if( strlen( $needle ) < $threshold ) {
				if( in_array( $needle, [$single, $plural] )) {
					$this->results[$key] = $entry;
				}
			}
			else if( strpos( sprintf( '%s %s', $single, $plural ), $needle ) !== false ) {
				$this->results[$key] = $entry;
			}
		}
		return $this;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function getEntryString( array $entry, $key )
	{
		return isset( $entry[$key] )
			? implode( '', (array) $entry[$key] )
			: '';
	}

	/**
	 * @return array
	 */
	protected function getSettings()
	{
		return $this->db->getOption( 'strings', [], true );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $entries )
	{
		$keys = [
			'msgctxt', 'msgid', 'msgid_plural', 'msgstr', 'msgstr[0]', 'msgstr[1]',
		];
		array_walk( $entries, function( &$entry ) use( $keys ) {
			foreach( $keys as $key ) {
				$entry = $this->normalizeEntryString( $entry, $key );
			}
		});
		return $entries;
	}

	/**
	 * @param string $key
	 * @return array
	 */
	protected function normalizeEntryString( array $entry, $key )
	{
		if( isset( $entry[$key] )) {
			$entry[$key] = $this->getEntryString( $entry, $key );
		}
		return $entry;
	}
}
