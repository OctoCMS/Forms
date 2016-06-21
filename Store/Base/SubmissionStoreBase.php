<?php

/**
 * Submission base store for table: submission

 */

namespace Octo\Forms\Store\Base;

use Octo\Store;
use Octo\Forms\Model\Submission;
use Octo\Forms\Model\SubmissionCollection;

/**
 * Submission Base Store
 */
class SubmissionStoreBase extends Store
{
    protected $table = 'submission';
    protected $model = 'Octo\Forms\Model\Submission';
    protected $key = 'id';

    /**
    * @param $value
    * @return Submission|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a Submission object by Id.
     * @param $value
     * @return Submission|null
     */
    public function getById(int $value)
    {
        // This is the primary key, so try and get from cache:
        $cacheResult = $this->cacheGet($value);

        if (!empty($cacheResult)) {
            return $cacheResult;
        }

        $rtn = $this->where('id', $value)->first();
        $this->cacheSet($value, $rtn);

        return $rtn;
    }

    /**
     * Get all Submission objects by FormId.
     * @return \Octo\Forms\Model\SubmissionCollection
     */
    public function getByFormId($value, $limit = null)
    {
        return $this->where('form_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Submission by FormId value.
     * @return int
     */
    public function getTotalByFormId($value) : int
    {
        return $this->where('form_id', $value)->count();
    }

    /**
     * Get all Submission objects by ContactId.
     * @return \Octo\Forms\Model\SubmissionCollection
     */
    public function getByContactId($value, $limit = null)
    {
        return $this->where('contact_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Submission by ContactId value.
     * @return int
     */
    public function getTotalByContactId($value) : int
    {
        return $this->where('contact_id', $value)->count();
    }
}
