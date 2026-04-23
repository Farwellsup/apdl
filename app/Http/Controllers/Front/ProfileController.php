<?php

namespace App\Http\Controllers\Front;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfileController extends Controller
{

    protected $repository;

    public function __construct()
    {
       $this->edxRepository = $this->getEdxRepository();
     
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {

       $user = Auth::user();

        $rules = [];


        if (empty($user->unit_id) && $request->has('unit_id')) {
            $rules['unit_id'] = 'required|exists:units,id';
        }

        if (empty($user->department_id) && $request->has('department_id')) {
            $rules['department_id'] = 'required|exists:departments,id';
        }

        if (empty($user->country_id) && $request->has('country_id')) {
            $rules['country_id'] = 'required|exists:countries,id';
        }

        if (empty($user->gender_id) && $request->has('gender_id')) {
            $rules['gender_id'] = 'required';
        }

        if ($request->has('old_password')) {
            $rules['old_password'] = ['required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('Current password is incorrect.');
                    }
                },
            ];
        }

         if ($request->has('password')) {
            $rules['password'] = [
                                'required',
                                'string',
                                'min:8',
                                'confirmed',
                                Password::min(8)
                                    ->mixedCase()
                                    ->letters()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised(),
                            ];
        }

        if (empty($user->policy_agree) && $request->has('policy_agree')) {
            $rules['policy_agree'] = 'required|accepted';
        }

        if (empty($rules)) {
            return redirect()->back();
        }

        $messages = [
            'unit_id.required' => 'Select your Unit to proceed',
            'department_id.required' => 'Select your Department to proceed',
            'country_id.required' => 'Select your Country to proceed',
            'gender_id.required' => 'Select your Gender to proceed',
            'old_password.required' => 'Enter the current password to proceed',
            'password.required' => 'Enter a new password to proceed',
            'policy_agree.required' => 'This is required to access the platform',
        ];

        $validated = $request->validate($rules, $messages);


         $edxReset =  (App::environment(['local', 'staging'])) ? true : $this->edxRepository->resetEdxPassword($findUser, decrypt($pd->position));

         if ($edxReset !== true) {

            return redirect()->back()->with('profile_error', 'An error occured while submitting your password to the lms. Please refresh the page and try again');
         }

        $user->fill($validated);

        if (array_key_exists('policy_agree', $rules)) {
            $user->policy_agree = $request->has('policy_agree') ? 1 : 0;
            
        }

        $user->department_name  = $user->department?->title;
        $user->unit_name      = $user->unit?->title;
        $user->country_name   = $user->country?->title; 
        $user->reset_pd = 1;
        $user->last_login_at    = Carbon::now();

        $user->save();


        return redirect()->route('pages', ['key' => 'courses'])->with('status', 'profile-updated');
    }



    protected function getEdxRepository()
    {
        return App::make("App\\Repositories\\AuthenticateEdxRepository");
    }


}
