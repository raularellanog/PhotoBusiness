<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Session;

class HomeController extends Controller
{
    public function index(Request $request){

        $result['title'] = 'Home';
        $result['description'] = 'Las mejores fotos de los espectáculos';
        $result['generalData'] = $this->generalData();
        $result['isCoverPhoto'] = true;
        $result['photos'] = $this->getAlbum(50);
        $result['photosTop'] = $this->getAlbumsTop();
        $result['page'] = 'home';
        return view('web/home')->with('result', $result);
    }

    public function collections(){
        $result['title'] = 'Colecciones';
        $result['description'] = 'Colecciones';
        $result['generalData'] = $this->generalData();
        $result['photos'] = $this->getAlbum(null);
        $result['isCoverPhoto'] = true;
        $result['page'] = 'collections';
        return view('web/collections')->with('result', $result);;
    }

    public function exclusives(){
        $result['title'] = 'Exclusivas';
        $result['description'] = 'Exclusivas';
        $result['generalData'] = $this->generalData();
        $result['isCoverPhoto'] = true;
        $result['page'] = 'exclusive';
        return view('web/collections')->with('result', $result);;
    }

    public function politicas(){
        $result['title'] = 'Políticas de privacidad';
        $result['description'] = 'Políticas de privacidad';
        $result['generalData'] = $this->generalData();
        $result['page'] = null;
        return view('web/politicas')->with('result', $result);;
    }

    public function quienesSomos(){
        $result['title'] = 'Quienes Somos';
        $result['description'] = 'Quienes Somos';
        $result['generalData'] = $this->generalData();
        $result['page'] = null;
        return view('web/quienessomos')->with('result', $result);;
    }

    public function contact(){
        $result['title'] = 'Contacto';
        $result['description'] = 'Contacto';
        $result['generalData'] = $this->generalData();
        $result['page'] = null;
        return view('web/contact')->with('result', $result);;
    }

    public function album($idAlbum, $nameAlbum){
        $result['title'] = $nameAlbum;
        $result['description'] = $nameAlbum;
        $result['generalData'] = $this->generalData();
        $result['photos'] = $this->getImagesAlbum($idAlbum);
        $result['dataAlbum'] = $this->getDataAlbum($idAlbum);
        $result['isCoverPhoto'] = false;
        $result['page'] = 'collections';
        return view('web/album')->with('result', $result);;
    }

    public function comprar(){
        $result['title'] = 'Compara imágen';
        $result['description'] = 'Imágenes relacionadas';
        $result['generalData'] = $this->generalData();
        $result['isWideImage'] = false;
        $result['page'] = null;
        $result['isCoverPhoto'] = false;
        return view('web/comprar_imagen')->with('result', $result);;
    }

    public function myaccount(){
        $result['title'] = 'Mi cuenta';
        $result['description'] = 'Historial de descargas';
        $result['page'] = null;
        $result['isCoverPhoto'] = true;
        $result['generalData'] = $this->generalData();
        return view('web/myaccount')->with('result', $result);
    }

    public function shoppingcart(){
        $result['title'] = 'Mi cuenta';
        $result['page'] = null;
        $result['generalData'] = $this->generalData();
        return view('web/shoppingcart')->with('result', $result);
    }

    function generalData(){
        $generalData['isLogin'] = session('isLogin');
        $generalData['hasExclusives'] = true;
        return $generalData;
    }

    function login(Request $request){

        try {
            $dataUser = DB::table('users')->where('email', '=', $request->get('recipientEmail'))->first();
            if(isset($dataUser)){
                $credentials = ['email' => trim($request->recipientEmail), "password" => trim($request->recipientPass)];
                if (Auth::attempt($credentials)){
                    session(["isLogin" => true]);
                }else{
                    session(["isLogin" => false]);
                }
            }else{
                $request->session()->put('isLogin', false);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            dd($ex->getMessage());
        }
        return back();
    }

    function logout(){
        Session::flush();
        Auth::logout();
        return back();
    }

    function getAlbum($limit){

        $DBAlbum = DB::table('images')
        ->select('images.id_album', 'images.image_path', 'image_with',
        'image_height', 'albums.id', 'albums.album_name', 'albums.number_photos', 'albums.date')
        ->join('albums', 'images.id_album', '=', 'albums.id')
        ->distinct()->get();

        $dataAlbums = array($DBAlbum['0']);

        for ($i = 0; $i <= count($DBAlbum)-1; $i++) {
            if($dataAlbums[count($dataAlbums)-1]->id_album != $DBAlbum[$i]->id_album){
                array_push($dataAlbums, $DBAlbum[$i]);
            }
        }

        return $dataAlbums;
    }

    function getAlbumsTop(){
        $DBAlbum = DB::table('images')
        ->select('images.id_album', 'images.image_path', 'images.image_with',
        'images.image_height', 'albums.id', 'albums.album_name', 'albums.number_photos', 'albums.date', 'albums.albums_top')
        ->join('albums', 'images.id_album', '=', 'albums.id')
        ->whereIn('albums.albums_top', [1, 2])
        ->orderBy('albums.albums_top', 'asc')
        ->distinct()->get();

        $dataAlbums = array();

        for ($i = 0; $i <= count($DBAlbum)-1; $i++) {
            if($DBAlbum[$i]->albums_top == 1 && $DBAlbum[$i]->image_with >= $DBAlbum[$i]->image_height && empty($dataAlbums)){
                array_push($dataAlbums, $DBAlbum[$i]);
                continue;
            }

            if($DBAlbum[$i]->albums_top == 2 && $DBAlbum[$i]->image_with < $DBAlbum[$i]->image_height){
                array_push($dataAlbums, $DBAlbum[$i]);
                break;
            }
        }

        return $dataAlbums;
    }

    function getImagesAlbum($idAlbum){
        $DBAlbum = DB::table('images')->where('id_album', '=', $idAlbum)->get();
        return $DBAlbum;
    }

    function getDataAlbum($idAlbum){
        $DBAlbum = DB::table('albums')->where('id', '=', $idAlbum)->first();
        return $DBAlbum;
    }
}
