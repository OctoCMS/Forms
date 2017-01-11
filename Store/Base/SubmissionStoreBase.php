<?php

/**
 * Submission base store for table: submission

 */

namespace Octo\Forms\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Forms\Model\Submission;
use Octo\Forms\Model\SubmissionCollection;
use Octo\Forms\Store\SubmissionStore;

/**
 * Submission Base Store
 */
class SubmissionStoreBase extends Store
{
    /** @var SubmissionStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'submission';

    /** @var string */
    protected $model = 'Octo\Forms\Model\Submission';

    /** @var string */
    protected $key = 'id';

    /**
     * Return the database store for this model.
     * @return SubmissionStore
     */
    public static function load() : SubmissionStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new SubmissionStore(Connection::get());
        }

        return self::$instance;
    }

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
