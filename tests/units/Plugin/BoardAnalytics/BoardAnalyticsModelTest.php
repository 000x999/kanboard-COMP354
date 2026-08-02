<?php

namespace KanboardTests\units\Plugin\BoardAnalytics;

use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Plugin\BoardAnalytics\Model\BoardAnalyticsModel;
use KanboardTests\units\Base;

require_once __DIR__.'/../../../../plugins/BoardAnalytics/Model/BoardAnalyticsModel.php';

class BoardAnalyticsModelTest extends Base
{
    public function testDueDateStatusAndBuildExposure()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $model = new BoardAnalyticsModel($this->container);
        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project 1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project 2')));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Overdue', 'date_due' => $now - 86400)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Due tomorrow', 'date_due' => $now + 86400)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Due next week', 'date_due' => $now + (7 * 86400))));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Due later', 'date_due' => $now + (8 * 86400))));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'No due date')));

        // Ignore closed tasks and tasks from other projects.
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'Closed', 'date_due' => $now - 86400, 'is_active' => 0)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 2, 'title' => 'Other project', 'date_due' => $now - 86400)));

        $expected = array(
            array('label' => 'Overdue', 'nb_tasks' => 1, 'percentage' => 20.0),
            array('label' => 'Due within 7 days', 'nb_tasks' => 2, 'percentage' => 40.0),
            array('label' => 'Due later', 'nb_tasks' => 1, 'percentage' => 20.0),
            array('label' => 'No due date', 'nb_tasks' => 1, 'percentage' => 20.0),
        );

        $this->assertSame($expected, $model->getDueDateStatus(1));
        $this->assertSame($expected, $model->build(1)['due_date_status']);
    }

    public function testDueDateStatusWithNoTasks()
    {
        $projectModel = new ProjectModel($this->container);
        $model = new BoardAnalyticsModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Empty project')));

        $this->assertSame(array(
            array('label' => 'Overdue', 'nb_tasks' => 0, 'percentage' => 0.0),
            array('label' => 'Due within 7 days', 'nb_tasks' => 0, 'percentage' => 0.0),
            array('label' => 'Due later', 'nb_tasks' => 0, 'percentage' => 0.0),
            array('label' => 'No due date', 'nb_tasks' => 0, 'percentage' => 0.0),
        ), $model->getDueDateStatus(1));
    }
}
