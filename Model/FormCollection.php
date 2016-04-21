<?php

/**
 * Form model collection
 */

namespace Octo\Forms\Model;

use Octo;
use b8\Model\Collection;

/**
 * Form Model Collection
 */
class FormCollection extends Collection
{
    /**
     * Add a Form model to the collection.
     * @param string $key
     * @param Form $value
     * @return FormCollection
     */
    public function addForm($key, Form $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return Form|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
