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
        $this->form = $this->createForm($this->formModel->getDefinition());

        if ($this->request->getMethod() == 'POST') {
            if ($this->processForm($this->formModel, $this->form)) {
                $this->view->thankyou = $this->formModel->getThankyouMessage();
                return;
            } else {
                $this->view->error = 'There was an error with your submission, please check for errors below.';
            }
        }

        $this->view->form = $this->form->render();
    }

    protected function createForm($definition)
    {
        $form = new FormElement();
        $form->setAction($this->request->getPath());
        $form->setMethod('POST');

        foreach ($definition as $field) {
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

    protected function processForm(FormModel $formModel, FormElement &$form)
    {
        $form->setValues($this->request->getParams());
        if (!$form->validate()) {
            return false;
        }

        try {
            $values = $form->getValues();
            $contactDetails = $this->getContactDetails($values);
            $contact = $this->contactStore->findContact($contactDetails);

            if (is_null($contact)) {
                $contact = new Contact();
            }

            if ($contact->getIsBlocked()) {
                return true;
            }

            $contact->setValues($contactDetails);
            $contact = $this->contactStore->save($contact);


            $submission = new Submission();
            $submission->setForm($formModel);
            $submission->setCreatedDate(new \DateTime());
            $submission->setContact($contact);

            if (array_key_exists('message', $values)) {
                $submission->setMessage(nl2br($values['message']));
                unset($values['message']);
            }

            $extra = [];
            foreach ($values as $key => $value) {
                $extra[$key] = $value;
            }

            $attachments = [];
            foreach ($form->getChildren() as $field) {
                if ($field instanceof \Octo\Form\Element\FileUpload) {
                    $attachments[$field->getUploadedName()] = $field->getUploadedPath();
                }
            }

            $submission->setExtra($extra);
            $submission = $this->submissionStore->save($submission);
            $params = array('formModel'=>$formModel, 'submission'=>$submission);
            Event::trigger('formsSubmit', $params);
            $this->sendEmails($formModel, $submission, $attachments);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    protected function getContactDetails(&$values)
    {
        $contact = [
            'email' => array_key_exists('email', $values) ? $values['email'] : null,
            'phone' => array_key_exists('phone', $values) ? $values['phone'] : null,
            'title' => array_key_exists('title', $values) ? $values['title'] : null,
            'gender' => array_key_exists('gender', $values) ? $values['gender'] : null,
            'first_name' => array_key_exists('name', $values) ? $values['name']['first_name'] : null,
            'last_name' => array_key_exists('name', $values) ? $values['name']['last_name'] : null,
            'address' => array_key_exists('address', $values) ? $values['address'] : null,
            'postcode' => array_key_exists('postcode', $values) ? $values['postcode'] : null,
            'date_of_birth' => array_key_exists('date_of_birth', $values) ? $values['date_of_birth'] : null,
            'company' => array_key_exists('company', $values) ? $values['company'] : null,
            'marketing_optin' => array_key_exists('marketing_optin', $values) ? $values['marketing_optin'] : 0,
        ];

        unset($values['email']);
        unset($values['phone']);
        unset($values['title']);
        unset($values['gender']);
        unset($values['name']);
        unset($values['address']);
        unset($values['postcode']);
        unset($values['date_of_birth']);
        unset($values['company']);
        unset($values['marketing_optin']);

        return array_filter($contact);
    }

    protected function sendEmails(FormModel $form, Submission $submission, array $attachments = [])
    {
        $config = Config::getInstance();
        $mail = new \PHPMailer();

        if (isset($config->site['smtp_server'])) {
            $mail->IsSMTP();
            $mail->Host = $config->site['smtp_server'];
        }

        $mail->IsHTML(true);
        $mail->Subject = 'Form Submission: ' . $form->getTitle();
        $mail->CharSet = "UTF-8";

        $recipients = array_filter(explode("\n", $form->getRecipients()));
        foreach ($recipients as $recipient) {
            $mail->AddAddress($recipient);
        }

        if ($submission->getContact() && $submission->getContact()->getEmail()) {
            $name = $submission->getContact()->getFirstName() . ' ' . $submission->getContact()->getLastName();
            $mail->AddReplyTo($submission->getContact()->getEmail(), $name);
        }

        if (isset($config->site['email_from'])) {
            $mail->SetFrom($config->site['email_from'], $config->site['email_from_name']);
        } else {
            $mail->SetFrom('octo@block8.net', 'Octo');
        }

        foreach ($attachments as $name => $path) {
            $mail->addAttachment($path, $name);
        }

        $message         = new Template('Emails/FormSubmission');
        $message->form   = $form;
        $message->submission = $submission;
        $body = $message->render();

        $mail->Body = $body;
        $mail->send();
    }
}
