<?php

/**
 * Submission base model for table: submission
 */

namespace Octo\Forms\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;
use Octo\Forms\Model\Submission;
use Octo\Forms\Store\SubmissionStore;
use Octo\Forms\Model\Form;
use Octo\System\Model\Contact;

/**
 * Submission Base Model
 */
abstract class SubmissionBase extends Model
{
    protected $table = 'submission';
    protected $model = 'Submission';
    protected $data = [
        'id' => null,
        'form_id' => null,
        'created_date' => null,
        'contact_id' => null,
        'extra' => null,
        'message' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'form_id' => 'getFormId',
        'created_date' => 'getCreatedDate',
        'contact_id' => 'getContactId',
        'extra' => 'getExtra',
        'message' => 'getMessage',
        'Form' => 'getForm',
        'Contact' => 'getContact',
    ];

    protected $setters = [
        'id' => 'setId',
        'form_id' => 'setFormId',
        'created_date' => 'setCreatedDate',
        'contact_id' => 'setContactId',
        'extra' => 'setExtra',
        'message' => 'setMessage',
        'Form' => 'setForm',
        'Contact' => 'setContact',
    ];

    /**
     * Return the database store for this model.
     * @return SubmissionStore
     */
    public static function Store() : SubmissionStore
    {
        return SubmissionStore::load();
    }

    /**
     * Get Submission by primary key: id
     * @param int $id
     * @return Submission|null
     */
    public static function get(int $id) : ?Submission
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return Submission
     */
    public function save() : Submission
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save Submission');
        }

        if (!($rtn instanceof Submission)) {
            throw new \Exception('Unexpected ' . get_class($rtn) . ' received from save.');
        }

        $this->data = $rtn->toArray();

        return $this;
    }


    /**
     * Get the value of Id / id
     * @return int
     */
     public function getId() : int
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of FormId / form_id
     * @return int
     */
     public function getFormId() : int
     {
        $rtn = $this->data['form_id'];

        return $rtn;
     }
    
    /**
     * Get the value of CreatedDate / created_date
     * @return DateTime
     */
     public function getCreatedDate() : DateTime
     {
        $rtn = $this->data['created_date'];

        if (!empty($rtn)) {
            $rtn = new DateTime($rtn);
        }

        return $rtn;
     }
    
    /**
     * Get the value of ContactId / contact_id
     * @return int
     */
     public function getContactId() : int
     {
        $rtn = $this->data['contact_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Extra / extra
     * @return array
     */
     public function getExtra() : ?array
     {
        $rtn = $this->data['extra'];

        $rtn = json_decode($rtn, true);

        if ($rtn === false) {
            $rtn = null;
        }

        return $rtn;
     }
    
    /**
     * Get the value of Message / message
     * @return string
     */
     public function getMessage() : ?string
     {
        $rtn = $this->data['message'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     * @return Submission
     */
    public function setId(int $value) : Submission
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of FormId / form_id
     * @param $value int
     * @return Submission
     */
    public function setFormId(int $value) : Submission
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['form_id'] !== $value) {
            $this->data['form_id'] = $value;
            $this->setModified('form_id');
        }

        return $this;
    }
    
    /**
     * Set the value of CreatedDate / created_date
     * @param $value DateTime
     * @return Submission
     */
    public function setCreatedDate($value) : Submission
    {
        $this->validateDate('CreatedDate', $value);

        if ($this->data['created_date'] !== $value) {
            $this->data['created_date'] = $value;
            $this->setModified('created_date');
        }

        return $this;
    }
    
    /**
     * Set the value of ContactId / contact_id
     * @param $value int
     * @return Submission
     */
    public function setContactId(int $value) : Submission
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['contact_id'] !== $value) {
            $this->data['contact_id'] = $value;
            $this->setModified('contact_id');
        }

        return $this;
    }
    
    /**
     * Set the value of Extra / extra
     * @param $value array
     * @return Submission
     */
    public function setExtra($value) : Submission
    {
        $this->validateJson($value);

        if ($this->data['extra'] !== $value) {
            $this->data['extra'] = $value;
            $this->setModified('extra');
        }

        return $this;
    }
    
    /**
     * Set the value of Message / message
     * @param $value string
     * @return Submission
     */
    public function setMessage(?string $value) : Submission
    {

        if ($this->data['message'] !== $value) {
            $this->data['message'] = $value;
            $this->setModified('message');
        }

        return $this;
    }
    

    /**
     * Get the Form model for this  by Id.
     *
     * @uses \Octo\Forms\Store\FormStore::getById()
     * @uses Form
     * @return Form|null
     */
    public function getForm() : ?Form
    {
        $key = $this->getFormId();

        if (empty($key)) {
           return null;
        }

        return Form::Store()->getById($key);
    }

    /**
     * Set Form - Accepts an ID, an array representing a Form or a Form model.
     * @throws \Exception
     * @param $value mixed
     * @return Submission
     */
    public function setForm($value) : Submission
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setFormId($value);
        }

        // Is this an instance of Form?
        if (is_object($value) && $value instanceof Form) {
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
     * @param $value Form
     * @return Submission
     */
    public function setFormObject(Form $value) : Submission
    {
        return $this->setFormId($value->getId());
    }

    /**
     * Get the Contact model for this  by Id.
     *
     * @uses \Octo\System\Store\ContactStore::getById()
     * @uses Contact
     * @return Contact|null
     */
    public function getContact() : ?Contact
    {
        $key = $this->getContactId();

        if (empty($key)) {
           return null;
        }

        return Contact::Store()->getById($key);
    }

    /**
     * Set Contact - Accepts an ID, an array representing a Contact or a Contact model.
     * @throws \Exception
     * @param $value mixed
     * @return Submission
     */
    public function setContact($value) : Submission
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setContactId($value);
        }

        // Is this an instance of Contact?
        if (is_object($value) && $value instanceof Contact) {
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
     * @param $value Contact
     * @return Submission
     */
    public function setContactObject(Contact $value) : Submission
    {
        return $this->setContactId($value->getId());
    }
}
