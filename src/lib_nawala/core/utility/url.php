<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework ApplicationInput Class
 *
 * @package       Framework
 * @subpackage    Application
 * @since         1.1
 * 
 * @deprecated in 2.0
 */
class NCoreUtilityUrl
{
	/**
	 * Splits url into array of it's pieces as follows:
	 * [scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]
	 * In addition it adds 'query_params' key which contains array of
	 * url-decoded key-value pairs
	 *
	 * @param String $sUrl Url
	 *
	 * @return Array Parsed url pieces
	 */
	public static function explode($sUrl)
	{
		$aUrl                 = parse_url($sUrl);
		$aUrl['query_params'] = array();
		$aPairs               = array();

		if (array_key_exists('query', $aUrl)) {
			$aUrl['query'] = preg_replace('/&(?!amp;)/i', '&amp;', $aUrl['query']);
			$aPairs        = explode('&amp;', $aUrl['query']);
		}

		foreach ($aPairs as $sPair) {
			if (trim($sPair) == '') {
				continue;
			}

			if (strpos($sPair, '=') !== false) {
				list($sKey, $sValue) = explode('=', $sPair);
				$aUrl['query_params'][$sKey] = urldecode($sValue);
			} else {
				$aUrl['query_params'][$sPair] = '';
			}
		}

		return $aUrl;
	}


	/**
	 * Compiles url out of array of it's pieces (returned by explodeUrl)
	 * 'query' is ignored if 'query_params' is present
	 *
	 * @param Array $aUrl Array of url pieces
	 */
	public static function implode($aUrl)
	{
		//[scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]

		$sQuery = '';

		// Compile query
		if (isset($aUrl['query_params']) && is_array($aUrl['query_params'])) {
			$aPairs = array();
			foreach ($aUrl['query_params'] as $sKey=> $sValue) {
				$kvp = $sKey;

				if (!empty($sValue)) {
					$kvp .= '=' . urlencode($sValue);
				}

				$aPairs[] = $kvp;
			}

			$sQuery = implode('&amp;', $aPairs);
		} else {
			$sQuery = $aUrl['query'];
		}

		// Compile url
		$sUrl = (isset($aUrl['scheme']) && isset($aUrl['host]']) ? $aUrl['scheme'] . '://' . (isset($aUrl['user']) && $aUrl['user'] != '' && isset($aUrl['pass']) ? $aUrl['user'] . ':' . $aUrl['pass'] . '@' : '') . $aUrl['host'] : '') . (isset($aUrl['path']) && $aUrl['path'] != '' ? $aUrl['path'] : '') . ($sQuery != '' ? '?' . $sQuery : '') . (isset($aUrl['fragment']) && $aUrl['fragment'] != '' ? '#' . $aUrl['fragment'] : '');

		return $sUrl;
	}


	/**
	 * Parses url and returns array of key-value pairs of url params
	 *
	 * @param String $sUrl
	 *
	 * @return Array
	 */
	public static function getParams($sUrl)
	{
		$aUrl = NCoreUtilityUrl::explode($sUrl);

		return $aUrl['query_params'];
	}


	/**
	 * Removes existing url params and sets them to those specified in $aParams
	 *
	 * @param String $sUrl    Url
	 * @param Array  $aParams Array of Key-Value pairs to set url params to
	 *
	 * @return  String Newly compiled url
	 */
	public static function setParams($sUrl, $aParams)
	{
		$aUrl                 = NCoreUtilityUrl::explode($sUrl);
		$aUrl['query']        = '';
		$aUrl['query_params'] = $aParams;

		return NCoreUtilityUrl::implode($aUrl);
	}


	/**
	 * Updates values of existing url params and/or adds (if not set) those specified in $aParams
	 *
	 * @param String $sUrl    Url
	 * @param Array  $aParams Array of Key-Value pairs to set url params to
	 *
	 * @return  String Newly compiled url
	 */
	public static function updateParams($sUrl, $aParams)
	{
		$aUrl                 = NCoreUtilityUrl::explode($sUrl);
		$aUrl['query']        = '';
		$aUrl['query_params'] = array_merge($aUrl['query_params'], $aParams);

		return NCoreUtilityUrl::implode($aUrl);
	}
}