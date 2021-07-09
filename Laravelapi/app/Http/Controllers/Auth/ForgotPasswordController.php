<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\User;
use App\Traits\MailTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use MailTrait;
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getUserResetLinkEmail(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
          ]);
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
       
           if (!$this->validateEmail($request->email)) {
            $data =array(
                'success' => false,
                'message' => $request->email.' account is not register with us please check email again or create a new account',
                'status' => '404'
            );
            return response()->json(compact('data'));
           }
           else{
               $token=$this->createToken($request->email);
               
            $thisUser = User::where('email',$request->email)->first();
            $url= url("/api/password.reset/{$token}");
            $text ="
            <p>Hello ".$thisUser->name.",  </p>
            <p> You are receiving this email because we received a password reset request for your account. </p>
            <p>Email       :        ".$request->email.",</p>
      
  <br /><p>
  <a href=".$url.">Reset Password</a></p><br/>
  <p>  If you did not request a password reset, no further action is required. </p>
  ";
 
     $this->sendsEMail($thisUser->email,$thisUser->name,'Reset Password ',$text);

            $data =array(
                'success' => true,
                'message' => 'Reset email sent please check your mail box',
                'status' => '200'
            );
            return response()->json(compact('data'));
           }
      
    }
    }

    public function createToken($email)
    {
        $oldToken=DB::table('password_resets')->where('email',$email)->first();
     if ($oldToken) {
        return $oldToken->token;
     } 
     else {
        $token=str_random(60);
        $this->saveToken($token,$email);
        return   $token;
     }  
    }

    public function saveToken($token,$email)
    {
        DB::table('password_resets')->insert([
            'email'=>$email,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);
    }

    public function validateEmail($email)
    {
        return !!User::where('email',$email)->first();
    }
}
