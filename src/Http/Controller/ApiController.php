<?php namespace Visiosoft\ServerModule\Http\Controller;

use Anomaly\Streams\Platform\Http\Controller\ResourceController;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Visiosoft\ServerModule\Jobs\CronSSH;
use Visiosoft\ServerModule\Jobs\PanelDomainAddSSH;
use Visiosoft\ServerModule\Jobs\PanelDomainRemoveSSH;
use Visiosoft\ServerModule\Jobs\PanelDomainSslSSH;
use Visiosoft\ServerModule\Jobs\PhpCliSSH;
use Visiosoft\ServerModule\Jobs\RootResetSSH;
use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;
use Visiosoft\ServerModule\Server\ServerModel;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Visiosoft\SiteModule\Site\SiteModel;

class ApiController extends ResourceController
{
    protected $servers;

    public function __construct(ServerRepositoryInterface $servers)
    {
        $this->servers = $servers;
        parent::__construct();
    }

    public function index()
    {
        try {
            $servers = $this->servers->all();

            $response = [
                'success' => true,
                'data' => []
            ];

            foreach ($servers as $server) {
                $data = [
                    'server_id' => $server->server_id,
                    'name' => $server->name,
                    'ip' => $server->ip,
                    'provider' => $server->provider,
                    'location' => $server->location,
                    'default' => $server->default,
                    'status' => $server->status,
                    'sites' => count($server->sites)
                ];
                array_push($response['data'], $data);
            }

            return response()->json($response);
        } catch (\Exception $exception) {
            return $this->response->json([
                'success' => false,
                'message' => trans('streams::error.500.name'),
                'errors' => [trans('streams::error.500.name')]
            ], 500);
        }
    }

    public function create()
    {
        $validator = Validator::make($this->request->all(), [
            'ip' => 'required|ip',
            'name' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.bad_request.name'),
                'errors' => $validator->errors()->getMessages()
            ], 400);
        }

        if ($this->request->ip == $this->request->server('SERVER_ADDR')) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_conflict_ip_current_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_conflict.name')
            ], 409);
        }

        if (ServerModel::where('ip', $this->request->ip)->first()) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_conflict_ip_duplicate_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_conflict.name')
            ], 409);
        }

        $server = new ServerModel();
        $server->ip = $this->request->ip;
        $server->name = $this->request->name;
        $server->provider = $this->request->provider;
        $server->location = $this->request->location;
        $server->password = Str::random(24);
        $server->database = Str::random(24);
        $server->server_id = Str::uuid();
        $server->cron = ' ';
        $server->save();

        return response()->json([
            'server_id' => $server->server_id,
            'name' => $this->request->name,
            'provider' => $this->request->provider,
            'location' => $this->request->location,
            'ip' => $this->request->ip,
            'setup' => URL::to('/sh/setup/' . $server->server_id)
        ]);
    }

    public function destroy(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message_default.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        if ($server->default) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.delete_default_server_message.name'),
                'errors' => trans('visiosoft.module.server::field.bad_request.name')
            ], 400);
        }

        $server->delete();

        return response()->json([]);
    }

    public function show(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        return response()->json([
            'sever_id' => $server->server_id,
            'name' => $server->name,
            'ip' => $server->ip,
            'location' => $server->location,
            'provider' => $server->provider,
            'default' => $server->default,
            'php' => $server->php,
            'github_key' => $server->github_key,
            'build' => $server->build,
            'cron' => $server->cron,
            'sites' => count($server->sites)
        ]);
    }

    public function panel()
    {
        $server = ServerModel::where('default', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_native_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $site = SiteModel::where('server_id', $server->id)->where('panel', 1)->first();

        if (!$site) {
            $domain = '';
        } else {
            $domain = $site->domain;
        }

        return response()->json([
            'sever_id' => $server->server_id,
            'name' => $server->name,
            'ip' => $server->ip,
            'location' => $server->location,
            'provider' => $server->provider,
            'domain' => $domain,
            'php' => $server->php,
            'github_key' => $server->github_key,
            'build' => $server->build,
            'cron' => $server->cron,
            'sites' => count($server->sites)
        ]);
    }

    public function paneldomain()
    {
        $server = ServerModel::where('default', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_native_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $site = SiteModel::where('server_id', $server->id)->where('panel', true)->first();
        if ($site) {
            $site->delete();
            PanelDomainRemoveSSH::dispatch($server)->delay(Carbon::now()->addSeconds(3));
        }

        if ($this->request->domain && $this->request->domain != '') {
            $validator = Validator::make($this->request->all(), [
                'domain' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.bad_request.name'),
                    'errors' => $validator->errors()->getMessages()
                ], 400);
            }
            $newsite = new SiteModel();
            $newsite->server_id = $server->id;
            $newsite->domain = $this->request->domain;
            $newsite->site_id = sha1(microtime());
            $newsite->username = md5(microtime());
            $newsite->password = 'Secret_123';
            $newsite->database = 'Secret_123';
            $newsite->panel = true;
            $newsite->save();
            PanelDomainAddSSH::dispatch($server)->delay(Carbon::now()->addSeconds(3));
        }

        return response()->json([]);
    }

    public function panelssl()
    {
        $server = ServerModel::where('default', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_native_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $site = SiteModel::where('server_id', $server->id)->where('panel', true)->first();

        if ($site) {
            PanelDomainSslSSH::dispatch($server, $site)->delay(Carbon::now()->addSeconds(3));
        } else {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.ssl_request_error_message.name'),
                'errors' => trans('visiosoft.module.server::field.bad_request.name')
            ], 400);
        }

        return response()->json([]);
    }

    public function edit(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        if ($this->request->ip) {
            $validator = Validator::make($this->request->all(), [
                'ip' => 'required|ip'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.bad_request.name'),
                    'errors' => $validator->errors()->getMessages()
                ], 400);
            }
            if (!$server->default && $this->request->ip == str_replace("\n", '', file_get_contents('https://checkip.amazonaws.com'))) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.edit_server_current_ip_error_message.name'),
                    'errors' => trans('visiosoft.module.server::field.server_conflict.name')
                ], 409);
            }
            if (ServerModel::where('ip', $this->request->ip)->where('server_id', '<>', $server_id)->first()) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.server_conflict_ip_duplicate_message.name'),
                    'errors' => trans('visiosoft.module.server::field.server_conflict.name')
                ], 409);
            }
            if ($server->default) {
                $server->ip = str_replace("\n", '', file_get_contents('https://checkip.amazonaws.com'));
            } else {
                $server->ip = $this->request->ip;
            }
        }

        if ($this->request->name) {
            $validator = Validator::make($this->request->all(), [
                'name' => 'required|min:3'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.bad_request.name'),
                    'errors' => $validator->errors()->getMessages()
                ], 400);
            }
            $server->name = $this->request->name;
        }

        if ($this->request->provider) {
            $server->provider = $this->request->provider;
        }

        if ($this->request->location) {
            $server->location = $this->request->location;
        }

        if ($this->request->cron) {
            $server->cron = $this->request->cron;
            $server->save();
            CronSSH::dispatch($server)->delay(Carbon::now()->addSeconds(3));
        }

        if ($this->request->php) {
            if (!in_array($this->request->php, config('visiosoft.module.server::pure.phpvers'))) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.bad_request.name'),
                    'errors' => 'Invalid PHP version.'
                ], 400);
            }
            PhpCliSSH::dispatch($server, $this->request->php)->delay(Carbon::now()->addSeconds(3));
            $server->php = $this->request->php;
        }

        $server->save();

        return response()->json([
            'sever_id' => $server->server_id,
            'name' => $server->name,
            'ip' => $server->ip,
            'location' => $server->location,
            'provider' => $server->provider,
            'default' => $server->default,
            'status' => $server->status,
            'php' => $server->php,
            'github_key' => $server->github_key,
            'build' => $server->build,
            'cron' => $server->cron
        ]);
    }

    public function ping(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        try {
            $remote = Http::get('http://' . $server->ip . '/ping_' . $server->server_id . '.php');
            if ($remote->status() == 200) {
                //
            } else {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.server_unavailable_message.name'),
                    'errors' => trans('visiosoft.module.server::field.server_unavailable.name')
                ], 503);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_unavailable_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_unavailable.name')
            ], 503);
        }
    }

    public function healthy(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();

        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        try {
            $remote = Http::get('http://' . $server->ip . '/ping_' . $server->server_id . '.php');
            if ($remote->status() != 200) {
                return response()->json([
                    'cpu' => '0',
                    'ram' => '0',
                    'hdd' => '0'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'cpu' => '0',
                'ram' => '0',
                'hdd' => '0'
            ]);
        }

        try {
            $ssh = new SSH2($server->ip, 22);
            if (!$ssh->login('pure', $server->password)) {

                return response()->json([
                    'message' => trans('visiosoft.module.server::field.server_error_ssh_error_message.name') . $server->server_id,
                    'errors' => trans('visiosoft.module.server::field.server_error.name')
                ], 500);
            }

            $ssh->setTimeout(360);
            $status = $ssh->exec('echo "`LC_ALL=C top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk \'{print 100 - $1}\'`%;`free -m | awk \'/Mem:/ { printf("%3.1f%%", $3/$2*100) }\'`;`df -h / | awk \'/\// {print $(NF-1)}\'`"');
            $ssh->exec('exit');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.something_error_message.name'),
                'errors' => trans('visiosoft.module.server::field.error.name')
            ], 500);
        }

        $status = str_replace('%', '', $status);
        $status = str_replace("\n", '', $status);

        $api = explode(';', $status);

        return response()->json([
            'cpu' => $api[0],
            'ram' => $api[1],
            'hdd' => $api[2]
        ]);
    }

    public function rootreset(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();
        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $last_password = $server->password;
        $new_password = Str::random(24);
        $server->password = $new_password;
        $server->save();

        RootResetSSH::dispatch($server, $new_password, $last_password)->delay(Carbon::now()->addSeconds(1));

        return response()->json([
            'password' => $server->password
        ]);
    }

    public function servicerestart(string $server_id, string $service)
    {
        if (!in_array($service, config('visiosoft.module.server::pure.services'))) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.invalid_service_error_message.name'),
                'errors' => trans('visiosoft.module.server::field.bad_request.name')
            ], 400);
        }

        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();
        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        try {
            $ssh = new SSH2($server->ip, 22);
            if (!$ssh->login('pure', $server->password)) {
                return response()->json([
                    'message' => trans('visiosoft.module.server::field.server_error_ssh_error_message.name') . $server->server_id,
                    'errors' => trans('visiosoft.module.server::field.server_error.name')
                ], 500);
            }

            $ssh->setTimeout(360);
            switch ($service) {
                case 'nginx':
                    $ssh->exec('sudo systemctl restart nginx.service');
                    break;
                case 'php':
                    $ssh->exec('sudo service php8.1-fpm restart');
                    $ssh->exec('sudo service php8.0-fpm restart');
                    $ssh->exec('sudo service php7.4-fpm restart');
                    $ssh->exec('sudo service php7.3-fpm restart');
                    break;
                case 'mysql':
                    $ssh->exec('sudo service mysql restart');
                    break;
                case 'redis':
                    $ssh->exec('sudo systemctl restart redis.service');
                    break;
                case 'supervisor':
                    $ssh->exec('service supervisor restart');
                    break;
                default:
                    //
                    break;
            }
            $ssh->exec('exit');

            return response()->json([]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.something_error_message.name'),
                'errors' => trans('visiosoft.module.server::field.error.name')
            ], 500);
        }
    }

    public function sites(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();
        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $sites = SiteModel::where('panel', false)->where('server_id', $server->id)->get();
        $response = [];

        foreach ($sites as $site) {
            $data = [
                'site_id' => $site->site_id,
                'domain' => $site->domain,
                'username' => $site->username,
                'php' => $site->php,
                'basepath' => $site->basepath,
                'aliases' => count($site->aliases)
            ];
            array_push($response, $data);
        }

        return response()->json($response);
    }

    public function domains(string $server_id)
    {
        $server = ServerModel::where('server_id', $server_id)->where('status', 1)->first();
        if (!$server) {
            return response()->json([
                'message' => trans('visiosoft.module.server::field.server_not_found_message.name'),
                'errors' => trans('visiosoft.module.server::field.server_not_found.name')
            ], 404);
        }

        $response = [];

        foreach ($server->allsites as $site) {
            array_push($response, $site->domain);
            foreach ($site->aliases as $alias) {
                array_push($response, $alias->domain);
            }
        }

        return response()->json($response);
    }


    public function setup(ServerRepositoryInterface $repository, $server_id)
    {
        $server = $repository->newQuery()->where('server_id', $server_id)->where('status', 0)->firstOrFail();

        $script = file_get_contents(__DIR__ . '/../../../resources/scripts/setup.sh');
        $script = Str::replaceArray('???', [
            $server->password,
            $server->database,
            $server->server_id
        ], $script);

        return response($script)
            ->withHeaders(['Content-Type' => 'application/x-sh']);
    }
}
