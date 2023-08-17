<?php namespace Visiosoft\ServerModule\Server;

use Visiosoft\ServerModule\Server\Contract\ServerInterface;
use Anomaly\Streams\Platform\Model\Server\ServerServerEntryModel;

class ServerModel extends ServerServerEntryModel implements ServerInterface
{

    public function getIp()
    {
        return $this->ip;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDatabasePassword()
    {
        return $this->database;
    }
}
