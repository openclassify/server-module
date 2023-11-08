<?php namespace Visiosoft\ServerModule\Server\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

class ServerTableButtons
{
    public function handle(ServerTableBuilder $builder)
    {
        $builder->setButtons([
            'action' => [
                'text' => function (EntryInterface $entry) {
                    return $entry->getStatus() || $entry->getIp() == $_SERVER['REMOTE_ADDR'] ? 'module::button.manage' : 'module::button.install';
                },
                'type' => function (EntryInterface $entry) {
                    return $entry->getStatus() || $entry->getIp() == $_SERVER['REMOTE_ADDR'] ? 'info' : 'success';
                },
                'href' => function (EntryInterface $entry) {
                    return $entry->getStatus() || $entry->getIp() == $_SERVER['REMOTE_ADDR'] ? '/admin/server/manage/{entry.server_id}' : '/admin/server/installation/{entry.server_id}';
                },
                'icon' => function (EntryInterface $entry) {
                    return $entry->getStatus() ? 'fa fa-cog' : 'fa fa-upload';
                }
            ],
            'delete' => [
                'enabled' => function (EntryInterface $entry) {
                    return $entry->getIp() != $_SERVER['REMOTE_ADDR'];
                },
            ]
        ]);
    }
}
