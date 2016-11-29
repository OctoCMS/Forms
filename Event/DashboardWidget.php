<?php

namespace Octo\Forms\Event;

use Octo\Template;
use Octo\Event\Listener;
use Octo\Event\Manager;
use Octo\Store;
use Octo\System\Model\Setting;

class DashboardWidget extends Listener
{
    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('DashboardWidgets', array($this, 'getWidget'));
        $manager->registerListener('DashboardStatistics', array($this, 'getStatistics'));
    }

    public function getStatistics(&$stats)
    {
        $submissionStore = Store::get('Submission');
        $total = $submissionStore->find()->count();

        if ($total) {
            $stats[] = [
                'title' => 'Form Submissions',
                'count' => number_format($total),
                'icon' => 'email',
                'color' => 'green',
                'link' => '/form',
            ];
        }
    }

    public function getWidget(&$widgets)
    {
        $submissionStore = Store::get('Submission');

        $view = new Template("Dashboard/latest-form-submissions", "admin");
        $view->latestSubmissions = $submissionStore->find()->order('id', 'DESC')->get(5);

        $widgets[] = ['order' => 10, 'html' => $view->render()];
    }
}
