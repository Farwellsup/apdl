<?php

namespace App\Http\Controllers\Auth;

use App;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;

        
    }
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->ensureIsNotRateLimited();

        try {

            $user = User::where('payroll_number', $request->input('payroll_number'))->first();

      

            if (!$user) {
                return redirect()->route('login')->withErrors([

                    'payroll_number' => 'The provided username does not match our records.',

                ])->withInput();
            }

            if ($user->published == 0) {

                return redirect()->route('login')->with('error', 'Kindly contact the administrator to activate your account.');
            }


            $credentials = ['payroll_number' => $request->input('payroll_number'), 'password' => $request->input('password')];


            if (!Auth::attempt($credentials)) {

                return redirect()->route('login')->withErrors([
                    'payroll_number' => 'The provided credentials do not match our records.',
                ])->onlyInput('payroll_number');

            } else {

                $edxStatus  =   App::environment(['local', 'staging']) ? true : $this->edxLogin($request);

                if (!$edxStatus) {

                    $this->destroy($request);

                    return redirect()->route('login')->with('error', 'Unable to login to the platform. Please try again later.');
                }

                if ($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3) {

                    $this->authManager->guard('twill_users')->loginUsingId($user->id);


                    $user->last_login_at = Carbon::now();
                    $user->save();

                 
                    $request->session()->regenerate();

                    return redirect()->route('pages', ['key' => 'courses']);
                } else {


                    $user->last_login_at = Carbon::now();
                    $user->save();

                    $request->session()->regenerate();

   
                    return redirect()->route('pages', ['key' => 'courses']);
                }
            }
        } catch (Exception $e) {

            return redirect()->route('login')->with('error', 'Something went wrong! Kindly try again or contact the administrator');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
