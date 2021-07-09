<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TokenResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use URL;
use DateTime;
use App\Traits\MailTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Mail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Models\user_activities;
use Carbon\Carbon;
use App\Http\Resources\RegisterResource;

class AuthController extends Controller
{

  use AuthenticatesUsers;
  use MailTrait;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */


  /**
   * Create a new controller instance.
   *
   * @return void
   */
   protected $username;

   /**
    * Create a new controller instance.
    */
   public function __construct()
   {
       $this->middleware('guest')->except('logout');
       $this->username = $this->findUsername();
   }

   /**
    * Get the login username to be used by the controller.
    *
    * @return string
    */
   public function findUsername()
   {
       $login = request()->input('email');

       $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

       request()->merge([$fieldType => $login]);

       return $fieldType;
   }

   /**
    * Get username property.
    *
    * @return string
    */
   public function username()
   {
       return $this->username;
   }

  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array  $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
      return Validator::make($data, [
          'name' => ['required', 'string', 'max:255'],
          'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
          'phone' =>['required','numeric','min:11','unique:users'],
      ]);
  }


   /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */



    protected function create(Request $request)
    {
      $messages = [
        'required' => 'The :attribute field is required.',
        'email.required' => 'The email field is required.',
        'email.email' => 'The email needs to have a valid format.',
        'password.regex' => 'Password must contain at least 1 lower-case and capital letter, a number and symbol.'
    ];

      $validator=Validator::make($request->all(), [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'pin' => ['required', 'string','max:4'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone' =>['required','unique:users','regex:/^([0-9\s\-\+\(\)]*)$/','min:10'],
        'password' => ['required', 'string', 'min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*#?&]/', 'confirmed'],
        // 'dob' => ['required','date_format:Y-m-d','before:today'],
      ], $messages);
      $verifyToken=Str::random(40);
    if($validator->fails())
    {

          return response()->json([
          "code"  =>  Response::HTTP_BAD_REQUEST,
          "type"  => "invalid",
          "message"  =>  "invalid_credentials",
          "developerMessage"  => $validator->messages(),
          ], Response::HTTP_BAD_REQUEST);
    }
    else {
     $name = $request->first_name.' '.$request->last_name;
      $email_code = strtoupper(Str::random(6));
       $email_time = Carbon::parse()->addMinutes(5);
        $time=Carbon::now();
        $user = User::create([
          'name' => $request->first_name.' '.$request->last_name,
          'email' => $request->email,
          'phone' => $request->phone,
          'password' => bcrypt($request->password),
          'email_code'=>$email_code,
          'email_time' => $email_time,
          'email_verify'=>0,
          'pin'=>$request->pin,
          'ip_address' =>  request()->ip(),
          'status' => '0',
          'is_permission' =>'3'
      ]);
          $url= url("/api/Authorization/{$email_code}");
          $text ="
          <p>Hello ".$name.",  </p>
          <p> Welcome you to ".config('app.name')." as we hope to serve you better. </p>
          <p>Otp is ".$email_code."</p> <p>  Please click on the link to verify your Invitation. </p>

<br />
<a href=".$url.">Verify E-Mail</a>
";
$data = array(
  'text' => $text
);

      $this->sendsregisterEMail($request->email,$name,'Account Created ',$data);

            $u = new RegisterResource($user);

            return response()->json([
              "code"  =>  Response::HTTP_CREATED,
              "message"  =>  "Account Created Please Check Register Email Account To Complete Registration",
              "developerMessage"  => $u,
              ], Response::HTTP_CREATED);



    }
}



    /**
     * Get a JWT via given credentials.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

      $validator=Validator::make($request->all(), [
                'email' => 'required', 'string', 'email', 'max:255',
                 'password' => ['required', 'string', 'max:255'],
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

      $login = request()->input('email');

      $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

      request()->merge([$fieldType => $login]);
      $credentials = $request->only([$fieldType, 'password']);
//$check=auth()->setTTL(360)->claims(['expires' =>  Carbon::now()->addDays(1)->toDateTimeString()])->attempt($credentials);
//$check=JWTAuth::attempt($credentials,['exp' =>  Carbon::now()->addDays(1)->toDateTimeString()]);JWT_TTL=3600
$token = JWTAuth::attempt($credentials, ['exp' => Carbon::now()->addDays(1)->toDateTimeString()]);

      if (!$token) {

          return response()->json([
          "code"  =>  Response::HTTP_NOT_FOUND,
          "type"  => "invalid",
          "message" => "We were unable to validate your credentials, please check your credentials and try again",
          "developerMessage" => "These credentials do not match our records."
        ],Response::HTTP_NOT_FOUND);
      }
      else{
    $check=User::where('email',request()->input('email'))->first();

    if($check->email_verify==0)
    {
      return response()->json([
        "message"=>"verify",
        "code"  =>  Response::HTTP_FORBIDDEN,
        "developerMessage" => "Account unverify please check your email for verification link"
      ],Response::HTTP_FORBIDDEN);
    }
   return $this->respondWithToken($token);
      }


    }
  }



    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

      $role = Auth::user()->is_permission;
      //dd($role);
      switch($role)
      {
        case 1:
           $access='administrator';
          break;
        case 2:
           $access='staff';
          break;
          case 3:
             $access='customer';
            break;
        default:
           $access='invalid';
          break;
      }

      $userdetails=auth()->user();

  $userinfo=new TokenResource($userdetails);
      // $user=array();
      //   $user = auth()->payload();
      //   $u=$user['exp'];
      //   $dt=new DateTime("@$u");
return response()->json([
    'user'=>$userinfo,
    'token' => $token,
    'message'=>"Welcome Login Successful"
]);
    }




    protected function loginWithToken($token,$message)
    {
      $user=array();
        $user = auth()->payload();
        $u=$user['exp'];
        $dt=new DateTime("@$u");
  //$account_details=$this->Useraccountinfo(auth()->user()->customerid);
      //  $user=$this->getUser($token);
        $data =array(
          // 'customer_id'   =>  auth()->user()->customerid,
            'accessToken' => $token,
            'expires' => $dt->format('Y-m-d H:i:s')
          //    'expires' => auth()->factory()->getTTL() * 1400 // can change to 5 or 10 mins

        );
        return response()->json(compact('data','message'));
    }
 /**
     * Get the user by token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($request)
    {
        JWTAuth::setToken($request);
        $user = JWTAuth::toUser();
        return response()->json($user);
    }





    public function Useraccountinfo($request)
    {
      $data['user_usdaccounts_info'] = DB::table('usdaccounts')->where('customerid', $request)->get();
      $data['user_account_info'] = DB::table('accounts')->where('customerid', $request)->get();
        $data['user_customer_info'] = DB::table('customers')->where('customerid', $request)->get();
      return response()->json($data);
    }

    public function logout()
  {
  //try {
  auth()->logout();

      return response()->json([
         'data' =>'200',
         'message' =>'Successfully logged out'
      ], 200);

  // } catch (JWTException $e) {
  //     if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
  //         return response()->json(['status' => 'Token is Invalid']);
  //     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
  //       // $refreshed = JWTAuth::refresh(JWTAuth::getToken());
  //       // $user = JWTAuth::setToken($refreshed)->toUser();
  //   //    header('Authorization: Bearer ' . $refreshed);
  //         return response()->json(['status' => 'Token is Expired']);
  //     }else{
  //         return response()->json(['status' => 'Authorization Token not found']);
  //     }
  // }



  }


  public function getAuthenticatedUser()
          {
                  try {

                          if (! $user = JWTAuth::parseToken()->authenticate()) {
                                  return response()->json(['user_not_found'], 404);
                          }

                  } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                          return response()->json(['token_expired'], $e->getStatusCode());

                  } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                          return response()->json(['token_invalid'], $e->getStatusCode());

                  } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                          return response()->json(['token_absent'], $e->getStatusCode());

                  }

                  return response()->json(compact('user'));
          }



}
