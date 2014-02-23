<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework NCompilerLess Class
 *
 * @package       Framework
 * @subpackage    FormatterClassic
 * @since         1.0
 * 
 * This class allows to compile less files and add ability to store file informations for simple and
 * secure caching and is taken near verbatim from:
 *
 * changes are marked with "NRDK -- BEGIN CHANGE" and "NRDK -- END CHANGE" comment markers
 *
 * lessphp v0.4.0
 * http://leafo.net/lessphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org
 *
 * Copyright 2013, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 * 
 */
class NCompilerLessFormatterClassic {
	public $indentChar = "  ";

	public $break = "\n";
	public $open = " {";
	public $close = "}";
	public $selectorSeparator = ", ";
	public $assignSeparator = ":";

	public $openSingle = " { ";
	public $closeSingle = " }";

	public $disableSingle = false;
	public $breakSelectors = false;

	public $compressColors = false;

	public function __construct() {
		$this->indentLevel = 0;
	}

	public function indentStr($n = 0) {
		return str_repeat($this->indentChar, max($this->indentLevel + $n, 0));
	}

	public function property($name, $value) {
		return $name . $this->assignSeparator . $value . ";";
	}

	protected function isEmpty($block) {
		if (empty($block->lines)) {
			foreach ($block->children as $child) {
				if (!$this->isEmpty($child)) return false;
			}

			return true;
		}
		return false;
	}

	public function block($block) {
		if ($this->isEmpty($block)) return;

		$inner = $pre = $this->indentStr();

		$isSingle = !$this->disableSingle &&
			is_null($block->type) && count($block->lines) == 1;

		if (!empty($block->selectors)) {
			$this->indentLevel++;

			if ($this->breakSelectors) {
				$selectorSeparator = $this->selectorSeparator . $this->break . $pre;
			} else {
				$selectorSeparator = $this->selectorSeparator;
			}

			echo $pre .
				implode($selectorSeparator, $block->selectors);
			if ($isSingle) {
				echo $this->openSingle;
				$inner = "";
			} else {
				echo $this->open . $this->break;
				$inner = $this->indentStr();
			}

		}

		if (!empty($block->lines)) {
			$glue = $this->break.$inner;
			echo $inner . implode($glue, $block->lines);
			if (!$isSingle && !empty($block->children)) {
				echo $this->break;
			}
		}

		foreach ($block->children as $child) {
			$this->block($child);
		}

		if (!empty($block->selectors)) {
			if (!$isSingle && empty($block->children)) echo $this->break;

			if ($isSingle) {
				echo $this->closeSingle . $this->break;
			} else {
				echo $pre . $this->close . $this->break;
			}

			$this->indentLevel--;
		}
	}
}