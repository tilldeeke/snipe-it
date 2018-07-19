<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MandatoryPasswordChangeController extends Controller
{
    /**
     * Shows the form to change the users password
     * 
     * @return View
     */
    public function edit()
    {
        /**
         * Collecting the rules the password needs to meet
         */
        $passwordRules = collect();

        /**
         * Password Minimum length
         */
        $minPasswordLength = Setting::getSettings()->pwd_secure_min;
        $passwordRules->push(trans('passwords.rule_length', ['length' => $minPasswordLength]));

        /**
         * Password complexity
         */
        $passwordComplexityRules = explode('|', Setting::getSettings()->pwd_secure_complexity);

        if ($passwordComplexityRules[0] != '') {
            foreach($passwordComplexityRules as $complexityRule) {
                $passwordRules->push(trans('passwords.rule_complexity_' . $complexityRule));
            }
        }
        return view('auth.passwords.change', compact('passwordRules'));
    }

    /**
     * Updates the users password
     *             
     * @param  Request $request
     * @return Redirect
     */
    public function update(Request $request)
    {   
        $this->validate($request, [
            'password'         => Setting::passwordComplexityRulesSaving('store'),
            'password_confirmation' => 'required|same:password',
        ]);

        $user = Auth::user();

        // Set the new password
        $user->password = Hash::make($request->input('password'));

        // Reset the flag for requiring the password change
        $user->mandatory_password_change_required = false;

        $user->save();
      
        return redirect()->route('home')->withSuccess(trans('auth.message.change_password.success'));
    }    
}
