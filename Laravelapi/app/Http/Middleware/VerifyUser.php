<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyUser
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
       
        $user = User::where('email',$request->email)->orWhere('phone',$request->email)->first();

        if ($user->email_verify != 1) {
            $data =array(
                'success' => false,
                'message' => 'Please check your email to verify your account',
                'status' => '401'
            );
               return response()->json(compact('data'));
               // return redirect()->route('email-verify');
        }
        // if ($user->phone_verify != 1) {
        //     return redirect()->route('phone-verify');
        // }

        if ($user->status == 1) {
            Auth::logout();
            $data =array(
                'success' => false,
                'message' => 'Sorry Your Account is Block Now..!',
                'status' => '401'
            );
               return response()->json(compact('data'));
        }
        return $next($request);
    }
}
