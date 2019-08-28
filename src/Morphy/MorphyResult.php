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
	public function imenitelniy()
    {
		return $this->data['i'];
	}

    public function roditelniy()
    {
		return $this->data['r'];
	}

    public function datelniy()
    {
		return $this->data['d'];
	}

    public function vinitelniy()
    {
		return $this->data['v'];
	}

    public function tvoritelniy()
    {
		return $this->data['t'];
	}

    public function predlozhniy()
    {
		return $this->data['p'];
	}

	public function getData()
    {
        return $this->data;
    }

    public function setForm(string $padezh, string $form)
    {
        $this->data($padezh, $form);

        return $this;
    }

    public function getForm(string $padezh)
    {
        return $this->data[$padezh];
    }

	function __toString()
	{
		return $this->imenitelniy();
	}
}