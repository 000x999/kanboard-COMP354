<div class="board-analytics">

    <?php if (! $is_ajax): ?>
        <div class="page-header">
            <h2><?= t('Board Analytics') ?></h2>
        </div>
    <?php endif ?>

    <!-- ============================================================= -->
    <!-- 1. Task distribution (donut) - reuses Kanboard's own chart    -->
    <!-- ============================================================= -->
    <div class="ba-section">
        <h3><?= t('Task distribution') ?></h3>

        <?php if (empty($task_distribution)): ?>
            <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
        <?php else: ?>
            <?= $this->app->component('chart-project-task-distribution', array(
                'metrics' => $task_distribution,
            )) ?>

            <table class="table-striped">
                <tr>
                    <th><?= t('Column') ?></th>
                    <th><?= t('Number of tasks') ?></th>
                    <th><?= t('Percentage') ?></th>
                </tr>
                <?php foreach ($task_distribution as $metric): ?>
                    <tr>
                        <td><?= $this->text->e($metric['column_title']) ?></td>
                        <td><?= (int) $metric['nb_tasks'] ?></td>
                        <td><?= n($metric['percentage']) ?>%</td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </div>

    <!-- ============================================================= -->
    <!-- 2. Tasks per assignee                                         -->
    <!-- ============================================================= -->
    <div class="ba-section">
        <h3><?= t('Tasks per assignee') ?></h3>

        <?php if (empty($tasks_per_assignee)): ?>
            <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
        <?php else: ?>
            <div class="ba-chart">
                <?php foreach ($tasks_per_assignee as $row): ?>
                    <div class="ba-bar-row">
                        <div class="ba-bar-label" title="<?= $this->text->e($row['user']) ?>"><?= $this->text->e($row['user']) ?></div>
                        <div class="ba-bar-track">
                            <div class="ba-bar-fill ba-bar-assignee" style="width: <?= $row['percentage'] ?>%;"></div>
                        </div>
                        <div class="ba-bar-value"><?= (int) $row['nb_tasks'] ?></div>
                    </div>
                <?php endforeach ?>
            </div>

            <table class="table-striped">
                <tr>
                    <th><?= t('User') ?></th>
                    <th><?= t('Number of tasks') ?></th>
                    <th><?= t('Percentage') ?></th>
                </tr>
                <?php foreach ($tasks_per_assignee as $row): ?>
                    <tr>
                        <td><?= $this->text->e($row['user']) ?></td>
                        <td><?= (int) $row['nb_tasks'] ?></td>
                        <td><?= n($row['percentage']) ?>%</td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </div>

    <!-- ============================================================= -->
    <!-- 3. Tasks completed over the last few weeks                    -->
    <!-- ============================================================= -->
    <div class="ba-section">
        <h3><?= t('Tasks completed over the last %d weeks', $weeks) ?></h3>

        <?php
            $max_completed = 0;
            foreach ($completed_per_week as $row) {
                $max_completed = max($max_completed, (int) $row['nb_tasks']);
            }
        ?>

        <?php if ($max_completed === 0): ?>
            <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
        <?php else: ?>
            <div class="ba-columns">
                <?php foreach ($completed_per_week as $row): ?>
                    <?php $height = $max_completed > 0 ? round(((int) $row['nb_tasks'] / $max_completed) * 100) : 0; ?>
                    <div class="ba-col">
                        <div class="ba-col-count"><?= (int) $row['nb_tasks'] ?></div>
                        <div class="ba-col-bar-track">
                            <div class="ba-col-bar-fill" style="height: <?= $height ?>%;"></div>
                        </div>
                        <div class="ba-col-label"><?= $this->text->e($row['label']) ?></div>
                    </div>
                <?php endforeach ?>
            </div>

            <table class="table-striped">
                <tr>
                    <th><?= t('Week of') ?></th>
                    <th><?= t('Tasks completed') ?></th>
                </tr>
                <?php foreach ($completed_per_week as $row): ?>
                    <tr>
                        <td><?= $this->text->e($row['label']) ?></td>
                        <td><?= (int) $row['nb_tasks'] ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </div>

    <!-- 4. Active-task due-date health -->
    <div class="ba-section">
        <h3><?= t('Active-task due-date health') ?></h3>

        <div class="ba-chart">
            <?php foreach ($due_date_status as $row): ?>
                <div class="ba-bar-row">
                    <div class="ba-bar-label"><?= t($row['label']) ?></div>
                    <div class="ba-bar-track">
                        <div class="ba-bar-fill ba-bar-assignee" style="width: <?= $row['percentage'] ?>%;"></div>
                    </div>
                    <div class="ba-bar-value"><?= (int) $row['nb_tasks'] ?></div>
                </div>
            <?php endforeach ?>
        </div>

        <table class="table-striped">
            <tr>
                <th><?= t('Due-date status') ?></th>
                <th><?= t('Number of tasks') ?></th>
                <th><?= t('Percentage') ?></th>
            </tr>
            <?php foreach ($due_date_status as $row): ?>
                <tr>
                    <td><?= t($row['label']) ?></td>
                    <td><?= (int) $row['nb_tasks'] ?></td>
                    <td><?= n($row['percentage']) ?>%</td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>
