<?php

/**
 * Form model for table: form
 */

namespace Octo\Forms\Model;

use Octo;

/**
 * Form Model
 * @uses Octo\Forms\Model\Base\FormBaseBase
 */
class Form extends Base\FormBase
{
	public function setDefinitionArray(array $value)
    {
        return $this->setDefinition($value);
    }

    public function getDefinitionArray() : array
    {
        return $this->getDefinition();
    }
}
