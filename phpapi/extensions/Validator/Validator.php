<?php

/**
 * Initialization file for the Validator extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Validator
 *
 * You will be validated. Resistance is futile.
 *
 * @file Validator.php
 * @ingroup Validator
 *
 * @licence GNU GPL v3 or later
 *
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

/**
 * This documenation group collects source code files belonging to Validator.
 *
 * Please do not use this group name for other code.
 *
 * @defgroup Validator Validator
 */

if ( !defined( 'PHP_API' ) ) {
	die( 'Not an entry point.' );
}

define( 'Validator_VERSION', '0.4.6 PHPAPI clone' );

// Register the internationalization file.
$wgExtensionMessagesFiles['Validator'] = dirname( __FILE__ ) . '/Validator.i18n.php';

// Autoload the classes.
$incDir = dirname( __FILE__ ) . '/includes/';
$globAutoloadClasses['CriterionValidationResult']	= $incDir . 'CriterionValidationResult.php'; 
$globAutoloadClasses['ItemParameterCriterion']	= $incDir . 'ItemParameterCriterion.php';
$globAutoloadClasses['ItemParameterManipulation']	= $incDir . 'ItemParameterManipulation.php';
$globAutoloadClasses['ListParameter'] 			= $incDir . 'ListParameter.php';
$globAutoloadClasses['ListParameterCriterion']	= $incDir . 'ListParameterCriterion.php';
$globAutoloadClasses['ListParameterManipulation']	= $incDir . 'ListParameterManipulation.php';
$globAutoloadClasses['Parameter'] 				= $incDir . 'Parameter.php';
$globAutoloadClasses['ParameterCriterion'] 		= $incDir . 'ParameterCriterion.php';
$globAutoloadClasses['ParameterInput']			= $incDir . 'ParameterInput.php';
$globAutoloadClasses['ParameterManipulation'] 	= $incDir . 'ParameterManipulation.php';
$globAutoloadClasses['ParserHook'] 				= $incDir . 'ParserHook.php';
$globAutoloadClasses['Validator'] 				= $incDir . 'Validator.php';
$globAutoloadClasses['TopologicalSort'] 			= $incDir . 'TopologicalSort.php';
// No need to autoload this one, since it's directly included below.
//$globAutoloadClasses['ValidationError']			= $incDir . 'ValidationError.php';
$globAutoloadClasses['ValidationErrorHandler']	= $incDir . 'ValidationErrorHandler.php';

$globAutoloadClasses['CriterionHasLength']		= $incDir . 'criteria/CriterionHasLength.php';
$globAutoloadClasses['CriterionInArray']			= $incDir . 'criteria/CriterionInArray.php';
$globAutoloadClasses['CriterionInRange']			= $incDir . 'criteria/CriterionInRange.php';
$globAutoloadClasses['CriterionIsFloat']			= $incDir . 'criteria/CriterionIsFloat.php';
$globAutoloadClasses['CriterionIsInteger']		= $incDir . 'criteria/CriterionIsInteger.php';
$globAutoloadClasses['CriterionIsNumeric']		= $incDir . 'criteria/CriterionIsNumeric.php';
$globAutoloadClasses['CriterionItemCount']		= $incDir . 'criteria/CriterionItemCount.php';
$globAutoloadClasses['CriterionMatchesRegex']		= $incDir . 'criteria/CriterionMatchesRegex.php';
$globAutoloadClasses['CriterionNotEmpty']			= $incDir . 'criteria/CriterionNotEmpty.php'; 
$globAutoloadClasses['CriterionTrue']				= $incDir . 'criteria/CriterionTrue.php';
$globAutoloadClasses['CriterionUniqueItems']		= $incDir . 'criteria/CriterionUniqueItems.php';

$globAutoloadClasses['ParamManipulationBoolean']	= $incDir . 'manipulations/ParamManipulationBoolean.php';
$globAutoloadClasses['ParamManipulationFloat']	= $incDir . 'manipulations/ParamManipulationFloat.php';
$globAutoloadClasses['ParamManipulationFunctions']= $incDir . 'manipulations/ParamManipulationFunctions.php';
$globAutoloadClasses['ParamManipulationImplode']	= $incDir . 'manipulations/ParamManipulationImplode.php';
$globAutoloadClasses['ParamManipulationInteger']	= $incDir . 'manipulations/ParamManipulationInteger.php';
$globAutoloadClasses['ParamManipulationString']	= $incDir . 'manipulations/ParamManipulationString.php';

$globAutoloadClasses['ValidatorDescribe'] 		= $incDir . 'parserHooks/Validator_Describe.php';
$globAutoloadClasses['ValidatorListErrors'] 		= $incDir . 'parserHooks/Validator_ListErrors.php';
unset( $incDir );

// This file needs to be included directly, since Validator_Settings.php
// uses it, in some rare cases before autoloading is defined.
require_once 'includes/ValidationError.php' ;
// Include the settings file.
require_once 'Validator_Settings.php';

require_once 'Language.php';