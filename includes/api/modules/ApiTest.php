<?php


/**
 * @ingroup API
 */
class ApiTest extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	/**
	 * Purges the cache of a page
	 */
	public function execute() {
	}


	public function getAllowedParams() {
		return array(
		);
	}

	public function getParamDescription() {
		return array(
		);
	}

	public function getDescription() {
		return array( 'Test module' );
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			
		) );
	}

	protected function getExamples() {
		return array(
			''
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}
}
