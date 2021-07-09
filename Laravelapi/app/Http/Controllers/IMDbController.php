<?php

namespace App\Http\Controllers;

use App\ViewModels\MoviesViewModel;
use App\ViewModels\MovieViewModel;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class IMDbController extends Controller
{
   public function comingsoonmovies(){

    $upcomingMovies = Http::withToken(config('services.tmdb.token'))
    ->get(config('services.tmdb.base_url').'movie/upcoming')
    ->json()['results'];
    $nowPlayingMovies = Http::withToken(config('services.tmdb.token'))
    ->get('https://api.themoviedb.org/3/movie/now_playing')
    ->json()['results'];
    $genres = Http::withToken(config('services.tmdb.token'))
    ->get('https://api.themoviedb.org/3/genre/movie/list')
    ->json()['genres'];

   
    if (!$upcomingMovies) {
      return response()->json([
              "code"  =>  Response::HTTP_NOT_FOUND,
              "message" => "Resource Not Available.",
              "developerMessage"=> $upcomingMovies,
             ],Response::HTTP_NOT_FOUND);
      } else {
       $data= new MoviesViewModel(
          $upcomingMovies,$nowPlayingMovies,
          $genres,
      );
$result=array([
'upcomingMovie'=>$data->upcomingMovies(),
'nowPlayingMovies'=>$data->nowPlayingMovies(),
'genres'=>$data->genres(),

]);
        return response()->json([
            "code"  =>  Response::HTTP_OK,
            "data" => $result
           ],Response::HTTP_OK);
    }


   }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/movie/'.$id.'?append_to_response=credits,videos,images')
            ->json();

        $data = new MovieViewModel($movie);

        return response()->json([
          "code"  =>  Response::HTTP_OK,
          "developerMessage" => $data
         ],Response::HTTP_OK);
    }



}
