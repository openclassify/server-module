<?php namespace Visiosoft\ServerModule\Http\Controller;

use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Illuminate\Support\Str;
use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;

class ServerController extends PublicController
{
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
