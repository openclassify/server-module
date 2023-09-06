<?php namespace Visiosoft\ServerModule\Server;

use Visiosoft\ServerModule\Server\Contract\ServerInterface;
use Anomaly\Streams\Platform\Model\Server\ServerServerEntryModel;
use Visiosoft\SiteModule\Site\SiteModel;

class ServerModel extends ServerServerEntryModel implements ServerInterface
{
    protected $hidden = [
        'id',
        'password',
        'database',
        'created_at',
        'updated_at'
    ];

    public function getStatus()
    {
        return $this->getAttribute('status');
    }

    public function sites()
    {
        return $this->hasMany(SiteModel::class,'server_id')->where('panel', false);
    }

    public function allsites()
    {
        return $this->hasMany(SiteModel::class);
    }
  
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
