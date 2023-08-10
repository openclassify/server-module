<?php namespace Visiosoft\ServerModule\Server;

use Visiosoft\ServerModule\Server\Contract\ServerInterface;
use Anomaly\Streams\Platform\Model\Server\ServerServerEntryModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServerModel extends ServerServerEntryModel implements ServerInterface
{
    use HasFactory;

    /**
     * @return ServerFactory
     */
    protected static function newFactory()
    {
        return ServerFactory::new();
    }
}
