<?php


/**
 * @ingroup API
 */
class ApiTest extends ApiBase {

	/**
	 * Purges the cache of a page
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		
		if ( !isset( $params['foo'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'foo' ) );
		}

		if ( !is_integer( $params['bar'] ) ) {
			$this->dieUsageMsg( array( 'notanint', 'bar' ) );
		}			
		
		// Output the param values for demonstration purpouses.
		foreach ( $params as $name => $value ) {
			if ( is_array( $value ) ) {
				$this->getResult()->setIndexedTagName( $value, 'value' );
			}
			$this->getResult()->addValue(
				'paramvalues',
				$name,
				$value
			);
		}
		
		// Let's add some random demo result numerical data.
		$randomz = array();
		
		for ( $i = 0; $i < 10; $i++ ) {
			$randomz[] = mt_rand();
		}
		$this->getResult()->setIndexedTagName( $randomz, 'value' );
		$this->getResult()->addValue(
			null,
			'randomdata',
			$randomz
		);		
	}

	public function getParameters() {
		$params = array(); // parent::getParameters();
		
		$params['foo'] = new Parameter( 'foo' );
		$params['foo']->setDescription( 'Simple demo parameter of type string that is required.' );
		
		$params['bar'] = new Parameter( 'bar', Parameter::TYPE_INTEGER, 0 );
		$params['bar']->setDescription( 'Demo parameter of type integer, optional.' );
		
		$params['baz'] = new ListParameter( 'baz' );
		$params['bar']->setDescription( 'Demo parameter of type integer, optional.' );
		$params['bar']->setDefault( array( 'spam', 'spamz', 'spamzz' ) );
		
		return $params;
	}
	
	public function getAllowedParams() {
		return array(
			'foo' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
			),
			'bar' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
			),
			'baz' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => 'spam|spamz|spamzz',
			),
		);
	}

	public function getParamDescription() {
		return array(
			'foo' => 'Simple demo parameter of type string that is required.',
			'bar' => 'Demo parameter of type integer, optional.',
			'baz' => 'Demo parameter of type string list, optional.'
		);
	}

	public function getDescription() {
		return array( 'Test module with some demo parameters.' );
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'foo' ),
			array( 'notanint', 'bar' ),
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=test&foo=oHaiThere!',
			'api.php?action=test&foo=oHaiThere!&bar=9342&baz=stuff|more stuff|...|infinity',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}
}
