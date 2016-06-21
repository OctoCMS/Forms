<?php

/**
 * Form base store for table: form

 */

namespace Octo\Forms\Store\Base;

use Octo\Store;
use Octo\Forms\Model\Form;
use Octo\Forms\Model\FormCollection;

/**
 * Form Base Store
 */
class FormStoreBase extends Store
{
    protected $table = 'form';
    protected $model = 'Octo\Forms\Model\Form';
    protected $key = 'id';

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
