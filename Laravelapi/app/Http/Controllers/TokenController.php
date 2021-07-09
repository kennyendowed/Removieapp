<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\MailTrait;
use App\Http\Resources\TokenResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

class TokenController extends Controller
{
  use MailTrait;
  public function me()
  {
   if (!auth()->user()) {
    return response()->json([
      'data'  => 'Token is Invalid',
      'code' => '404',
    ], 404);
   }
   else {
    $token=auth()->user();
return new TokenResource($token);
   }
  }


  public function payload()
  {
    $user = auth()->payload();

    return response()->json($user);
  }

  public function Authorization(Request $request ,$id)
  {
      $url="http://localhost:3000/login";
    //Crypt::decrypt($request->id)
    //Crypt::encrypt($request->id)

  $user = User::where('email_code','=',$id)->get();
//dd($user->isEmpty());

if (!$user->isEmpty()) {
if ($user[0]->email_code == $id)
{
    $datetime=date("Y-m-d h:i:s");
    $useOwner = User::findOrFail($user[0]->id);
    $useOwner->email_verify = 1;
    $useOwner->email_verified_at = $datetime;
    $useOwner->save();
    return Redirect::away($url);
   //  return response()->json([
   //  "code"  =>  Response::HTTP_OK,
   //  "developerMessage" => $request->get('name')."Welcome Verification successfully!"
   // ],Response::HTTP_OK);
}else{
 return response()->json([
 "code"  =>  Response::HTTP_NOT_FOUND,
 "developerMessage" => "Verification Code in Invalid."
],Response::HTTP_NOT_FOUND);

}
}
else{
return response()->json([
  'developerMessage'  => 'Verification Code Unavailable',
  'code' =>Response::HTTP_NOT_FOUND,
], Response::HTTP_NOT_FOUND);
}

  }

  public function resendEmail(Request $request)
  {
      $user = User::findOrFail(Auth::user()->id);
      if ($user->email_time > Carbon::now())
      {
          $tt = Carbon::parse($user->email_time)->diffForHumans();
          return response()->json([
            'data'  => 'Please Try Again. After '.$tt,
            'code' => '404',
          ], 404);
      }else{
        $email_code = strtoupper(Str::random(6));
        $url= url("/Authorization/{$email_code}");
        $text ="
        <p>Hello ".$user->name.",  </p>
        <p> Welcome you to COINAPPY  as we hope to serve you better. </p>
        <p>Your Verification Code Is:".$email_code.",</p>
        <p>  Please click on the link to verify your email account.        </p>

<br />
<a href=".$url.">Verify E-Mail</a>
";
          $this->sendsEMail($user->email,$user->name,'Email verification',$text);
          $useOwner = User::findOrFail($user->id);
          $useOwner->email_code = $email_code;
          $useOwner->email_time = Carbon::parse()->addMinutes(5);
          $useOwner->save();
          return response()->json([
            'code' => '200',
            'status'  =>$user->name.' New Email Verification Code Send Your Email Address',
         ], 200);
      }
  }



  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  //     try{
  // //   $newtoken = $this->respondWithToken(auth()->refresh());
  //        $newtoken = auth()->refresh();
  //     }catch(\Tymon\JWTAuth\Exceptions\TokenInvalideException $e){
  //         return response()->json(['error' => $e->getMessage()],401);
  //     }
  //     return response()->json(['access_token' => $newtoken]);
  }
  protected function respondWithToken($token)
  {


return response()->json([
  'accessToken' => $token,
  'expires' =>  Carbon::now()->addDays(1)->toDateTimeString(),
  'expires_inn' => auth()->factory()->getTTL(),
]);
  }
  public function clearRoute()
      {
           return   \Artisan::call('route:clear');
     //  return    \Artisan::call('optimize');
      }
      public function invalidate()
      {
      return response()->json(auth()->invalidate());
      }
}
