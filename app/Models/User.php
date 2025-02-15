<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Libs\helpers;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $help;
    public function __construct()
    {
        $this->help = new helpers();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function create($data)
    {
        try {
            $data = is_object($data) ? (array)$data : $data;
            $id = DB::table('users')->insertGetId([
                'user_name' => $this->help->get_slug(trim($data['user_name'])),
                'first_name' => ucwords(trim($data['first_name'])),
                'last_name' => ucwords(trim($data['last_name'])),
                'email' => trim($data['email']),
                'password' =>  Hash::make(trim($data['password'])),
                'id_role' => isset($data['role_id']) ? $data['role_id'] : 0,
                'company_id' => 0,
                'users_online' => 0,
                'users_max_online' => isset($data['users_max_onlien']) ? $data['users_max_onlien'] : null,
                'download_numbers' => isset($data['download_numbers']) ? $data['download_numbers'] : -1,
                'max_download_numbers'=>999999999,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return $id;
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
            return 0;
        }
    }
    public function edit($data)
    {
        try {
            $data = is_object($data) ? (array)$data : $data;
            $id = DB::table('users')->where('id', $data['id'])->update([
                'user_name' =>  $this->help->get_slug(trim($data['user_name'])),
                'first_name' => ucwords(trim($data['first_name'])),
                'last_name' => ucwords(trim($data['last_name'])),
                'email' => trim($data['email']),
                // 'password' => isset($data['password']) && $data['password'] != null ? Hash::make(trim($data['password'])) : null,
                'id_role' => isset($data['role_id']) ? $data['role_id'] : 0,
                'status' =>  isset($data['status']) ? $data['status'] : 'A',
                'updated_at' => date('Y-m-d H:i:s'),
                // 'users_onlien' => isset($data['users_onlien']),
                // 'users_max_onlien' => isset($data['users_max_onlien']) ? $data['users_max_onlien'] : null,
                // 'download_numbers' => isset($data['download_numbers']) ? $data['download_numbers'] : -1,
            ]);
            return $data['id'];
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }
    public function update_config($data)
    {
        try {
            $data = is_object($data) ? (array)$data : $data;
            $id = DB::table('users')->where('id', $data['id'])->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'users_max_onlien' => isset($data['users_max_onlien']) ? $data['users_max_onlien'] : null,
                // 'users_onlien' => isset($data['users_onlien']),
                // 'download_numbers' => isset($data['download_numbers']) ? $data['download_numbers'] : -1,
            ]);
            return $data['id'];
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }
    public function change_pass($id, $pass)
    {
        try {
            $change = DB::table('users')->where('id', $id)->update([
                'password' => Hash::make(trim($pass)),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return  $id;
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public function verify_sessions()
    {
        try {
            session(['logout' => false]);
            $id_user = session('user_id');
            if ($id_user) {
                $user = DB::table('user')->where('id', $id_user)->first();
                if ($user->users_max_onlien != null && $user->users_max_onlien < 0) {
                    if ($user->users_max_onlien > $user->users_onlien) {
                        session(['logout' => true]);
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function add_session($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
            $update = DB::table('users')->where('id', $id)->update([
                'users_onlien' => inval($user->users_onlien) + 1
            ]);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
    public function res_session($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
            $valor =  inval($user->users_onlien) - 1;
            if ($valor < 0) {
                $valor = 0;
            }
            $update = DB::table('users')->where('id', $id)->update([
                'users_onlien' => $valor,
            ]);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}
