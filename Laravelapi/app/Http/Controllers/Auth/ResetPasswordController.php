<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\MailTrait;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use MailTrait;
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected function sendResetResponse($response)
    {
        return response()->json(['success' => trans($response)]);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['error' => trans($response)], 401);
    }

    // removed min:6 validation
    protected function reset(Request $request)
    {
        $messages = [
            'required' => 'The :attribute field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email needs to have a valid format.',
            'password.regex' => 'Password must contain at least 1 lower-case and capital letter, a number and symbol.'
        ];
    
          $validator=Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*#?&]/', 'confirmed'],
            'token' => ['required'],
          ], $messages);
        if($validator->fails())
        {
    
              return response()->json([
              "code"  =>  '400',
              "type"  => "invalid",
              "message"  =>  "invalid_credentials",
              "developerMessage"  => $validator->messages(),
              ], 400);
        }
        else {
            return $this->getpasswordresettablerow($request)->count() > 0 ? $this->changepassword($request) : $this->tokenNotfound();

        }
    }

    private function getpasswordresettablerow($request)
    {

        return DB::table('password_resets')->where(['email'=>$request->email,'token' =>$request->token]);
    }

    private function changepassword($request)
    {
        $user= User::whereEmail($request->email)->first();
        $user->update(['password'=>bcrypt($request->password)]);
        $this->getpasswordresettablerow($request)->delete();
        $text ="
        <p>Hello ".$user->name.",  </p>
        <p> You are receiving this email because you just changed your account  password . </p>
        
<p>  If you did not request a password reset, please try to reset your password again  and also change the password to your personal email. </p>
";

 $this->sendsEMail($user->email,$user->name,'Reset Password Notification',$text);
        return response()->json(['error' =>'Password has been changed','code'=>Response::HTTP_CREATED],Response::HTTP_CREATED);
    }

    private function tokenNotfound()
    {
        return response()->json(['error' =>'Token or Email is incorrect','code'=>Response::HTTP_UNPROCESSABLE_ENTITY],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
