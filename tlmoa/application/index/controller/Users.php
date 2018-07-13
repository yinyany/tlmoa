<?php
namespace app\index\controller;

use think\Controller;
use think\Validate;
use app\index\model\User;
use app\index\controller\Base;

class Users extends Base
{
	//获取用户的所有信息,查看用户总数,加载用户列表
    public function indexs()
    {   
        if (isset($_GET['name'])) {
            $where = $_GET['name'];
        }else{
            $where = '';
        }
    	$userlist =  User::getUsersList($where);
    	$count = User::getUsersCount($where);
    	$this->assign(['userlist'=>$userlist,'count'=>$count,'where'=>$where]);
    	// 模板输出
      	return $this->fetch('index/userList');
    }

    // 根据id删除用户
    public function deleteUser($id)
    {
    	$num =  User::delUser($id);
    	// dump($num);
        if ($num === false) {
            session('message','超级用户不能删除');
            $this->redirect('Users/indexs');
        }else{
            if ($num > 0) {
            session('message','用户删除成功');
            $this->redirect('Users/indexs');
            }else{
                session('message','用户删除失败');
                $this->redirect('Users/indexs');
            }
        }
    }

    // 加载添加用户页面
    public function viewCreateUser()
    {
    	return view('index/userAdd');
    }
    
    //创建新用户
    public function createUserList()
    {
        // 验证表单信息
        $rule = [
            ['name','require|max:25|unique:user','用户名必填|用户名最长为25个字符|此用户名已存在'],
            ['phone','require','手机号必填'],
            ['password','require|min:6|confirm','密码必填|密码最少为6位字符|两次输入密码不同']
        ];
        $validate = new Validate($rule);
        $result   = $validate->check(input('post.'));
        if(true !== $result){
            // 验证失败 输出错误信息
            session('message', "用户添加失败:".$validate->getError());
            // 跳转至登录页
            $this->redirect('Users/viewCreateUser');
        }
        $_POST['password'] = md5($_POST['password']);
    	$num = User::setUserList($_POST);
    	if ($num >0) {
    		session('message','用户添加成功');
           	$this->redirect('Users/indexs');
    	}else{
    		session('message','用户添加失败');
    		$this->redirect('Users/indexs');
    	}
    }
    
    // 加载编辑用户页面
    public function viewEditUser($id){
		// 查询单个数据
		$userlist = User::where('id',$id)->find();
    	return view('index/userEdit',['userlist'=>$userlist]);
    }

    //编辑用户
    public function editUserList(){
        $rule = [
            ['phone','require','手机号必填'],
            ['password','require|min:6|confirm','密码必填|密码最少为6位字符|两次输入密码不同']
        ];
        $validate = new Validate($rule);
        $result   = $validate->check(input('post.'));
        if(true !== $result){
            // 验证失败 输出错误信息
            session('message', "用户信息修改失败:".$validate->getError());
            // 跳转至登录页
            $this->redirect('Users/viewEditUser',['id'=>input('post.id')]);
        }
    	$num = User::edUserList($_POST);
        $priveurl =  input('post.ref_url');
    	if ($num >0) {
    		session('message','用户信息修改成功');
            $this->redirect($priveurl);
        }else{
            session('message','用户信息修改失败');
           	$this->redirect($priveurl);
    	}
    }
}
