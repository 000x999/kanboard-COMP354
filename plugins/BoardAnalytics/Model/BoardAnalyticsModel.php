<?php

namespace Kanboard\Plugin\BoardAnalytics\Model;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;

/**
 * Board Analytics Model
 *
 * Computes the four metrics shown on the dashboard for a single project.
 * All numbers are derived from Kanboard's own "tasks" and "columns" tables,
 * so the plugin stays in sync with the board automatically and needs no
 * schema of its own.
 *
 * @package Kanboard\Plugin\BoardAnalytics\Model
 */
class BoardAnalyticsModel extends Base
{
    /**
     * Number of active tasks per assignee (unassigned tasks are bucketed
     * under "Unassigned"). Sorted from most to fewest tasks.
     *
     * @param  integer $project_id
     * @return array   List of ['user' => string, 'nb_tasks' => int, 'percentage' => float]
     */
    public function getTasksPerAssignee($project_id)
    {
        $tasks = $this->taskFinderModel->getAll($project_id);
        $users = $this->projectUserRoleModel->getAssignableUsersList($project_id);

        $metrics = array();
        $total = 0;

        foreach ($tasks as $task) {
            $name = isset($users[$task['owner_id']]) ? $users[$task['owner_id']] : $users[0];

            if (! isset($metrics[$name])) {
                $metrics[$name] = 0;
            }

            $metrics[$name]++;
            $total++;
        }

        arsort($metrics);

        $result = array();

        foreach ($metrics as $name => $count) {
            $result[] = array(
                'user'       => $name,
                'nb_tasks'   => $count,
                'percentage' => $total > 0 ? round(($count * 100) / $total, 1) : 0.0,
            );
        }

        return $result;
    }

    // Group active tasks by due-date status.
    public function getDueDateStatus($project_id)
    {
        $tasks = $this->taskFinderModel->getAll($project_id);
        $now = time();
        $soon = $now + (7 * 86400);
        $buckets = array(
            'overdue' => array('label' => 'Overdue', 'nb_tasks' => 0),
            'due_soon' => array('label' => 'Due within 7 days', 'nb_tasks' => 0),
            'due_later' => array('label' => 'Due later', 'nb_tasks' => 0),
            'no_due_date' => array('label' => 'No due date', 'nb_tasks' => 0),
        );

        foreach ($tasks as $task) {
            $date_due = (int) $task['date_due'];

            if ($date_due === 0) {
                $bucket = 'no_due_date';
            } elseif ($date_due <= $now) {
                $bucket = 'overdue';
            } elseif ($date_due <= $soon) {
                $bucket = 'due_soon';
            } else {
                $bucket = 'due_later';
            }

            $buckets[$bucket]['nb_tasks']++;
        }

        $total = count($tasks);

        foreach ($buckets as &$bucket) {
            $bucket['percentage'] = $total > 0 ? round(($bucket['nb_tasks'] * 100) / $total, 1) : 0.0;
        }

        unset($bucket);

        return array_values($buckets);
    }

    /**
     * Number of tasks completed per week over the last $weeks weeks.
     *
     * Grouping is done in PHP (not with database-specific date functions) so
     * the query stays portable across SQLite, MySQL and PostgreSQL. Weeks with
     * no completed task are returned as zero so the series is continuous.
     *
     * @param  integer $project_id
     * @param  integer $weeks       How many trailing weeks to report (default 10)
     * @return array   List of ['week' => 'YYYY-MM-DD', 'label' => string, 'nb_tasks' => int]
     */
    public function getTasksCompletedPerWeek($project_id, $weeks = 10)
    {
        // Start of the current week (Monday, 00:00:00 local time).
        $current_monday = strtotime('monday this week');
        $window_start = $current_monday - (($weeks - 1) * 7 * 86400);

        // Pre-seed every week bucket with zero.
        $buckets = array();
        for ($i = 0; $i < $weeks; $i++) {
            $ts = $window_start + ($i * 7 * 86400);
            $key = date('Y-m-d', $ts);
            $buckets[$key] = array(
                'week'     => $key,
                'label'    => date('M j', $ts),
                'nb_tasks' => 0,
            );
        }

        // Closed tasks completed on/after the window start.
        $tasks = $this->db->table(TaskModel::TABLE)
            ->eq('project_id', $project_id)
            ->eq('is_active', TaskModel::STATUS_CLOSED)
            ->gte('date_completed', $window_start)
            ->columns('date_completed')
            ->findAll();

        foreach ($tasks as $task) {
            $completed = (int) $task['date_completed'];

            if ($completed <= 0) {
                continue;
            }

            // Snap the completion date back to its week's Monday.
            $offset_weeks = (int) floor(($completed - $window_start) / (7 * 86400));

            if ($offset_weeks < 0 || $offset_weeks >= $weeks) {
                continue;
            }

            $key = date('Y-m-d', $window_start + ($offset_weeks * 7 * 86400));

            if (isset($buckets[$key])) {
                $buckets[$key]['nb_tasks']++;
            }
        }

        return array_values($buckets);
    }

    /**
     * Convenience: build the plugin-specific metrics in one call.
     *
     * (Task distribution per column is provided directly by Kanboard's own
     * TaskDistributionAnalytic, so it is not duplicated here.)
     *
     * @param  integer $project_id
     * @param  integer $weeks
     * @return array
     */
    public function build($project_id, $weeks = 10)
    {
        return array(
            'tasks_per_assignee' => $this->getTasksPerAssignee($project_id),
            'completed_per_week' => $this->getTasksCompletedPerWeek($project_id, $weeks),
            'due_date_status'      => $this->getDueDateStatus($project_id),
        );
    }
}
