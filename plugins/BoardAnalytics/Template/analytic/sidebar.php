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
        <li <?= $this->app->checkMenuSelection('BoardAnalyticsController', 'show') ?>>
            <?= $this->url->link(t('Board Analytics'), 'BoardAnalyticsController', 'show', array('plugin' => 'BoardAnalytics', 'project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'userDistribution') ?>>
            <?= $this->modal->replaceLink(t('User repartition'), 'AnalyticController', 'userDistribution', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'cfd') ?>>
            <?= $this->modal->replaceLink(t('Cumulative flow diagram'), 'AnalyticController', 'cfd', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'burndown') ?>>
            <?= $this->modal->replaceLink(t('Burndown chart'), 'AnalyticController', 'burndown', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'averageTimeByColumn') ?>>
            <?= $this->modal->replaceLink(t('Average time into each column'), 'AnalyticController', 'averageTimeByColumn', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'leadAndCycleTime') ?>>
            <?= $this->modal->replaceLink(t('Lead and cycle time'), 'AnalyticController', 'leadAndCycleTime', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'timeComparison') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs actual time'), 'AnalyticController', 'timeComparison', array('project_id' => $project['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('AnalyticController', 'estimatedVsActualByColumn') ?>>
            <?= $this->modal->replaceLink(t('Estimated vs actual time per column'), 'AnalyticController', 'estimatedVsActualByColumn', array('project_id' => $project['id'])) ?>
        </li>

        <?= $this->hook->render('template:analytic:sidebar', array('project' => $project)) ?>
    </ul>
</div>
