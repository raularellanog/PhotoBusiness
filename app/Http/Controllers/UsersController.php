<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use  App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{

    public function  postLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = ['email' => trim($request->email), "password" => trim($request->password)];
            if (Auth::attempt($credentials)) {
                $user = DB::table('users')->where('id', Auth::id())->first();
                $this->user_visit($user->id);
                session(['id_user' => Auth::id()]);
                session(['id_role' => $user->id_role]);
                session(['id_company' => $user->company_id]);
                if ($user->id_role) {
                    return redirect()->route('admin.dashboard')
                        ->withSuccess('You have Successfully loggedin');
                } else {
                }
            }
            return redirect()->route('admin.login')->withSuccess('Oppes! You have entered invalid credentials');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('admin.login')->withSuccess('Oppes! You have entered invalid credentials');
        }
    }
    public function user_visit($user_id)
    {
        try {
            $id = DB::table('user_visit')->insertGetId([
                'user_id' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return $id;
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }
    public function postRegister(Request $request)
    {
        try {
            $userModel = new User();
            // $request->validate([
            //     'name' => 'required',
            //     'email' => 'required|email|unique:users',

            //     'password' => 'required|min:6',
            // ]);
            $data['name'] = "Raul Arellano";
            $data['email'] = "raularellanoguevara@gmail.com";
            $data['password'] = "123456789";
            // $data = $request->all();
            $check = $userModel->create($data);
            dd("termino");
        } catch (\Throwable $th) {
            dd($th);
            throw $th;
        }
    }
    public function existsEmail($email = null)
    {
        try {
            if ($email != null) {
                $exists = DB::table('users')->where('email', trim($email))->first();
                if (!$exists) {
                    return true;
                }
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function index()
    {
        $result['users'] = DB::table('users')->get();
        return view('admin.users.index')->with('result', $result);
    }
    public function add()
    {
        $result = array();
        $result['breadcrumb'] = array();
        array_push($result['breadcrumb'], ['title' => 'Usuarios', 'url' => url('admin/users')]);
        array_push($result['breadcrumb'], ['title' => ' Agregar Usuario', 'url' => url('admin/users/add')]);
        return view('admin.users.add')->with('result', $result);
    }
    public function edit($id)
    {
        $result['breadcrumb'] = array();
        array_push($result['breadcrumb'], ['title' => 'Usuarios', 'url' => url('admin/users')]);
        array_push($result['breadcrumb'], ['title' => ' Editar Usuario', 'url' => url('admin/users/edit', $id)]);
        return view('admin.users.edit')->with('result', $result)->with('id',$id);
    }

    public function logout()
    {
        $role = session('id_role');
        Session::flush();
        Auth::logout();
        if ($role == 1) {
            return redirect()->route('admin.login');
        }
        return Redirect('login');
    }

    public function update(Request $request)
    {
        try {
            $modelUser = new User();
            $request->validate([
                'id' => 'required',
            ]);
            $user_id = $modelUser->edit($request->all());
            if ($user_id) {
                return redirect()->back()->with('success', true);
            }
            return redirect()->back()->with('error', true);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('error', true);
        }
    }
    public function update_pass(Request $request)
    {
        try {
            $modelUser = new User();
            $request->validate([
                'id' => 'required',
                'password' => 'required',
                'password_rep' => 'required|same:password'
            ]);
            $user_id = $modelUser->change_pass($request->id, $request->password);
            if ($user_id) {
                return redirect()->back()->with('success', true);
            }
            return redirect()->back()->with('error', true);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('error', true);
        }
    }
    public function update_config(Request $request)
    {
        try {
            $modelUser = new User();
            $request->validate([
                'id' => 'required',
            ]);
            $user_id = $modelUser->update_config($request->all());
            if ($user_id) {
                return redirect()->back()->with('success', true);
            }
            return redirect()->back()->with('error', true);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('error', true);
        }
    }
    public function insert(Request $request)
    {
        $modelUser = new User();
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $exists = DB::table('users')->where('email', trim($request->email))->first();
        if (!$exists) {
            $user_id = $modelUser->create($request->all());
            if ($user_id) {
                return redirect()->back()->with('success', true);
            }
        }
        return redirect()->back()->with('error', true);
    }
}
