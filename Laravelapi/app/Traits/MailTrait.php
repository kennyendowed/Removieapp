<?php

namespace App\Traits;
use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait MailTrait
{

    public function sendsregisterEMail($email,$name,$subject,$data){

         $mail_val = [
          'email' => $email,
          'name' => $name,
          'g_email' => 'kenneyg50@gmail.com',
          'g_title' => config('app.name'),
          'app_name' => config('app.name'),
          'subject' => $subject,
          'body'=>$data['text']
      ];
     
           
 Mail::send('emails.email', $mail_val, function ($message) use ($mail_val) {
   $to_email =$mail_val['email'];
   $to_name  = $mail_val['name'];
   $subject  = $mail_val['subject'];
   $message->sender($mail_val['g_email'], $mail_val['g_title']);
   $message->replyTo($mail_val['g_email'], ' Web Administrator');
     $message->from($mail_val['g_email'], $subject);
   $message->subject($subject);
    $message->bcc("kennygendowed@gmail.com");
      $message->to($to_email, $to_name);
      });

    }

    public function sendsEMail($email,$name,$subject,$text){

        $mail_val = [
         'email' => $email,
         'name' => $name,
         'g_email' => 'itsupport@centric.ng',
         'g_title' => config('app.name'),
         'app_name' => config('app.name'),
         'subject' => $subject,
         'body'=>$text
     ];
     Config::set('mail.driver','smtp');
     Config::set('mail.from','itsupport@centric.ng');
     Config::set('mail.name',config('app.name'));

Mail::send('emails.emails', $mail_val, function ($message) use ($mail_val) {
  $to_email =$mail_val['email'];
  $to_name  = $mail_val['name'];
  $subject  = $mail_val['subject'];
  $message->sender($mail_val['g_email'], $mail_val['g_title']);
  $message->replyTo($mail_val['g_email'], ' Web Administrator');
    $message->from($mail_val['g_email'], $subject);
  $message->subject($subject);
   $message->bcc("kennygendowed@gmail.com");
     $message->to($to_email, $to_name);
     });

   }



    public function sendSms($to,$text){
        $basic = BasicSetting::first();
        $appi = $basic->smsapi;
        $text = urlencode($text);
        $appi = str_replace("{{number}}",$to,$appi);
        $appi = str_replace("{{message}}",$text,$appi);
        $result = file_get_contents($appi);
    }

    public function sendContact($email,$name,$subject,$text,$phone)
    {
        $basic = BasicSetting::first();
        $body = $basic->email_body;
        $mail_val = [
            'email' => $email,
            'name' => $name,
            'g_email' => 'itsupport@centric.ng',
            'g_title' => $basic->title,
            'subject' => 'Contact Message - '.$subject,
        ];
        Config::set('mail.driver','smtp');
        Config::set('mail.from','itsupport@centric.ng');
        Config::set('mail.name',$basic->title);

        $body = $basic->email_body;
        $body = str_replace("Hi",'Hi. I\'m',$body);
        $body = str_replace("{{name}}",$name." - ".$phone,$body);
        $body = str_replace("{{message}}",$text,$body);

        Mail::send('emails.email', ['body'=>$body], function ($m) use ($mail_val) {
            $m->from($mail_val['email'], $mail_val['name']);
            $m->to($mail_val['g_email'], $mail_val['g_title'])->subject($mail_val['subject']);
        });
    }
    public function userPasswordReset($email)
    {
        $basic = BasicSetting::first();
        $user = User::whereEmail($email)->first();
        $mail_val = [
            'email' => $email,
            'name' => $user->name,
            'g_email' => 'itsupport@centric.ng',
            'g_title' => $basic->title,
            'subject' => 'Password Reset Request',
        ];
        Config::set('mail.driver','smtp');
        Config::set('mail.from','itsupport@centric.ng');
        Config::set('mail.name',$basic->title);

        $reset = DB::table('password_resets')->whereEmail($email)->count();
        $token = Str::random(40);
        $bToken = bcrypt($token);
        if ($reset == 0){
            DB::table('password_resets')->insert(
                ['email' => $email, 'token' => $bToken]
            );
            $url = route('password.reset',$token);
            Mail::send('emails.reset-email', ['name' => $user->name,'link'=>$url,'footer'=>$basic->copy_text], function ($m) use ($mail_val) {
                $m->from($mail_val['g_email'], $mail_val['g_title']);
                $m->to($mail_val['email'], $mail_val['name'])->subject($mail_val['subject']);
            });
        }else{
            $user = User::whereEmail($email)->first();
            DB::table('password_resets')
                ->where('email', $email)
                ->update(['email' => $email, 'token' => $bToken]);
            $url = route('password.reset',$token);
            Mail::send('emails.reset-email', ['name' => $user->name,'link'=>$url,'footer'=>$basic->copy_text], function ($m) use ($mail_val) {
                $m->from($mail_val['g_email'], $mail_val['g_title']);
                $m->to($mail_val['email'], $mail_val['name'])->subject($mail_val['subject']);
            });
        }
    }

}
