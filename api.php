<?php

/**
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2006 Yuri Astrakhan <Firstname><Lastname>@gmail.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

/**
 * This file is the entry point for all API queries. It begins by checking
 * whether the API is enabled on this wiki; if not, it informs the user that
 * s/he should set $wgEnableAPI to true and exits. Otherwise, it constructs
 * a new ApiMain using the parameter passed to it as an argument in the URL
 * ('?action=') and with write-enabled set to the value of $wgEnableWriteAPI
 * as specified in LocalSettings.php. It then invokes "execute()" on the
 * ApiMain object instance, which produces output in the format sepecified
 * in the URL.
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

// So extensions (and other code) can check whether they're running in API mode
define( 'MW_API', true );

$apiDir = dirname( __FILE__ );

$globAPIModules = array();

// Initialise common code
require ( $apiDir . '/includes/WebStart.php' );

// URL safety checks
//
// See RawPage.php for details; summary is that MSIE can override the
// Content-Type if it sees a recognized extension on the URL, such as
// might be appended via PATH_INFO after 'api.php'.
//
// Some data formats can end up containing unfiltered user-provided data
// which will end up triggering HTML detection and execution, hence
// XSS injection and all that entails.
//
if ( $wgRequest->isPathInfoBad() ) {
	// TODO
	wfHttpError( 403, 'Forbidden',
		'Invalid file extension found in PATH_INFO. ' .
		'The API must be accessed through the primary script entry point.' );
	return;
}

require $apiDir . ( file_exists( $apiDir . '/api.config.php' ) ? '/api.config.php' : '/api.config.default.php' );

/* Construct an ApiMain with the arguments passed via the URL. What we get back
 * is some form of an ApiMain, possibly even one that produces an error message,
 * but we don't care here, as that is handled by the ctor.
 */
$processor = new ApiMain( $wgRequest );

// Process data & print results
$processor->execute();

