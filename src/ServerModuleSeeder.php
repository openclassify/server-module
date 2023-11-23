<?php namespace Visiosoft\ServerModule;

use Anomaly\Streams\Platform\Support\Str;
use Illuminate\Database\Seeder;
use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;

class ServerModuleSeeder extends Seeder
{
    public function run()
    {
        $serverRepository = app(ServerRepositoryInterface::class);
        $server = $serverRepository->newQuery()->first();
        if (!$server) {
            $params = [
                'ip' => '127.0.0.1',
                'server_id' => Str::uuid(),
                'name' => 'This Vps',
                'default' => true,
            ];
            $serverRepository->create($params);
        }
    }
}
