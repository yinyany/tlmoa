<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
    // 性别获取器
	public function getSexAttr($sex)
    {
        $sexs = ['w'=>'女','m'=>'男'];
        return $sexs[$sex];
    }

    // 权限获取器
    public function getRoleAttr($role)
    {
        $roles = [1=>'超级用户',2=>'普通用户'];
        return $roles[$role];
    }

	// 登录用户信息验证
	static function code($userInfo){
		// 获取输入的用户名
		$name = $userInfo['name'];
		// 数据库查找当前用户的信息
		$list = User::where('name',$name)->find();
		// 进行密码判断
		if($list['password'] !== md5($userInfo['password'])){
			// 验证失败，输出错误信息
			session('error','账号或密码错误');
			return false;
		}else{
			// 判断正确，存储用户信息
			session('userInfo',$list);
			return true;
		}
	}

    //获取所有用户信息(条件搜索用户信息)
    static function getUsersList($where)
    {
        if ($where == '') {
            return User::order('id', 'asc')->paginate(6);
        }else{
            return User::where('name','like',$where)->order('id', 'asc')->paginate(6,false,['query'=>request()->param()]);
        }
    }
        
    //获取所有用户总数(条件搜索用户数量)
    static function getUsersCount($where)
    {
        if ($where == '') {
           return User::count();
        }else{
            return User::where('name','like',$where)->count();
        }
        
    }

    //添加用户
    static function setUserList($post)
    {
		$user = new User($post);
		// 过滤post数组中的非数据表字段数据
		return $user->allowField(true)->save();
    }

    //编辑用户
    static function edUserList($post)
    {
		$user = new User();
		$post['password'] = md5($post['password']);
		// post数组中只有phone和password字段会写入
		return $user->allowField(['phone','password'])->save($post, ['id' => $post['id']]);
    }

    //删除用户
    static function delUser($id)
    {
        $list = User::where('id',$id)->find();
        // dump($list->getData('role'));die;
        if ($list->getData('role') == '1') {
            return false;
        }else{
            return User::destroy($id);
        }
    }
}