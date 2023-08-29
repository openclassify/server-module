<?php namespace Visiosoft\ServerModule\Server\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

interface ServerInterface extends EntryInterface
{
    public function getStatus();
}
