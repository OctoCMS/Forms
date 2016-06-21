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
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->validateNotNull('Definition', $value);
        $this->validateString('Definition', $value);

        if ($this->data['definition'] === $value) {
            return;
        }

        $this->data['definition'] = $value;
        $this->setModified('definition');
    }


    public function getDefinitionArray() : array
    {
        $value = json_decode($this->data['definition'], true);

        if (!is_array($value)) {
            $value = [];
        }

        return $value;
    }
}
