<?php namespace Visiosoft\ServerModule;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Visiosoft\ServerModule\Console\ServerSetupCheck;
use Visiosoft\ServerModule\Console\UpdateServerCredentials;
use Visiosoft\ServerModule\Http\Controller\ApiController;
use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;
use Visiosoft\ServerModule\Server\ServerRepository;
use Anomaly\Streams\Platform\Model\Server\ServerServerEntryModel;
use Visiosoft\ServerModule\Server\ServerModel;
use Illuminate\Routing\Router;

class ServerModuleServiceProvider extends AddonServiceProvider
{

    /**
     * Additional addon plugins.
     *
     * @type array|null
     */
    protected $plugins = [];

    /**
     * The addon Artisan commands.
     *
     * @type array|null
     */
    protected $commands = [
        ServerSetupCheck::class,
        UpdateServerCredentials::class,
    ];

    /**
     * The addon's scheduled commands.
     *
     * @type array|null
     */
    protected $schedules = [
        '* * * * *' => [
            'servers:setupcheck'
        ]
    ];

    /**
     * The addon routes.
     *
     * @type array|null
     */
    protected $routes = [
        'admin/server' => 'Visiosoft\ServerModule\Http\Controller\Admin\ServerController@index',
        'admin/server/create' => 'Visiosoft\ServerModule\Http\Controller\Admin\ServerController@create',
        'admin/server/edit/{id}' => 'Visiosoft\ServerModule\Http\Controller\Admin\ServerController@edit',
        'sh/setup/{id}' => 'Visiosoft\ServerModule\Http\Controller\ServerController@setup',
        'admin/server/installation/{id}' => 'Visiosoft\ServerModule\Http\Controller\Admin\ServerController@installation',
        'admin/server/manage/{server_id}' => 'Visiosoft\ServerModule\Http\Controller\Admin\ServerController@manage',
    ];

    /**
     * The addon middleware.
     *
     * @type array|null
     */
    protected $middleware = [
        //Visiosoft\ServerModule\Http\Middleware\ExampleMiddleware::class
    ];

    /**
     * Addon group middleware.
     *
     * @var array
     */
    protected $groupMiddleware = [
        //'web' => [
        //    Visiosoft\ServerModule\Http\Middleware\ExampleMiddleware::class,
        //],
    ];

    /**
     * Addon route middleware.
     *
     * @type array|null
     */
    protected $routeMiddleware = [];

    /**
     * The addon event listeners.
     *
     * @type array|null
     */
    protected $listeners = [
        //Visiosoft\ServerModule\Event\ExampleEvent::class => [
        //    Visiosoft\ServerModule\Listener\ExampleListener::class,
        //],
    ];

    /**
     * The addon alias bindings.
     *
     * @type array|null
     */
    protected $aliases = [
        //'Example' => Visiosoft\ServerModule\Example::class
    ];

    /**
     * The addon class bindings.
     *
     * @type array|null
     */
    protected $bindings = [
        ServerServerEntryModel::class => ServerModel::class,
    ];

    /**
     * The addon singleton bindings.
     *
     * @type array|null
     */
    protected $singletons = [
        ServerRepositoryInterface::class => ServerRepository::class,
    ];

    /**
     * Additional service providers.
     *
     * @type array|null
     */
    protected $providers = [
        //\ExamplePackage\Provider\ExampleProvider::class
    ];

    /**
     * The addon view overrides.
     *
     * @type array|null
     */
    protected $overrides = [
        //'streams::errors/404' => 'module::errors/404',
        //'streams::errors/500' => 'module::errors/500',
    ];

    /**
     * The addon mobile-only view overrides.
     *
     * @type array|null
     */
    protected $mobile = [
        //'streams::errors/404' => 'module::mobile/errors/404',
        //'streams::errors/500' => 'module::mobile/errors/500',
    ];

    /**
     * Register the addon.
     */
    public function register()
    {
        // Run extra pre-boot registration logic here.
        // Use method injection or commands to bring in services.
    }

    /**
     * Boot the addon.
     */
    public function boot()
    {
        // Run extra post-boot registration logic here.
        // Use method injection or commands to bring in services.
    }

    /**
     * Map additional addon routes.
     *
     * @param Router $router
     */
    public function map(Router $router)
    {
        $this->mapRouters($router);
    }

    public function mapRouters(Router $router)
    {
        //Todo: Update Api for Server Manage Screen

        $router->group(['prefix' => 'api/servers', 'middleware' => ['apikey']], function () use ($router) {
            $router->get('/', [ApiController::class, 'index']);
//            $router->post('/', [ApiController::class, 'create']);
//            $router->get('/panel', [ApiController::class, 'panel']);
//            $router->patch('/panel/domain', [ApiController::class, 'paneldomain']);
//            $router->post('/panel/ssl', [ApiController::class, 'panelssl']);
//            $router->delete('/{server_id}', [ApiController::class, 'destroy']);
//            $router->get('/{server_id}', [ApiController::class, 'show']);
//            $router->patch('/{server_id}', [ApiController::class, 'edit']);
//            $router->get('/{server_id}/ping', [ApiController::class, 'ping']);
//            $router->get('/{server_id}/healthy', [ApiController::class, 'healthy']);
//            $router->post('/{server_id}/rootreset', [ApiController::class, 'rootreset']);
//            $router->post('/{server_id}/servicerestart/{service}', [ApiController::class, 'servicerestart']);
//            $router->get('/{server_id}/sites', [ApiController::class, 'sites']);
//            $router->get('/{server_id}/domains', [ApiController::class, 'domains']);
        });
    }
}
