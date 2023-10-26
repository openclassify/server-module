<?php

namespace Visiosoft\ServerModule\Console;

use Illuminate\Console\Command;
use Visiosoft\ServerModule\Server\ServerModel;

class UpdateServerCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servers:update_server_credentials {server} {ip} {sshpass} {dbpass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Initial Server ';


    public function handle()
    {
        $server = ServerModel::query();

        if (strlen($this->argument('server')) != 0) {
            $server->where('server_id', $this->argument('server'));
        }

        if ($server->first()) {
            $server->update(
                [
                    'ip' => $this->argument('ip'),
                    'password' => $this->argument('sshpass'),
                    'database' => $this->argument('dbpass')
                ]);
        }
    }
}
