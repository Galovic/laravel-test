<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserRequest;
use App\Models\Entrust\Role;
use App\Models\User;

class UsersController extends AdminController
{

    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:users-show')->only('index');
        $this->middleware('permission:users-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:users-edit')->only([ 'edit', 'update', 'toggle' ]);
        $this->middleware('permission:users-delete')->only('delete');
    }

    /**
     * Request: show users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription('Uživatelé', 'přehled uživatelů');

        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }


    /**
     * Request: Show form to create new user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription('Uživatelé', 'nový uživatel');
        $roles = Role::enabled()->get();
        return view('admin.users.create', compact('roles'));
    }


    /**
     * Request: store new user
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->getValues());

        $user->roles()->sync($request->getRoles());

        flash('Uživatel úspěšně vytvořen.', 'success');
        return redirect()->route('admin.users');
    }


    /**
     * Request: Show form to update specified user
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->setTitleDescription('Uživatelé', 'upravit uživatele');
        $roles = Role::enabled()->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }


    /**
     * Request: update specified user
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        if($user->protected)
        {
            flash('Tohoto uživatele nelze upravit.', 'warning');
            return redirect()->route('admin.users');
        }

        $user->update($request->getValues());

        $user->roles()->sync($request->getRoles());

        flash('Uživatel úspěšně upraven.', 'success');
        return redirect()->route('admin.users');
    }


    /**
     * Request: delete specified user
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
        if($user->protected)
        {
            flash('Tohoto uživatele nelze upravit.', 'warning');
            return $this->refresh();
        }

        $user->delete();

        flash('Uživatel úspěšně smazán.', 'success');
        return $this->refresh();
    }


    /**
     * Request: Toggle user enabled/disabled
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggle(User $user)
    {
        if($user->protected)
        {
            flash('Tohoto uživatele nelze upravit.', 'warning');
            return $this->refresh();
        }

        $user->update([
            'enabled' => !$user->enabled
        ]);

        flash('Uživatel úspěšně '. ( $user->enabled ? 'povolen' : 'zakázán' ) .'.', 'success');
        return $this->refresh();
    }

}