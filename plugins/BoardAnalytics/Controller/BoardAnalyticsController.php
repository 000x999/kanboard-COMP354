<?php

namespace Kanboard\Plugin\BoardAnalytics\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Plugin\BoardAnalytics\Model\BoardAnalyticsModel;

/**
 * Board Analytics Controller
 *
 * Serves the per-project analytics dashboard. Access control (the user must be
 * a member of the project) is inherited from BaseController::getProject().
 *
 * @package Kanboard\Plugin\BoardAnalytics\Controller
 */
class BoardAnalyticsController extends BaseController
{
    /**
     * Render the dashboard for the requested project.
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        $model = new BoardAnalyticsModel($this->container);

        // Allow ?weeks=N (clamped to a sane range); default to 8 weeks.
        $weeks = (int) $this->request->getIntegerParam('weeks', 8);
        $weeks = max(4, min(26, $weeks));

        $metrics = $model->build($project['id'], $weeks);

        $this->response->html($this->helper->layout->analytic('BoardAnalytics:dashboard/show', array(
            'project'            => $project,
            'weeks'              => $weeks,
            // Reuse Kanboard's own Task Distribution report (donut chart data).
            'task_distribution'  => $this->taskDistributionAnalytic->build($project['id']),
            'tasks_per_assignee' => $metrics['tasks_per_assignee'],
            'completed_per_week' => $metrics['completed_per_week'],
            'due_date_status'    => $metrics['due_date_status'],
            'title'              => t('Board Analytics'),
        )));
    }
}
