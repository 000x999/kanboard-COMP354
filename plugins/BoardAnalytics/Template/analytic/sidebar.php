<?php
/*
 * Board Analytics overrides the core "analytic/sidebar" template so that our
 * dashboard link appears FIRST in the Analytics sidebar. The remaining entries
 * are Kanboard's built-in analytics, kept in their original order.
 *
 * NOTE for maintainers: because this is a full override, if a future Kanboard
 * release adds a new built-in analytics page, add the matching <li> here too.
 */
?>
<div class="sidebar">
    <ul>
        <li class="board-analytics-home" <?= $this->app->checkMenuSelection('BoardAnalyticsController', 'show') ?>>
            <?= $this->url->link(t('Board Analytics'), 'BoardAnalyticsController', 'show', array('plugin' => 'BoardAnalytics', 'project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'averageTimeByColumn') ?>>
            <?= $this->modal->replaceLink(t('Average Time by Column'), 'AnalyticController', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'burndown') ?>>
            <?= $this->modal->replaceLink(t('Burndown Chart'), 'AnalyticController', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'cfd') ?>>
            <?= $this->modal->replaceLink(t('Cumulative Flow Diagram'), 'AnalyticController', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'timeComparison') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs Actual Time'), 'AnalyticController', 'timeComparison', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'estimatedVsActualByColumn') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs Actual Time (by Column)'), 'AnalyticController', 'estimatedVsActualByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'leadAndCycleTime') ?>>
            <?= $this->modal->replaceLink(t('Lead and Cycle time'), 'AnalyticController', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'taskDistribution') ?>>
            <?= $this->modal->replaceLink(t('Task Distribution'), 'AnalyticController', 'taskDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li class="sidebar-analytics" <?= $this->app->checkMenuSelection('AnalyticController', 'userDistribution') ?>>
            <?= $this->modal->replaceLink(t('Tasks by User'), 'AnalyticController', 'userDistribution', array('project_id' => $project['id'])) ?>
        </li>

        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>
    </ul>
</div>
