<?php namespace Visiosoft\ServerModule\Http\Controller\Admin;

use Visiosoft\ServerModule\Server\Contract\ServerRepositoryInterface;
use Visiosoft\ServerModule\Server\Form\ServerFormBuilder;
use Visiosoft\ServerModule\Server\Table\ServerTableBuilder;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

class ServerController extends AdminController
{

    /**
     * Display an index of existing entries.
     *
     * @param ServerTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ServerTableBuilder $table)
    {
        return $table->render();
    }

    /**
     * Create a new entry.
     *
     * @param ServerFormBuilder $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(ServerFormBuilder $form)
    {
        return $form->render();
    }

    /**
     * Edit an existing entry.
     *
     * @param ServerFormBuilder $form
     * @param        $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(ServerFormBuilder $form, $id)
    {
        return $form->render($id);
    }

    public function installation(ServerRepositoryInterface $repository, $server_id)
    {
        $server = $repository->newQuery()->where('server_id', $server_id)->where('status', 0)->firstOrFail();

        return $this->view->make('module::installation',compact('server'));
    }
}
