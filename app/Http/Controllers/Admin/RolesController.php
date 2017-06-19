<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\Entrust\Role;
use App\Models\Module\InstalledModule;

class RolesController extends AdminController {

    /**
     * Show roles
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription(
            trans('admin/roles/general.page.index.title'),
            trans('admin/roles/general.page.index.description')
        );

        $roles = Role::paginate(10);
        return view('admin.roles.index', compact('roles'));
    }


    /**
     * Show form to create new role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/roles/general.page.create.title'),
            trans('admin/roles/general.page.create.description')
        );

        $groups = $this->getPermissionGroups();

        return view('admin.roles.create', compact('groups'));
    }


    /**
     * Store new role
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request)
    {
        $role = new Role($request->getValues());
        $role->setAutomaticName();
        $role->save();

        $role->saveNamedPermissions($request->getPermissions());

        flash(trans('admin/roles/general.status.created'), 'success');

        return redirect()->route('admin.roles');
    }


    /**
     * Show form to edit role
     *
     * @param Role $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $this->setTitleDescription(
            trans('admin/roles/general.page.edit.title'),
            trans('admin/roles/general.page.edit.description', ['name' => $role->name])
        );

        if(!$role->isEditable())
        {
            flash(trans('admin/roles/general.status.no-updated'), 'warning');
            return redirect()->route('admin.roles');
        }

        $permissionsNames = $role->getAllPermissionsNames();
        $groups = $this->getPermissionGroups();

        return view('admin.roles.edit', compact('role', 'permissionsNames', 'groups'));
    }


    /**
     * Update role
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        if(!$role->isEditable())
        {
            flash(trans('admin/roles/general.status.no-updated'), 'warning');
            return redirect()->route('admin.roles');
        }

        $role->update($request->getValues());
        $role->saveNamedPermissions($request->getPermissions());

        flash(trans('admin/roles/general.status.updated'), 'success');

        return redirect()->route('admin.roles');
    }


    /**
     * Delete role
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Role $role)
    {
        if(!$role->isEditable())
        {
            flash(trans('admin/roles/general.status.no-deleted'), 'warning');
            return redirect()->route('admin.roles');
        }

        $role->delete();
        flash(trans('admin/roles/general.status.deleted'), 'success');

        return $this->refresh();
    }


    /**
     * Toggle enabled / disabled
     *
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Role $role)
    {
        if(!$role->isEditable())
        {
            flash(trans('admin/roles/general.status.no-deleted'), 'warning');
        }else{
            $role->update([
                'enabled' => !$role->enabled
            ]);
            flash(trans('admin/roles/general.status.enabled'), 'success');
        }

        return redirect()->route('admin.roles');
    }


    /**
     * Load permission groups including modules.
     *
     * @return array
     */
    private function getPermissionGroups() {
        $groups = config('permissions.groups', []);

        /** @var InstalledModule $installedModule */
        foreach (InstalledModule::enabled()->get() as $installedModule) {
            $moduleGroup = $installedModule->module->config('permissions.group');

            if ($moduleGroup && isset($groups[$moduleGroup])) {
                $groups[$moduleGroup]['modules'][] = $installedModule->module;
            }
        }

        return $groups;
    }

}