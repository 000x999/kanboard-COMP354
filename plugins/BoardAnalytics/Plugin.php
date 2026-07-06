<?php

namespace Kanboard\Plugin\BoardAnalytics;

use Kanboard\Core\Plugin\Base;

/**
 * Board Analytics Plugin
 *
 * Adds a self-contained "Board Analytics" dashboard to each project, showing:
 *   - how many tasks are in each column right now,
 *   - how tasks are split between assignees,
 *   - how many tasks were completed over the last few weeks.
 *
 * The plugin lives entirely in the plugins/ folder and does not modify any
 * Kanboard core file. It integrates through the public route, template hook
 * and asset hook APIs documented in the Kanboard developer guide.
 *
 * COMP 354 - Team 65711
 *
 * @package Kanboard\Plugin\BoardAnalytics
 */
class Plugin extends Base
{
    /**
     * Called on every request: register the route, menu links and stylesheet.
     */
    public function initialize()
    {
        // Pretty URL for the dashboard: /board-analytics/<project_id>
        $this->route->addRoute(
            '/board-analytics/:project_id',
            'BoardAnalyticsController',
            'show',
            'BoardAnalytics'
        );

        // Put our dashboard first in the Analytics sidebar by overriding that
        // sidebar template (the sidebar hook only appends to the bottom).
        $this->template->setTemplateOverride('analytic/sidebar', 'BoardAnalytics:analytic/sidebar');

        // ...and inside the project drop-down menu, so it is easy to find.
        $this->template->hook->attach('template:project:dropdown', 'BoardAnalytics:project/dropdown');

        // Load the plugin stylesheet (relative to the application root).
        $this->template->hook->attach(
            'template:layout:css',
            'plugins/BoardAnalytics/Assets/css/board-analytics.css'
        );
    }

    public function getPluginName()
    {
        return 'Board Analytics';
    }

    public function getPluginDescription()
    {
        return t('A simple per-project analytics dashboard: tasks per column, tasks per assignee, and tasks completed over the last few weeks.');
    }

    public function getPluginAuthor()
    {
        return 'COMP 354 - Team 65711';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/kanboard';
    }

    public function getCompatibleVersion()
    {
        // Works with any reasonably recent Kanboard release.
        return '>=1.2.0';
    }
}
