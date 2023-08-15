<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class VisiosoftModuleServerCreateServerStream extends Migration
{

    /**
     * This migration creates the stream.
     * It should be deleted on rollback.
     *
     * @var bool
     */
    protected $delete = false;

    /**
     * The stream definition.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'server',
        'title_column' => 'ip',
        'translatable' => false,
        'versionable' => true,
        'trashable' => true, // for soft delete
        'searchable' => true, // for index
        'sortable' => true,
    ];

    /**
     * @var array
     */
    protected $fields = [
        'server_id' => 'anomaly.field_type.text',
        'name' => 'anomaly.field_type.text',
        'ip' => 'anomaly.field_type.text',
        'password' => 'anomaly.field_type.text',
        'database' => 'anomaly.field_type.text',
        'provider' => 'anomaly.field_type.text',
        'location' => 'anomaly.field_type.text',
        'php' => [
            'type' => 'anomaly.field_type.select',
            'config' => [
                'handler' => \Visiosoft\ServerModule\Handler\PhpVersions::class
            ],
        ],
        'github_key' => 'anomaly.field_type.textarea',
        'cron' => 'anomaly.field_type.textarea',
        'default' => [
            'type' => 'anomaly.field_type.boolean',
            'config' => [
                'default_value' => false
            ],
        ],
        'build' => 'anomaly.field_type.integer',
        'status' => [
            'type' => 'anomaly.field_type.integer',
            'config' => [
                'default_value' => 0
            ],
        ],
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        'server_id' => [
            'required' => true,
            'unique' => true
        ],
        'ip' => [
            'required' => true,
        ],
        'name' => [
            'required' => true,
        ],
        'password' => [
            'required' => true,
        ],
        'database' => [
            'required' => true,
        ],
        'provider',
        'location',
        'php' => [
            'required' => true,
        ],
        'github_key',
        'cron',
        'default',
        'build',
        'status',
    ];

}
