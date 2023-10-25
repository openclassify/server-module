<?php namespace Visiosoft\ServerModule;

use Illuminate\Database\Seeder;
use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;

class ServerModuleSeeder extends Seeder
{
    public function run()
    {
        $serverRepository = app(ServerRepositoryInterface::class);
        $server = $serverRepository->newQuery()->where('server_id', "33f66b6e-732a-11ee-b962-0242ac12000")->first();
        if (!$server) {
            $params = [
                'ip' => '127.0.0.1',
                'server_id' => '33f66b6e-732a-11ee-b962-0242ac12000',
                'name' => 'This Vps',
            ];
            $serverRepository->create($params);
        }
    }
}
