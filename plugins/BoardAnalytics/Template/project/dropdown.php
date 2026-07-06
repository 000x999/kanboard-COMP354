<li>
    <i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>
    <?= $this->url->link(t('Board Analytics'), 'BoardAnalyticsController', 'show', array('plugin' => 'BoardAnalytics', 'project_id' => $project['id'])) ?>
</li>
