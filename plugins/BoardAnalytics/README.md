# Board Analytics — Kanboard Plugin

**COMP 354 · Summer 2026 · Team 65711**

Adds one **read-only analytics dashboard** to each Kanboard project, with three sections:

1. **Task distribution** — donut of active tasks per column (reuses Kanboard's own chart).
2. **Tasks per assignee** — bar chart of the workload per team member ("Unassigned" bucket for unassigned tasks).
3. **Tasks completed over the last _N_ weeks** — one bar per week (default 8).

Each section shows a chart **and** an exact data table. The plugin adds **no database
tables** and **changes no Kanboard core file**.

## Install

Copy the folder into Kanboard's `plugins/` directory and refresh:

```
kanboard/plugins/BoardAnalytics/
```

No build step or configuration. Remove the folder to uninstall.

## Use

Open a project → **Board Analytics** (first item in the Analytics sidebar, or in the
project drop-down menu). Direct URL: `/board-analytics/<project_id>`.
Add `?weeks=N` (4–26) to change the throughput window.

## Files (for maintainers)

| File | Purpose |
| --- | --- |
| `Plugin.php` | Registers the route, menu links, sidebar override, and stylesheet. |
| `Controller/BoardAnalyticsController.php` | Access check, reads `weeks`, gathers metrics, renders the page. |
| `Model/BoardAnalyticsModel.php` | Two metrics: tasks-per-assignee and completed-per-week. |
| `Template/dashboard/show.php` | The dashboard page (donut + bars + tables). |
| `Template/analytic/sidebar.php` | **Full override** of the Analytics sidebar so our link is first and "Task distribution" is dropped. If Kanboard adds a built-in analytics page, add its `<li>` here too. |
| `Template/project/dropdown.php` | Link in the project drop-down menu. |
| `Assets/css/board-analytics.css` | Chart/table styling (bars are pure CSS). |

## Design notes

- **Task distribution** reuses Kanboard's `TaskDistributionAnalytic` + `c3.js` donut
  instead of re-implementing it — less code, consistent look.
- **Tasks-per-assignee** bars are pure CSS (no chart library needed).
- **Week bucketing** is done in PHP, so the completed-per-week query is portable across
  SQLite / MySQL / PostgreSQL.
- Access control is inherited from `BaseController::getProject()` — only project members
  can view a project's analytics.

## Extending it

- New metric → add a method to `BoardAnalyticsModel`, expose it from `build()`, and add a
  `.ba-section` in `dashboard/show.php`.
- Change the default window → the `$weeks = 8` default in the controller and model.

## License

MIT — see `LICENSE`. Compatible with Kanboard `>= 1.2.0` (verified on v1.2.52).
