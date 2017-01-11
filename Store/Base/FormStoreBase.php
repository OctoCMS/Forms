<?php

/**
 * Form base store for table: form

 */

namespace Octo\Forms\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Forms\Model\Form;
use Octo\Forms\Model\FormCollection;
use Octo\Forms\Store\FormStore;

/**
 * Form Base Store
 */
class FormStoreBase extends Store
{
    /** @var FormStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'form';

    /** @var string */
    protected $model = 'Octo\Forms\Model\Form';

    /** @var string */
    protected $key = 'id';

    /**
     * Return the database store for this model.
     * @return FormStore
     */
    public static function load() : FormStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new FormStore(Connection::get());
        }

        return self::$instance;
    }

    /**
    * @param $value
    * @return Form|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a Form object by Id.
     * @param $value
     * @return Form|null
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
}
