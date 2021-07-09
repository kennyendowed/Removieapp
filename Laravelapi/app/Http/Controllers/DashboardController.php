<?php

namespace App\Http\Controllers;


use App\User;
use Input;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\activities;
use App\Models\user_activities;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Response;


class DashboardController extends Controller
{

    public function __construct()
    {

    }

    public function createActivities(Request $request)
    {

        $validator=Validator::make($request->all(), [
            'name' =>['required', 'string'],
            'description' => ['required', 'string']
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
            $activities = activities::create($request->all());
            return response()->json(['data' => $activities,'code'=>Response::HTTP_CREATED],Response::HTTP_CREATED);
        }

    }

    public function getActivities()
    {
        $data['page_title'] = "View Activites";
        $data['activities'] = activities::all();
        return response()->json(['message' =>$data,'code'=>Response::HTTP_OK],Response::HTTP_OK);
    }
    public function getActiveActivities()
    {
        $data['page_title'] = "View active Activites";
        $data['activities'] = activities::where('status','=','1')->get();
        return response()->json(['message' =>$data,'code'=>Response::HTTP_OK],Response::HTTP_OK);
    }

    public function addWristbandUser(Request $request)
    {

        $p = User::with(['activities'])->where('qrcode', $request->get('qrcode'))->orWhere('phone', $request->get('qrcode'))->orWhere('wristband_id', $request->get('qrcode'))->first();

        $validator=Validator::make($request->all(), [
            'wristband_id' => 'required',
            'qrcode' => 'required'
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
        if (!$p) {
            return response()->json(['message' => 'Data not found','code'=>Response::HTTP_NOT_FOUND],Response::HTTP_NOT_FOUND);
        }
        else{
        $plan = Input::except('_method','_token');
        $plan['wristband_id'] = $request->wristband_id;
        $p->fill($plan)->save();
        return response()->json(['message' => ''.$p->name.' Account Update Successfully','code'=>Response::HTTP_OK],Response::HTTP_OK);
    }
    }
}
 public function updateActivities(Request $request,$id)
    {

        $p = activities::find($id);
        $validator=Validator::make($request->all(), [
            'name' => 'required|unique:activities,name,'.$p->id,
            'description' => 'required',
            'status' => 'required|integer'
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
        if (!$p) {
            return response()->json(['message' => 'Data not found','code'=>Response::HTTP_NOT_FOUND],Response::HTTP_NOT_FOUND);
        }
        else{
        $plan = Input::except('_method','_token');
        $plan['name'] = $request->name;
        $plan['status'] = $request->status;
        $plan['description'] = $request->description;
        $p->fill($plan)->save();
        return response()->json(['message' => 'Activities Update Successfully','code'=>Response::HTTP_OK],Response::HTTP_OK);
    }
    }
}
	public function deleteActivities(Request $request)
    {

        $p = activities::find($request->id);
        $validator=Validator::make($request->all(), [
            'id' => 'required'
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
        if (!$p) {
            return response()->json(['message' => 'Data not found','code'=>Response::HTTP_NOT_FOUND],Response::HTTP_NOT_FOUND);
        }
        else{
            activities::destroy($request->id);
        return response()->json(['message' => 'Activities Deleted Successfully','code'=>Response::HTTP_OK],Response::HTTP_OK);
    }
}
    }

    public function getUserInfo(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'qrcode' => 'required|string|max:255'
        ]);
        if($validator->fails())
        {

              return response()->json([
              "code"  =>  '400',
              "type"  => "invalid",
              "message"  =>  "invalid_credentials",
              "developerMessage"  => $validator->messages(),
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        else {
 $user=User::with(['activities'])->where('qrcode', $request->get('qrcode'))->orWhere('phone', $request->get('qrcode'))->orWhere('wristband_id', $request->get('qrcode'))->first();

      if(!$user)
      {
        return response()->json([
            "code"  =>  Response::HTTP_NOT_FOUND,
            "message"  =>  "invalid Ticket",
          ],Response::HTTP_NOT_FOUND);

      }else {

  return response()->json(['data' => $user,'code'=>Response::HTTP_OK],Response::HTTP_OK);



        }
    }

    }

    public function validateQrcode(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'qrcode' => 'required|string|max:255',
            'activities' => ['required', 'string']
        ]);
        if($validator->fails())
        {

              return response()->json([
              "code"  =>  '400',
              "type"  => "invalid",
              "message"  =>  "invalid_credentials",
              "developerMessage"  => $validator->messages(),
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        else {
 $user=User::where('qrcode', $request->get('qrcode'))->orWhere('phone', $request->get('qrcode'))->first();

      if(!$user)
      {
        return response()->json([
            "code"  =>  Response::HTTP_NOT_FOUND,
            "message"  =>  "invalid Ticket",
          ],Response::HTTP_NOT_FOUND);

      }else {
        $time=Carbon::now();
         $user_activitie=user_activities::where('user_id', $request->get('qrcode'))->orWhere('user_id', $user->qrcode)->first();
  foreach ($user_activitie['description'] as $key => $value) {
    $time1=Carbon::parse($user_activitie['updated_at'])->format('Y-m-d');
    $time2=Carbon::today()->toDateString();
     if ($time1 == $time2 && $value['activities']==$request->activities) {
                   return response()->json(['data' => 'Ticket Already Checked In for this activitie '.$request->activities.' Today ','code'=>Response::HTTP_ALREADY_REPORTED],Response::HTTP_ALREADY_REPORTED);
     }

  }



  $array_data = $user_activitie['description'];
  array_push($array_data,[
      'activities'=>$request->activities,
    'time' => $time->toDateTimeString(),
      ],);
  $user_activitie->update(['description' => $array_data]);
  $user_activitie->save();



$message ='Welcome '.$user->name.'  has been successfully Checked in for '.$request->activities .' Activities';

  return response()->json(['data' => $message,'code'=>Response::HTTP_OK],Response::HTTP_OK);



        }
    }


    }



        public function checkin(Request $request)
      {
          $this->validate($request, [
            'name' => 'required|string|max:255',
             'id' => 'required',

        ]);

        $items =  reservation_attendance::updateOrCreate(array(
           'attend' =>'1',
           'ivcode'=>$request->id,
           'author'=>Auth::user()->id
          ));


             $message ='ok';

           return response()->json([
              'error' => '1',
              'status'  => $message,
           ], 200);



      }



}
