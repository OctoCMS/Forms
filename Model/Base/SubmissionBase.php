<?php

/**
 * Submission base model for table: submission
 */

namespace Octo\Forms\Model\Base;

use Octo\Model;
use Octo\Store;

/**
 * Submission Base Model
 */
class SubmissionBase extends Model
{
    protected function init()
    {
        $this->table = 'submission';
        $this->model = 'Submission';

        // Columns:
        
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        
        $this->data['form_id'] = null;
        $this->getters['form_id'] = 'getFormId';
        $this->setters['form_id'] = 'setFormId';
        
        $this->data['created_date'] = null;
        $this->getters['created_date'] = 'getCreatedDate';
        $this->setters['created_date'] = 'setCreatedDate';
        
        $this->data['contact_id'] = null;
        $this->getters['contact_id'] = 'getContactId';
        $this->setters['contact_id'] = 'setContactId';
        
        $this->data['extra'] = null;
        $this->getters['extra'] = 'getExtra';
        $this->setters['extra'] = 'setExtra';
        
        $this->data['message'] = null;
        $this->getters['message'] = 'getMessage';
        $this->setters['message'] = 'setMessage';
        
        // Foreign keys:
        
        $this->getters['Contact'] = 'getContact';
        $this->setters['Contact'] = 'setContact';
        
        $this->getters['Form'] = 'getForm';
        $this->setters['Form'] = 'setForm';
        
    }

    
    /**
     * Get the value of Id / id
     * @return int
     */

     public function getId()
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of FormId / form_id
     * @return int
     */

     public function getFormId()
     {
        $rtn = $this->data['form_id'];

        return $rtn;
     }
    
    /**
     * Get the value of CreatedDate / created_date
     * @return DateTime
     */

     public function getCreatedDate()
     {
        $rtn = $this->data['created_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
     }
    
    /**
     * Get the value of ContactId / contact_id
     * @return int
     */

     public function getContactId()
     {
        $rtn = $this->data['contact_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Extra / extra
     * @return array|null
     */

     public function getExtra()
     {
        $rtn = $this->data['extra'];

        $rtn = json_decode($rtn, true);

        if (empty($rtn)) {
            $rtn = null;
        }

        return $rtn;
     }
    
    /**
     * Get the value of Message / message
     * @return string
     */

     public function getMessage()
     {
        $rtn = $this->data['message'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     */
    public function setId(int $value)
    {

        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }
    
    /**
     * Set the value of FormId / form_id
     * @param $value int
     */
    public function setFormId(int $value)
    {


        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }

        $this->validateNotNull('FormId', $value);

        if ($this->data['form_id'] === $value) {
            return;
        }

        $this->data['form_id'] = $value;
        $this->setModified('form_id');
    }
    
    /**
     * Set the value of CreatedDate / created_date
     * @param $value DateTime
     */
    public function setCreatedDate($value)
    {
        $this->validateDate('CreatedDate', $value);
        $this->validateNotNull('CreatedDate', $value);

        if ($this->data['created_date'] === $value) {
            return;
        }

        $this->data['created_date'] = $value;
        $this->setModified('created_date');
    }
    
    /**
     * Set the value of ContactId / contact_id
     * @param $value int
     */
    public function setContactId(int $value)
    {


        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }

        $this->validateNotNull('ContactId', $value);

        if ($this->data['contact_id'] === $value) {
            return;
        }

        $this->data['contact_id'] = $value;
        $this->setModified('contact_id');
    }
    
    /**
     * Set the value of Extra / extra
     * @param $value array|null
     */
    public function setExtra($value)
    {
        $this->validateJson($value);
        $this->validateNotNull('Extra', $value);

        if ($this->data['extra'] === $value) {
            return;
        }

        $this->data['extra'] = $value;
        $this->setModified('extra');
    }
    
    /**
     * Set the value of Message / message
     * @param $value string
     */
    public function setMessage($value)
    {



        if ($this->data['message'] === $value) {
            return;
        }

        $this->data['message'] = $value;
        $this->setModified('message');
    }
    
    
    /**
     * Get the Contact model for this  by Id.
     *
     * @uses \Octo\System\Store\ContactStore::getById()
     * @uses \Octo\System\Model\Contact
     * @return \Octo\System\Model\Contact
     */
    public function getContact()
    {
        $key = $this->getContactId();

        if (empty($key)) {
           return null;
        }

        return Store::get('Contact')->getById($key);
    }

    /**
     * Set Contact - Accepts an ID, an array representing a Contact or a Contact model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setContact($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setContactId($value);
        }

        // Is this an instance of Contact?
        if (is_object($value) && $value instanceof \Octo\System\Model\Contact) {
            return $this->setContactObject($value);
        }

        // Is this an array representing a Contact item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setContactId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Contact.');
    }

    /**
     * Set Contact - Accepts a Contact model.
     *
     * @param $value \Octo\System\Model\Contact
     */
    public function setContactObject(\Octo\System\Model\Contact $value)
    {
        return $this->setContactId($value->getId());
    }

    /**
     * Get the Form model for this  by Id.
     *
     * @uses \Octo\Forms\Store\FormStore::getById()
     * @uses \Octo\Forms\Model\Form
     * @return \Octo\Forms\Model\Form
     */
    public function getForm()
    {
        $key = $this->getFormId();

        if (empty($key)) {
           return null;
        }

        return Store::get('Form')->getById($key);
    }

    /**
     * Set Form - Accepts an ID, an array representing a Form or a Form model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setForm($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setFormId($value);
        }

        // Is this an instance of Form?
        if (is_object($value) && $value instanceof \Octo\Forms\Model\Form) {
            return $this->setFormObject($value);
        }

        // Is this an array representing a Form item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setFormId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Form.');
    }

    /**
     * Set Form - Accepts a Form model.
     *
     * @param $value \Octo\Forms\Model\Form
     */
    public function setFormObject(\Octo\Forms\Model\Form $value)
    {
        return $this->setFormId($value->getId());
    }
}
