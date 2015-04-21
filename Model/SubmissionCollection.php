<?php

/**
 * Submission model collection
 */

namespace Octo\Forms\Model;

use Octo;
use b8\Model\Collection;

/**
 * Submission Model Collection
 */
class SubmissionCollection extends Collection
{
    /**
     * Add a Submission model to the collection.
     * @param string $key
     * @param Submission $value
     * @return SubmissionCollection
     */
    public function add($key, Submission $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return Submission|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
