<?php namespace App\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends AdminController {

    /**
     * Show form to edit role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit()
    {
        $this->setTitleDescription(
            'Účet',
            'Nastavení'
        );

        $user = auth()->user();

        return view('admin.account.edit', compact('user'));
    }


    /**
     * Update account
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'max:250',
            'lastname' => 'max:250',
            'email' => ['required', 'email', Rule::unique(User::getTableName(), 'email')->ignore(auth()->id())],
            'username' => ['required', 'max:100', Rule::unique(User::getTableName(), 'username')->ignore(auth()->id())],
            'image' => 'image',
            'position' => 'max:250',
            'about' => 'max:1000'
        ]);

        /** @var User $user */
        $user = auth()->user();
        $user->update($request->all());

        if($request->hasFile('image')) {

            if (!file_exists($imagesDir = $user::getImagesDirectory())) {
                mkdir($imagesDir, 0755, true);
            }

            \Image::make($request->file('image'))
                ->encode('jpg')
                ->fit(80, 80)
                ->save($user->image_path);
        }
        elseif ($request->input('remove_image') == 'true' && $user->hasCustomImage()) {
            \File::delete($user->image_path);
        }

        flash('Změny byly uloženy.', 'success');
        return redirect()->route('admin.account.edit');
    }


    /**
     * Change account password
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|min:8|regex:/^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
            'verify_new_password' => 'required|same:new_password',
        ], [
            'password.required' => 'Zadejte prosím vaše současné heslo.',
            'new_password.min' => 'Heslo musí mít alespoň 8 znaků.',
            'new_password.regex' => 'Heslo musí být kombinací velkých a malých písmen a číslic.',
            'verify_new_password.required' => 'Zadejte heslo znovu pro ověření správnosti.',
            'verify_new_password.same' => 'Hesla se neshodují.',
        ]);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        $user =  auth()->user();

        if(\Hash::check($request->input('password'), $user->password)) {
            $user->forceFill([
                'password' => bcrypt($request->input('new_password'))
            ])->save();
        }
        else
        {
            $validator->getMessageBag()->add('password', 'Zadejte prosím platné aktuální heslo.');
            $this->throwValidationException($request, $validator);
        }

        flash('Heslo úspěšně změněno.', 'success');
        return redirect()->route('admin.account.edit');
    }


}