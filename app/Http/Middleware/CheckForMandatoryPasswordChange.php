<?php
namespace App\Http\Middleware;

use App\Models\Setting;
use Auth;
use Closure;
use Illuminate\Support\Facades\Schema;
use Log;

class CheckForMandatoryPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /**
         * Ignore the middleware if we aren't authenticated
         */
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->path() == 'password/change') {
            /**
             * If the user doesn't need to change their password, prevent them from accessing the form 
             * since we don't require the the current password
             */            
            if (Auth::user()->mandatory_password_change_required == false) {
                return redirect()->to('/');
            }

            /**
             * If we are currently on the password change form and need to change our password, ignore the middleware
             */
            if (Auth::user()->mandatory_password_change_required == true) {
                return $next($request);
            }
        }        

        /**
         * If the users needs to change their password and isn't on the form, redirect them to the form
         */
        if (Auth::user()->mandatory_password_change_required == true) {
            return redirect()->route('mandatory-password-change');
        }

        return $next($request);

    }
}
