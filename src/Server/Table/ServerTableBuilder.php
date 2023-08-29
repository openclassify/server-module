<?php namespace Visiosoft\ServerModule\Server\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

class ServerTableBuilder extends TableBuilder
{

    /**
     * The table views.
     *
     * @var array|string
     */
    protected $views = [];

    /**
     * The table filters.
     *
     * @var array|string
     */
    protected $filters = [];

    /**
     * The table columns.
     *
     * @var array|string
     */
    protected $columns = [
        'name',
        'ip'
    ];

    /**
     * The table actions.
     *
     * @var array|string
     */
    protected $actions = [
    ];

    /**
     * The table options.
     *
     * @var array
     */
    protected $options = [
        'sortable' => false,
    ];

    /**
     * The table assets.
     *
     * @var array
     */
    protected $assets = [];

}
