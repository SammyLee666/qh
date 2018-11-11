<?php

namespace App;

use App\Library\common;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'available_balance', //可用余额
        'freeze_balance',  //冻结余额
        'promotion_code',  //推广码
        'status',  //0正常
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    static public function createUser(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['promotion_code'] = common::promotionCode();
        QrCode::format('png')->generate($data['promotion_code'], public_path('static/users/qrcodes/' . $data['promotion_code'] . '.png'));
        self::create($data);
    }


    /**
     * @param array $data
     * @return mixed
     */
    static protected function validator(array $data)
    {
        return \Validator::make($data, [
            'phone' => 'required|regex:/^1[34578]\d{9}$/|unique:users',
            'password' => 'required|min:6|confirmed',
        ], [
            'phone.required' => '手机号必填!',
            'phone.regex' => '手机号格式不正确!',
            'phone.unique' => '手机号已经存在!',
            'password.required' => '密码必填!',
            'password.min' => '密码最短6位!',
            'password.confirmed' => '密码两次输入不一致!',
        ]);

    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
