<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\controller\Customers;


class Index extends Controller
{
    //检测是否登录
    public function index()
    {
        // 如果没有用户信息，加载登录页面
        if(session('?userInfo') == false){
        	return $this->fetch('index/login');
        }else{
            $Customers = new Customers();
            $num = $Customers->getCustomerNum();
            $this->assign(['num'=>$num]);
            // dump($_SERVER);die;
    		// 模板输出
            return $this->fetch('index/index');
        }
    }
}
