<?php
namespace Octo\Forms\Admin\Controller;

use Octo\Admin\Controller;
use Octo\Admin\Menu;
use Octo\Forms\Model\Form;
use Octo\Store;

/**
 * Class FormController
 */
class FormController extends Controller
{
    /**
     * @var \Octo\Forms\Store\FormStore
     */
    protected $formStore;

    /**
     * @var \Octo\Forms\Store\SubmissionStore
     */
    protected $submissionStore;

    /**
     * @var \Octo\System\Store\ContactStore
     */
    protected $contactStore;

    public static function registerMenus(Menu $menu)
    {
        $form = $menu->addRoot('Forms', '/form')->setIcon('edit');
        $form->addChild(new Menu\Item('Add Form', '/form/add'));
        $manage = new Menu\Item('Manage Forms', '/form');
        $manage->addChild(new Menu\Item('Edit Form', '/form/edit', true));
        $manage->addChild(new Menu\Item('Delete Form', '/form/delete', true));
        $manage->addChild(new Menu\Item('View Submissions', '/form/submissions', true));
        $manage->addChild(new Menu\Item('View Submission', '/form/submission', true));

        $form->addChild($manage);
    }

    /**
     * Setup initial menu
     *
     * @return void
     */
    public function init()
    {
        $this->formStore = Store::get('Form');
        $this->submissionStore = Store::get('Submission');
        $this->contactStore = Store::get('Contact');

        $this->setTitle('Forms');
        $this->addBreadcrumb('Forms', '/form');
    }

    public function index()
    {
        $forms = $this->formStore->getAll(0, 500);

        if ($this->request->isAjax()) {
            $rtn = [];

            foreach ($forms as $form) {
                $rtn[$form->getId()] = $form->getTitle();
            }

            return $this->json($rtn);
        }

        $this->view->forms = $forms;
    }

    public function add()
    {
        $this->setTitle('Add Form');
        $this->addBreadcrumb('Add Form', '/form/add');

        if ($this->request->getMethod() == 'POST') {
            $form = new Form();
            $form->setValues($this->getParams());
            $this->formStore->save($form);

            return $this->redirect('/form')->success('Form saved successfully.');
        }
    }

    public function edit($formId)
    {
        $form = $this->formStore->getById($formId);

        $this->addBreadcrumb($form->getTitle(), '/form/edit/' . $form->getId());
        $this->setTitle($form->getTitle(), 'Edit Form');

        if ($this->request->getMethod() == 'POST') {
            $form->setValues($this->getParams());
            $this->formStore->save($form);

            return $this->redirect('/form')->success('Form saved successfully.');
        }

        $form = [
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'recipients' => $form->getRecipients(),
            'definition' => htmlspecialchars(json_encode($form->getDefinition())),
            'thankyou_message' => $form->getThankyouMessage(),
        ];

        $this->view->form = $form;
    }

    public function submissions($formId)
    {
        $form = $this->formStore->getById($formId);

        $this->setTitle('Submissions', $form->getTitle());
        $this->addBreadcrumb($form->getTitle(), '/form/edit/' . $formId);
        $this->addBreadcrumb('Submissions', '/form/submissions/' . $formId);

        $submissions = $this->submissionStore->getAllForForm($form, 0, 500);
        $this->view->submissions = $submissions;
        $this->view->form = $form;
    }

    public function submission($submissionId)
    {
        $submission = $this->submissionStore->getById($submissionId);
        $form = $submission->getForm();

        $this->addBreadcrumb($form->getTitle(), '/form/edit/' . $form->getId());
        $this->addBreadcrumb('Submissions', '/form/submissions/' . $form->getId());
        $this->setTitle('View Submission', $form->getTitle());

        $extra = [];

        if ($submission->getExtra()) {
            foreach ($submission->getExtra() as $key => $value) {
                $extra[] = $this->getExtra($form->getDefinition(), $key, $value);
            }
        }

        $this->view->submission = $submission;
        $this->view->extra = $extra;
    }

    protected function getExtra($definition, $key, $value)
    {
        foreach ($definition as $field) {
            if ($field['id'] == $key) {
                $rtn = ['id' => $key, 'label' => $field['label']];

                if (isset($field['options'][$value])) {
                    $rtn['value'] = $field['options'][$value];
                } else {
                    $rtn['value'] = $value;
                }

                return $rtn;
            }
        }

        return ['id' => $key, 'label' => $key, 'value' => $value];
    }

    public function delete($formId)
    {
        $form = $this->formStore->getById($formId);
        $this->formStore->delete($form);

        return $this->redirect('/form')->success($form->getTitle() . ' has been deleted.');
    }
}
