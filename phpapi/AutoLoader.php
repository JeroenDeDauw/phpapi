<?php
/**
 * This defines autoloading handling 
 *
 * @file
 */

/**
 * Locations of core classes
 * Extension classes are specified with $wgAutoloadClasses
 * This array is a global instead of a static member of AutoLoader to work around a bug in APC
 */
global $globAutoloadLocalClasses, $globAutoloadClasses;

$phpApiDir = dirname( __FILE__ ) . '/';
$phpApiBaseDir = $phpApiDir . 'apibase/';
$phpApiFormatDir = $phpApiDir . 'formats/';
$phpApiModuleDir = $phpApiDir . 'modules/';

$globAutoloadLocalClasses = array(
	# Includes
	'Sanitizer' => $phpApiDir . 'Sanitizer.php',
	'WebRequest' => $phpApiDir. 'WebRequest.php',
	'FauxRequest' => $phpApiDir. 'WebRequest.php',
	'Hooks' => $phpApiDir. 'Hooks.php',
	'Xml' => $phpApiDir .'Xml.php',

	# API base
	'ApiBase' => $phpApiBaseDir . 'ApiBase.php',
	'ApiFormatBase' => $phpApiBaseDir . 'ApiFormatBase.php',
	'ApiHelp' => $phpApiBaseDir . 'ApiHelp.php',
	'ApiMain' => $phpApiBaseDir . 'ApiMain.php',
	'ApiResult' => $phpApiBaseDir . 'ApiResult.php',

	# API formats
	'ApiFormatDbg' => $phpApiFormatDir . 'ApiFormatDbg.php',
	'ApiFormatDump' => $phpApiFormatDir . 'ApiFormatDump.php',
	'ApiFormatJson' => $phpApiFormatDir . 'ApiFormatJson.php',
	'ApiFormatPhp' => $phpApiFormatDir . 'ApiFormatPhp.php',
	'ApiFormatRaw' => $phpApiFormatDir . 'ApiFormatRaw.php',
	'ApiFormatTxt' => $phpApiFormatDir . 'ApiFormatTxt.php',
	'ApiFormatWddx' => $phpApiFormatDir . 'ApiFormatWddx.php',
	'ApiFormatXml' => $phpApiFormatDir . 'ApiFormatXml.php',
	'ApiFormatYaml' => $phpApiFormatDir . 'ApiFormatYaml.php',

	# API modules
	'ApiTest' => $phpApiModuleDir . 'ApiTest.php',
);

class AutoLoader {
	/**
	 * autoload - take a class name and attempt to load it
	 *
	 * @param $className String: name of class we're looking for.
	 * @return bool Returning false is important on failure as
	 * it allows Zend to try and look in other registered autoloaders
	 * as well.
	 */
	static function autoload( $className ) {
		global $globAutoloadLocalClasses, $globAutoloadClasses;

		if ( isset( $globAutoloadLocalClasses[$className] ) ) {
			$filename = $globAutoloadLocalClasses[$className];
		} elseif ( isset( $globAutoloadClasses[$className] ) ) {
			$filename = $globAutoloadClasses[$className];
		} else {
			return false;
		}

		# Make an absolute path, this improves performance by avoiding some stat calls
		if ( substr( $filename, 0, 1 ) != '/' && substr( $filename, 1, 1 ) != ':' ) {
			global $apiDir;
			$filename = "$apiDir/$filename";
		}

		require( $filename );

		return true;
	}

	/**
	 * Force a class to be run through the autoloader, helpful for things like
	 * Sanitizer that have define()s outside of their class definition. Of course
	 * this wouldn't be necessary if everything in MediaWiki was class-based. Sigh.
	 *
	 * @return Boolean Return the results of class_exists() so we know if we were successful
	 */
	static function loadClass( $class ) {
		return class_exists( $class );
	}
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( array( 'AutoLoader', 'autoload' ) );
} else {
	function __autoload( $class ) {
		AutoLoader::autoload( $class );
	}

	ini_set( 'unserialize_callback_func', '__autoload' );
}
