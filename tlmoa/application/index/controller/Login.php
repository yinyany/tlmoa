<?php
namespace app\index\controller;

use think\Controller;
use think\Validate;
use app\index\model\User;

class Login extends Controller
{

	// 执行用户登录
    public function doLogin()
    {
    	// 验证表单信息
    	$rule = [
		    ['name','require|max:25','用户名必填|用户名最长为25个字符'],
		   	['password','require','密码必填'],
		   	['captcha','require|captcha','验证码必填|验证码错误']
		];
		$validate = new Validate($rule);
		$result   = $validate->check(input('post.'));
		if(true !== $result){
		    // 验证失败 输出错误信息
		    session('error', $validate->getError());
		    // 跳转至登录页
		     $this->redirect('index/index');
		}
		// 验证登录信息
		if(User::code(input('post.'))){
			// 验证成功，跳转至首页
			$this->redirect('index/index');
		}else{
			// 重定向至登录页
			 $this->redirect('index/index');
		}
    }

    //退出登录，清除session
    public function loginout(){
    	// 清除session信息
    	session('userInfo',null);
    	// 重定向至登录页
    	 $this->redirect('index/index');
    }   
}
