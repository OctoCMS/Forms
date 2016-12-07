<?php

namespace Octo\Forms\Block;

use b8;
use b8\Config;
use b8\Form\Element\Button;
use b8\Form\Element\Select;
use Octo\Admin\Form as AdminForm;
use b8\Form\Element\Submit;
use Octo\Block;
use Octo\Form as FormElement;
use Octo\System\Model\Contact;
use Octo\Forms\Model\Form as FormModel;
use Octo\Forms\Model\Submission;
use Octo\Store;
use Octo\Template;
use Octo\Event;

class Form extends Block
{
    /**
     * @var Store\FormStore
     */
    protected $formStore;

    /**
     * @var Store\ContactStore
     */
    protected $contactStore;

    /**
     * @var Store\SubmissionStore
     */
    protected $submissionStore;

    /**
     * @var \Octo\Forms\Model\Form
     */
    protected $formModel;

    /**
     * @var \b8\Form
     */
    protected $form;

    public static function getInfo()
    {
        return [
            'title' => 'Form',
            'icon' => 'edit',
            'editor' => ['\Octo\Forms\Block\Form', 'getEditorForm']
        ];
    }

    public static function getEditorForm($item)
    {
        $form = new AdminForm();
        $form->setId('block_' . $item['id']);

        $store = Store::get('Form');
        $rtn = $store->getAll(0, 1000);

        $forms = [0 => 'Please select...'];
        foreach ($rtn as $frm) {
            $forms[$frm->getId()] = $frm->getTitle();
        }

        $formSelect = Select::create('id', 'Form');
        $formSelect->setId('block_form_form_' . $item['id']);
        $formSelect->setOptions($forms);
        $formSelect->setClass('select2');
        $form->addField($formSelect);

        $saveButton = new Button();
        $saveButton->setValue('Save ' . $item['name']);
        $saveButton->setClass('block-save btn btn-success');
        $form->addField($saveButton);

        if (isset($item['content']) && is_array($item['content'])) {
            $form->setValues($item['content']);
        }

        return $form;
    }

    protected function init()
    {
        $this->formStore = Store::get('Form');
        $this->contactStore = Store::get('Contact');
        $this->submissionStore = Store::get('Submission');
    }

    public function renderNow()
    {
        $formId = $this->getContent('id', null);

        if (empty($formId)) {
            return;
        }

        $this->formModel = $this->formStore->getById($formId);
        $this->form = self::createForm($this->formModel);
        $this->view->form = $this->form->render();
    }

    /**
     * @param \Octo\Forms\Model\Form $formModel
     * @return \Octo\Form
     */
    public static function createForm(FormModel $formModel) : \Octo\Form
    {
        $form = new FormElement();
        $form->setClass('octo-forms-form');
        $form->setAction('/form/submit');
        $form->setMethod('POST');

        $formId = b8\Form\Element\Hidden::create('form_id', 'Form ID');
        $formId->setValue($formModel->getId());
        $form->addField($formId);

        foreach ($formModel->getDefinition() as $field) {
            $type = str_replace('_', ' ', $field['type']);
            $type = ucwords($type);
            $type = str_replace(' ', '', $type);
            $class = FormElement::getFieldClass($type);

            if (!is_null($class)) {
                $thisField = new $class($field['id']);
                $thisField->setLabel($field['label']);
                $thisField->setRequired($field['required']);

                if (array_key_exists('options', $field) && is_array($field['options'])) {
                    $thisField->setOptions($field['options']);
                }

                if (method_exists($thisField, 'setCheckedValue')) {
                    $thisField->setCheckedValue('Checked');
                }

                $form->addField($thisField);
            } else {
                print 'Type not found:  ' . $type . '<br>';
            }
        }

        $form->addField(new Submit());

        return $form;
    }
}
