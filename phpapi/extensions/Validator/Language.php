<?php

/**
 * This is the new function for getting translated interface messages.
 * See the Message class for documentation how to use them.
 * The intention is that this function replaces all old wfMsg* functions.
 * @param $key \string Message key.
 * Varargs: normal message parameters.
 * @return Message
 * @since 1.17
 */
function wfMessage( $key /*...*/) {
	$params = func_get_args();
	array_shift( $params );
	if ( isset( $params[0] ) && is_array( $params[0] ) ) {
		$params = $params[0];
	}
	return new Message( $key, $params );
}

/**
 * This function accepts multiple message keys and returns a message instance
 * for the first message which is non-empty. If all messages are empty then an
 * instance of the first message key is returned.
 * @param varargs: message keys
 * @return Message
 * @since 1.18
 */
function wfMessageFallback( /*...*/ ) {
	$args = func_get_args();
	return MWFunction::callArray( 'Message::newFallbackSequence', $args );
}

/**
 * Get a message from anywhere, for the current user language.
 *
 * Use wfMsgForContent() instead if the message should NOT
 * change depending on the user preferences.
 *
 * @param $key String: lookup key for the message, usually
 *    defined in languages/Language.php
 *
 * This function also takes extra optional parameters (not
 * shown in the function definition), which can be used to
 * insert variable text into the predefined message.
 * @return String
 */
function wfMsg( $key ) {
	$args = func_get_args();
	array_shift( $args );
	return wfMsgReal( $key, $args, true );
}

/**
 * Same as above except doesn't transform the message
 *
 * @param $key String
 * @return String
 */
function wfMsgNoTrans( $key ) {
	$args = func_get_args();
	array_shift( $args );
	return wfMsgReal( $key, $args, true, false, false );
}

/**
 * Get a message from anywhere, for the current global language
 * set with $wgLanguageCode.
 *
 * Use this if the message should NOT change dependent on the
 * language set in the user's preferences. This is the case for
 * most text written into logs, as well as link targets (such as
 * the name of the copyright policy page). Link titles, on the
 * other hand, should be shown in the UI language.
 *
 * Note that MediaWiki allows users to change the user interface
 * language in their preferences, but a single installation
 * typically only contains content in one language.
 *
 * Be wary of this distinction: If you use wfMsg() where you should
 * use wfMsgForContent(), a user of the software may have to
 * customize potentially hundreds of messages in
 * order to, e.g., fix a link in every possible language.
 *
 * @param $key String: lookup key for the message, usually
 *     defined in languages/Language.php
 * @return String
 */
function wfMsgForContent( $key ) {
	global $wgForceUIMsgAsContentMsg;
	$args = func_get_args();
	array_shift( $args );
	$forcontent = true;
	if( is_array( $wgForceUIMsgAsContentMsg ) &&
		in_array( $key, $wgForceUIMsgAsContentMsg ) )
	{
		$forcontent = false;
	}
	return wfMsgReal( $key, $args, true, $forcontent );
}

/**
 * Same as above except doesn't transform the message
 *
 * @param $key String
 * @return String
 */
function wfMsgForContentNoTrans( $key ) {
	global $wgForceUIMsgAsContentMsg;
	$args = func_get_args();
	array_shift( $args );
	$forcontent = true;
	if( is_array( $wgForceUIMsgAsContentMsg ) &&
		in_array( $key, $wgForceUIMsgAsContentMsg ) )
	{
		$forcontent = false;
	}
	return wfMsgReal( $key, $args, true, $forcontent, false );
}

/**
 * Get a message from the language file, for the UI elements
 *
 * @deprecated in 1.18; use wfMessage()
 */
function wfMsgNoDB( $key ) {
	wfDeprecated( __FUNCTION__ );
	$args = func_get_args();
	array_shift( $args );
	return wfMsgReal( $key, $args, false );
}

/**
 * Get a message from the language file, for the content
 *
 * @deprecated in 1.18; use wfMessage()
 */
function wfMsgNoDBForContent( $key ) {
	wfDeprecated( __FUNCTION__ );
	global $wgForceUIMsgAsContentMsg;
	$args = func_get_args();
	array_shift( $args );
	$forcontent = true;
	if( is_array( $wgForceUIMsgAsContentMsg ) &&
		in_array( $key,	$wgForceUIMsgAsContentMsg ) )
	{
		$forcontent = false;
	}
	return wfMsgReal( $key, $args, false, $forcontent );
}


/**
 * Really get a message
 *
 * @param $key String: key to get.
 * @param $args
 * @param $useDB Boolean
 * @param $forContent Mixed: Language code, or false for user lang, true for content lang.
 * @param $transform Boolean: Whether or not to transform the message.
 * @return String: the requested message.
 */
function wfMsgReal( $key, $args, $useDB = true, $forContent = false, $transform = true ) {
	wfProfileIn( __METHOD__ );
	$message = wfMsgGetKey( $key, $useDB, $forContent, $transform );
	$message = wfMsgReplaceArgs( $message, $args );
	wfProfileOut( __METHOD__ );
	return $message;
}

/**
 * This function provides the message source for messages to be edited which are *not* stored in the database.
 *
 * @deprecated in 1.18; use wfMessage()
 * @param $key String
 */
function wfMsgWeirdKey( $key ) {
	wfDeprecated( __FUNCTION__ );
	$source = wfMsgGetKey( $key, false, true, false );
	if ( wfEmptyMsg( $key ) ) {
		return '';
	} else {
		return $source;
	}
}

/**
 * Fetch a message string value, but don't replace any keys yet.
 *
 * @param $key String
 * @param $useDB Bool
 * @param $langCode String: Code of the language to get the message for, or
 *                  behaves as a content language switch if it is a boolean.
 * @param $transform Boolean: whether to parse magic words, etc.
 * @return string
 */
function wfMsgGetKey( $key, $useDB, $langCode = false, $transform = true ) {
	wfRunHooks( 'NormalizeMessageKey', array( &$key, &$useDB, &$langCode, &$transform ) );

	$cache = MessageCache::singleton();
	$message = $cache->get( $key, $useDB, $langCode );
	if( $message === false ) {
		$message = '&lt;' . htmlspecialchars( $key ) . '&gt;';
	} elseif ( $transform ) {
		$message = $cache->transform( $message );
	}
	return $message;
}

/**
 * Replace message parameter keys on the given formatted output.
 *
 * @param $message String
 * @param $args Array
 * @return string
 * @private
 */
function wfMsgReplaceArgs( $message, $args ) {
	# Fix windows line-endings
	# Some messages are split with explode("\n", $msg)
	$message = str_replace( "\r", '', $message );

	// Replace arguments
	if ( count( $args ) ) {
		if ( is_array( $args[0] ) ) {
			$args = array_values( $args[0] );
		}
		$replacementKeys = array();
		foreach( $args as $n => $param ) {
			$replacementKeys['$' . ( $n + 1 )] = $param;
		}
		$message = strtr( $message, $replacementKeys );
	}

	return $message;
}

/**
 * Return an HTML-escaped version of a message.
 * Parameter replacements, if any, are done *after* the HTML-escaping,
 * so parameters may contain HTML (eg links or form controls). Be sure
 * to pre-escape them if you really do want plaintext, or just wrap
 * the whole thing in htmlspecialchars().
 *
 * @param $key String
 * @param string ... parameters
 * @return string
 */
function wfMsgHtml( $key ) {
	$args = func_get_args();
	array_shift( $args );
	return wfMsgReplaceArgs( htmlspecialchars( wfMsgGetKey( $key, true ) ), $args );
}

/**
 * Return an HTML version of message
 * Parameter replacements, if any, are done *after* parsing the wiki-text message,
 * so parameters may contain HTML (eg links or form controls). Be sure
 * to pre-escape them if you really do want plaintext, or just wrap
 * the whole thing in htmlspecialchars().
 *
 * @param $key String
 * @param string ... parameters
 * @return string
 */
function wfMsgWikiHtml( $key ) {
	$args = func_get_args();
	array_shift( $args );
	return wfMsgReplaceArgs(
		MessageCache::singleton()->parse( wfMsgGetKey( $key, true ), null, /* can't be set to false */ true )->getText(),
		$args );
}

/**
 * Returns message in the requested format
 * @param $key String: key of the message
 * @param $options Array: processing rules. Can take the following options:
 *   <i>parse</i>: parses wikitext to HTML
 *   <i>parseinline</i>: parses wikitext to HTML and removes the surrounding
 *       p's added by parser or tidy
 *   <i>escape</i>: filters message through htmlspecialchars
 *   <i>escapenoentities</i>: same, but allows entity references like &#160; through
 *   <i>replaceafter</i>: parameters are substituted after parsing or escaping
 *   <i>parsemag</i>: transform the message using magic phrases
 *   <i>content</i>: fetch message for content language instead of interface
 * Also can accept a single associative argument, of the form 'language' => 'xx':
 *   <i>language</i>: Language object or language code to fetch message for
 *       (overriden by <i>content</i>).
 * Behavior for conflicting options (e.g., parse+parseinline) is undefined.
 *
 * @return String
 */
function wfMsgExt( $key, $options ) {
	$args = func_get_args();
	array_shift( $args );
	array_shift( $args );
	$options = (array)$options;

	foreach( $options as $arrayKey => $option ) {
		if( !preg_match( '/^[0-9]+|language$/', $arrayKey ) ) {
			# An unknown index, neither numeric nor "language"
			wfWarn( "wfMsgExt called with incorrect parameter key $arrayKey", 1, E_USER_WARNING );
		} elseif( preg_match( '/^[0-9]+$/', $arrayKey ) && !in_array( $option,
		array( 'parse', 'parseinline', 'escape', 'escapenoentities',
		'replaceafter', 'parsemag', 'content' ) ) ) {
			# A numeric index with unknown value
			wfWarn( "wfMsgExt called with incorrect parameter $option", 1, E_USER_WARNING );
		}
	}

	if( in_array( 'content', $options, true ) ) {
		$forContent = true;
		$langCode = true;
		$langCodeObj = null;
	} elseif( array_key_exists( 'language', $options ) ) {
		$forContent = false;
		$langCode = wfGetLangObj( $options['language'] );
		$langCodeObj = $langCode;
	} else {
		$forContent = false;
		$langCode = false;
		$langCodeObj = null;
	}

	$string = wfMsgGetKey( $key, /*DB*/true, $langCode, /*Transform*/false );

	if( !in_array( 'replaceafter', $options, true ) ) {
		$string = wfMsgReplaceArgs( $string, $args );
	}

	$messageCache = MessageCache::singleton();
	if( in_array( 'parse', $options, true ) ) {
		$string = $messageCache->parse( $string, null, true, !$forContent, $langCodeObj )->getText();
	} elseif ( in_array( 'parseinline', $options, true ) ) {
		$string = $messageCache->parse( $string, null, true, !$forContent, $langCodeObj )->getText();
		$m = array();
		if( preg_match( '/^<p>(.*)\n?<\/p>\n?$/sU', $string, $m ) ) {
			$string = $m[1];
		}
	} elseif ( in_array( 'parsemag', $options, true ) ) {
		$string = $messageCache->transform( $string,
				!$forContent, $langCodeObj );
	}

	if ( in_array( 'escape', $options, true ) ) {
		$string = htmlspecialchars ( $string );
	} elseif ( in_array( 'escapenoentities', $options, true ) ) {
		$string = Sanitizer::escapeHtmlAllowEntities( $string );
	}

	if( in_array( 'replaceafter', $options, true ) ) {
		$string = wfMsgReplaceArgs( $string, $args );
	}

	return $string;
}