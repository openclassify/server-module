<?php namespace Visiosoft\ServerModule\Server\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

class ServerTableButtons
{
    public function handle(ServerTableBuilder $builder)
    {
        $builder->setButtons([]);
    }
}
