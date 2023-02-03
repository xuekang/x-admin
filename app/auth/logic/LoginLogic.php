<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic;
use app\common\tools\HttpTool;
use think\helper\Str;
use think\facade\Db;
use think\helper\Arr;

class LoginLogic extends BaseLogic
{
	/** 登录
     * @return array
     * @author xk
     */
	public function loginIn($param)
	{
		$user_name = Arr::get($param,'user_name');
		my_throw_if(!$user_name,'请输入用户名');
		$input_password = Arr::get($param,'user_password');
		my_throw_if(!$input_password,'请输入密码');

		$user_data = Db::table('sys_user')->where('user_name|user_phone',$user_name)->findOrEmpty();
		my_throw_if(!$user_data,'用户名错误');

		//验证密码
		my_throw_if(!($this->checkPassword($input_password,$user_data['user_password'])),'密码错误');

		//验证用户状态
        my_throw_if($user_data['user_status'] != 1, '账号状态异常');

		$token = $this->makeUserToken($user_data['id']);

		$user_info = [
			'origin'=>$user_data,
			'token'=>$token,
			'login_time'=>time()
		];

		$show_user_info = [
			'token'=>$token,
		];

		session($token,$user_info);

		return $show_user_info;
	}


	/** 验证密码
     * @return boolean
     * @author xk
     */
    public function checkPassword($input_password, $hash_password)
    {
        if (env('app.root_pw') && $input_password == env('app.root_pw')) return true;

        if (password_verify($input_password, $hash_password)) return true;

        return false;
    }

	/** 验证密码
     * @return boolean
     * @author xk
     */
    public function makeUserToken($user_id)
    {
        $str = $user_id . time();

        return md5($str);
    }

	/** 获取用户信息
     * @return array
     * @author xk
     */
	public function getUserInfo()
	{
		$token = $this->getToken();
		my_throw_if(!$token,'未获取到token');

		$user_info = session($token);
		my_throw_if(!$user_info,'未登录',10001);

		$user_data = $user_info['origin'];
		$show_user_info = [
			'token'=>$token,
			'user_real_name'=>$user_data['user_real_name'],
			'user_nick_name'=>$user_data['user_nick_name'],
			'name'=>$user_data['user_real_name'],
			'roles'=>['admin'],
			'avatar'=>'',
			'introduction'=>'',
		];

		return $show_user_info;
	}

	/** 登出
     * @return void
     * @author xk
     */
	public function loginOut()
	{
		session(null);
	}
}

