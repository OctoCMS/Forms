<?php

namespace Octo\Forms\Controller;

use Octo\Controller;
use Octo\Event;
use Octo\Forms\Model\Submission;
use Octo\Forms\Store\SubmissionStore;
use Octo\Job\Manager;
use Octo\Store;
use Octo\Forms\Block\Form as FormBlock;
use Octo\Form as OctoForm;
use Octo\Forms\Model\Form as FormModel;
use Octo\System\Model\Contact;
use Octo\System\Model\Job;
use Octo\System\Store\ContactStore;
use Octo\Template;


class FormController extends Controller
{
    public function submit()
    {
        // ----
        // Firstly, attempt to load the form:
        // ----

        $formId = $this->getParam('form_id', null);
        
        if (empty($formId)) {

            return $this->error('There was a technical problem submitting the form. Please try again later. (Code: FORM_I)');
        }

        /** @var \Octo\Forms\Model\Form $formModel */
        $formModel = Store::get('Form')->getById((int)$formId);

        if (empty($formModel)) {
            return $this->error('There was a technical problem submitting the form. Please try again later. (Code: FORM_M)');
        }

        // ----
        // Next, validate the submission:
        // ----
        
        $form = \Octo\Forms\Block\Form::createForm($formModel);
        
        list($valid, $errors) = $this->validate($form);

        if (!$valid) {
            return $this->error('Please review the highlighted fields to continue.', $errors, 400);
        }
        
        // ----
        // Process the form submission:
        // ----

        try {
            $this->processForm($form, $formModel, $form->getValues());
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
            return $this->error('There was a technical problem submitting the form. Please try again later. (Code: FORM_EX)');
        }

        // ----
        // If we've got this far, great success!
        // ----
        
        return $this->success($formModel->getThankyouMessage());
    }

    protected function error($message, $fields = [], $code = 500)
    {
        if ($this->request->isAjax()) {
            return $this->json([
                'success' => false,
                'message' => $message,
                'fields' => $fields,
            ])->setResponseCode($code);
        }

        return $this->redirect($_SERVER['HTTP_REFERER'] . '?f=0');
    }

    protected function success($message)
    {
        if ($this->request->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => $message,
            ])->setResponseCode(200);
        }

        return $this->redirect($_SERVER['HTTP_REFERER'] . '?f=1');
    }

    /**
     * @param \Octo\Form $form
     * @return array
     */
    protected function validate(\Octo\Form $form)
    {
        $form->setValues($this->getParams());

        $errors = [];
        if (!$form->validate($errors)) {
            return [false, $errors];
        }

        return [true, null];
    }

    protected function processForm(OctoForm $form, FormModel $formModel, array $values = [])
    {
        unset($values['form_id']);
        
        /** @var ContactStore $contactStore */
        $contactStore = Store::get('Contact');

        /** @var SubmissionStore $submissionStore */
        $submissionStore = Store::get('Submission');

        $contactDetails = $this->getContactDetails($values);
        $contact = $contactStore->findContact($contactDetails);

        if (is_null($contact)) {
            $contact = new Contact();
            $contact->setAddress([]);
        }

        if ($contact->getIsBlocked()) {
            return true;
        }

        $contact->setValues($contactDetails);
        $contact = $contactStore->save($contact);


        $submission = new Submission();
        $submission->setForm($formModel);
        $submission->setCreatedDate(new \DateTime());
        $submission->setContact($contact);

        if (array_key_exists('message', $values)) {
            $submission->setMessage(nl2br(strip_tags($values['message'])));
            unset($values['message']);
        }

        $extra = [];
        foreach ($values as $key => $value) {
            $extra[$key] = $value;
        }

        $attachments = [];
        foreach ($form->getChildren() as $field) {
            if ($field instanceof OctoForm\Element\FileUpload) {
                $attachments[$field->getUploadedName()] = $field->getUploadedPath();
            }
        }

        $submission->setExtra($extra);
        $submission = $submissionStore->save($submission);

        $params = [
            'form' => $formModel,
            'submission' => $submission,
        ];

        Event::trigger('Octo.Forms.Submit', $params);
        $this->sendEmails($formModel, $submission, $attachments);

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
        // Render the submission email template:
        $message = new Template('Emails/FormSubmission');
        $message->form = $form;
        $message->submission = $submission;
        $body = $message->render();

        // Create the basic email object:
        $email = [
            'subject' => 'Form Submission: ' . $form->getTitle(),
            'body' => $body,
            'html' => true,
            'attachments' => $attachments,
        ];

        // Set the reply to if possible:
        $contact = $submission->getContact();

        if ($contact && $contact->getEmail()) {
            $name = trim($contact->getFirstName() . ' ' . $contact->getLastName());
            $email['reply_to'] = ['email' => $contact->getEmail(), 'name' => $name];
        }

        // Send a copy of the email to each recipient listed in the form definition:
        $recipients = array_filter(explode("\n", $form->getRecipients()));
        foreach ($recipients as $recipient) {
            $email['to'] = [['email' => $recipient, 'name' => '']];

            $job = new Job();
            $job->setType('Octo.System.SendEmail');
            $job->setData($email);

            Manager::create($job, Job::PRIORITY_HIGH);
        }
    }
}