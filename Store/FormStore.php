<?php

/**
 * Form store for table: form
 */

namespace Octo\Forms\Store;

use b8\Database;
use Octo;
use Octo\Forms\Model\Form;

/**
 * Form Store
 * @uses Octo\Forms\Store\Base\FormStoreBase
 */
class FormStore extends Base\FormStoreBase
{
	public function getAll($start = 0, $limit = 25)
    {
        return $this->find()
            ->order('title', 'ASC')
            ->offset($start)
            ->limit($limit)
            ->get();
    }
}
