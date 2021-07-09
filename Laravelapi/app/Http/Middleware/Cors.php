<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        return $next($request)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', '*')
        ->header('Access-Control-Allow-Credentials', true)
        ->header('Access-Control-Allow-Headers', 'X-Requested-With,Content-Type,X-Token-Auth,Authorization,X-Authorization,Origin')
        ->header('Accept', 'application/json');
    }

    // public function handle($request, Closure $next){
    //     header('Access-Control-Allow-Origin', '*');
    //     //ALLOW OPTIONS METHOD
    //     $headers = [
    //     'Access-Control-Allow-Origin' => '*',
    //     'Access-Control-Allow-Methods' => '*',
    //     'Accept'=> 'application/json',
    //     'Access-Control-Allow-Credentials' => true,
    //     'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization,X-Authorization',
    //     ];
    //     if ($request->getMethod() == 'OPTIONS') {
    //     //The client-side application can set only headers allowed in Access-Control-Allow-Headers
    //     return response()->json('OK', 200, $headers);
    //     }
    //     $response = $next($request);
    //     foreach ($headers as $key => $value) {
    //     $response->header($key, $value);
    //     }
    //     return $response;
    //     }
}
