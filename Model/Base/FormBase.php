<?php

/**
 * Form base model for table: form
 */

namespace Octo\Forms\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;
use Octo\Forms\Model\Form;

/**
 * Form Base Model
 */
abstract class FormBase extends Model
{
    protected function init()
    {
        $this->table = 'form';
        $this->model = 'Form';

        // Columns:
        
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        
        $this->data['title'] = null;
        $this->getters['title'] = 'getTitle';
        $this->setters['title'] = 'setTitle';
        
        $this->data['recipients'] = null;
        $this->getters['recipients'] = 'getRecipients';
        $this->setters['recipients'] = 'setRecipients';
        
        $this->data['definition'] = null;
        $this->getters['definition'] = 'getDefinition';
        $this->setters['definition'] = 'setDefinition';
        
        $this->data['thankyou_message'] = null;
        $this->getters['thankyou_message'] = 'getThankyouMessage';
        $this->setters['thankyou_message'] = 'setThankyouMessage';
        
        // Foreign keys:
        
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
    
    

    public function Submissions() : Query
    {
        return Store::get('Submission')->where('form_id', $this->data['id']);
    }
}
