<?php
/**
 * Created by PhpStorm.
 * User: LittlePoo
 * Date: 14.12.2015
 * Time: 10:17
 */

namespace Morphy;

class MorphyResult
{
	function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	function imenitelniy() {
		return $this->data['i'];
	}

	function roditelniy() {
		return $this->data['r'];
	}

	function datelniy() {
		return $this->data['d'];
	}

	function vinitelniy() {
		return $this->data['v'];
	}

	function tvoritelniy() {
		return $this->data['t'];
	}

	function predlozhniy() {
		return $this->data['p'];
	}

	function __toString()
	{
		return $this->imenitelniy();
	}
}