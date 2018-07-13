<?php
namespace app\index\controller;

use think\Controller;
class Base extends Controller
{
    public function _initialize(){
        // 如果没有用户信息，加载登录页面
        if(session('?userInfo') == false){
            $this->redirect('index/index');
        }
    }
}
