<?php
namespace app\index\model;

use think\Model;

class Customer extends Model
{
    //获取所有客户信息(条件搜索客户信息)
    static function getCustomerList($where)
    {
        if ($where['type'] == '' && $where['val'] == '') 
        {
            return Customer::order('id', 'asc')->paginate(6);
        }else{
            return Customer::where($where['type'],'like',"%".$where['val']."%")->paginate(6,false,['query'=>request()->param()]);
        }
    } 

    //获取客户数量(条件搜索客户数量)
    static function getCustomerCount($where)
    {
        if ($where['type'] == '' && $where['val'] == '') {
           return Customer::count();
        }else{
            return Customer::where($where['type'],'like',"%".$where['val']."%")->count();
        }
    }

    //添加客户
    static function setCustomerList($post)
    {
        foreach ($post as $k => $v) {
            if (!$post[$k]) {
                unset($post[$k]);
            }
        }
		$Customer = new Customer($post);
		// 过滤post数组中的非数据表字段数据
        return $Customer->allowField(true)->save();
    }

    //编辑客户
    static function edCustomerList($post)
    {
        foreach ($post as $k => $v) {
            if (!$post[$k]) {
                unset($post[$k]);
            }
        }
		$Customer = new Customer();
		return $Customer->allowField(true)->save($post, ['id' => $post['id']]);
    }

    //删除客户
    static function delCustomer($id)
    {
    	return Customer::destroy($id);
    }
}