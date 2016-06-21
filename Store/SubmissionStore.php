<?php

/**
 * Submission store for table: submission
 */

namespace Octo\Forms\Store;

use b8\Database;
use Octo;
use Octo\Forms\Model\Submission;

/**
 * Submission Store
 * @uses Octo\Forms\Store\Base\SubmissionStoreBase
 */
class SubmissionStore extends Base\SubmissionStoreBase
{
	/**
     * Get the total number of submissions in the system.
     * @return int
     */
    public function getTotal()
    {
        return $this->find()->count();
    }

    public function getAll($start = 0, $limit = 25)
    {
        return $this->find()
            ->order('id', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();
    }

    public function getAllForForm(Octo\Forms\Model\Form $form, $start = 0, $limit = 25)
    {
        return $this->where('form_id', $form->getId())
            ->order('id', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();
    }
}
