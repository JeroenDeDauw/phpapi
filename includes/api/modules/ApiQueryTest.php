<?php

/**
 * @ingroup API
 */
class ApiQueryTest extends ApiQueryBase {

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'test' );
	}

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
		return 'Test query module';
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
