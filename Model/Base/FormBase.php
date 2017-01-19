<?php

/**
 * Form base model for table: form
 */

namespace Octo\Forms\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;

use Octo\Forms\Store\FormStore;
use Octo\Forms\Model\Form;

/**
 * Form Base Model
 */
abstract class FormBase extends Model
{
    protected $table = 'form';
    protected $model = 'Form';
    protected $data = [
        'id' => null,
        'title' => null,
        'recipients' => null,
        'definition' => null,
        'thankyou_message' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'title' => 'getTitle',
        'recipients' => 'getRecipients',
        'definition' => 'getDefinition',
        'thankyou_message' => 'getThankyouMessage',
    ];

    protected $setters = [
        'id' => 'setId',
        'title' => 'setTitle',
        'recipients' => 'setRecipients',
        'definition' => 'setDefinition',
        'thankyou_message' => 'setThankyouMessage',
    ];

    /**
     * Return the database store for this model.
     * @return FormStore
     */
    public static function Store() : FormStore
    {
        return FormStore::load();
    }

    /**
     * Get Form by primary key: id
     * @param int $id
     * @return Form|null
     */
    public static function get(int $id) : ?Form
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return Form
     */
    public function save() : Form
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save Form');
        }

        if (!($rtn instanceof Form)) {
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
     * Get the value of Title / title
     * @return string
     */
     public function getTitle() : string
     {
        $rtn = $this->data['title'];

        return $rtn;
     }
    
    /**
     * Get the value of Recipients / recipients
     * @return string
     */
     public function getRecipients() : ?string
     {
        $rtn = $this->data['recipients'];

        return $rtn;
     }
    
    /**
     * Get the value of Definition / definition
     * @return array
     */
     public function getDefinition() : ?array
     {
        $rtn = $this->data['definition'];

        $rtn = json_decode($rtn, true);

        if ($rtn === false) {
            $rtn = null;
        }

        return $rtn;
     }
    
    /**
     * Get the value of ThankyouMessage / thankyou_message
     * @return string
     */
     public function getThankyouMessage() : ?string
     {
        $rtn = $this->data['thankyou_message'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     * @return Form
     */
    public function setId(int $value) : Form
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of Title / title
     * @param $value string
     * @return Form
     */
    public function setTitle(string $value) : Form
    {

        if ($this->data['title'] !== $value) {
            $this->data['title'] = $value;
            $this->setModified('title');
        }

        return $this;
    }
    
    /**
     * Set the value of Recipients / recipients
     * @param $value string
     * @return Form
     */
    public function setRecipients(?string $value) : Form
    {

        if ($this->data['recipients'] !== $value) {
            $this->data['recipients'] = $value;
            $this->setModified('recipients');
        }

        return $this;
    }
    
    /**
     * Set the value of Definition / definition
     * @param $value array
     * @return Form
     */
    public function setDefinition($value) : Form
    {
        $this->validateJson($value);

        if ($this->data['definition'] !== $value) {
            $this->data['definition'] = $value;
            $this->setModified('definition');
        }

        return $this;
    }
    
    /**
     * Set the value of ThankyouMessage / thankyou_message
     * @param $value string
     * @return Form
     */
    public function setThankyouMessage(?string $value) : Form
    {

        if ($this->data['thankyou_message'] !== $value) {
            $this->data['thankyou_message'] = $value;
            $this->setModified('thankyou_message');
        }

        return $this;
    }
    
}
