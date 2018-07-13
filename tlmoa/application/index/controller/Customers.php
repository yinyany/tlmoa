<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Customer;
use app\index\controller\Base;


class Customers extends Base
{
	//获取客户的所有信息,查看客户总数,加载客户列表
    public function index()
    {   
        $customerlist = Customer::order('id', 'asc')->paginate(6);
        $count = Customer::count(); 
    	$this->assign(['customerlist'=>$customerlist,'count'=>$count]);
    	// 模板输出
      	return $this->fetch('index/customerList');
    }

    //加载客户搜索页面
    public function viewSearchCustomer()
    {   
        $where['type'] = input('get.type','');
        $where['val'] = input('get.val','');
        if ($where['val'] != '' && $where['type'] == '') {
            session('message','请选择搜索类别');
            $this->redirect('Customers/viewSearchCustomer');
        }
        $customerlist =  Customer::getCustomerList($where);
        $count = Customer::getCustomerCount($where);
        $this->assign(['customerlist'=>$customerlist,'count'=>$count,'where'=>$where]);
        return view('index/customerSearch');
    }

    //获取首页数据
    public function getCustomerNum(){
        $num['sum'] = Customer::count(); //客户总数
        $num['hg'] = Customer::where(['checkout'=>'合格'])->count();//已合格
        $num['dqr'] = Customer::where(['checkout'=>'待确认'])->count();//待确认
        $num['nhg'] = Customer::where(['checkout'=>'不合格'])->count();//不合格
        return $num;
    }

    // 根据id删除客户
    public function deleteCustomer($id)
    {   
    	$num =  Customer::delCustomer($id);
    	if ($num > 0) {
    		session('message','客户删除成功');
           	$this->redirect('Customers/index');

    	}else{
    		session('message','客户删除失败');
    		$this->redirect('Customers/index');
    	}
    }

    // 加载添加客户页面
    public function viewCreateCustomer()
    {
    	return view('index/customerAdd');
    }
    
    //创建新客户
    public function createCustomerList()
    {   
        if (array_key_exists('imgurl',$_POST)) {
            $_POST['imgurl'] = implode(";", $_POST['imgurl']);
        }
        // dump($_POST);die;
    	$num = Customer::setCustomerList($_POST);
    	if ($num >0) {
    		session('message','客户添加成功');
           	$this->redirect('Customers/index');
    	}else{
    		session('message','客户添加失败');
    		$this->redirect('Customers/index');
    	}
    }

    //图片上传
    public function doUpload()
    {
        $pathName  =  $this->request->param('path');//图片存放的目录
        $file = request()->file('file');//获取文件信息
        $path =  'static/upload/' . (!empty($pathName) ? $pathName : 'images');//文件目录
        //创建文件夹
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }
        $info = $file->move($path);//保存在目录文件下
        if ($info && $info->getPathname()) {
            $data = [
                'status' => 1,
                'data' =>  '/'.$info->getPathname(),
            ];
            echo exit(json_encode($data));
        } else {
            echo exit(json_encode($file->getError()));
        }   
    }     
 
    // 加载编辑客户页面
    public function viewEditCustomer($id){
		// 查询单个数据
		$customerlist = Customer::where('id',$id)->find()->toArray();
        $customerlist['imgurl'] = explode(';', $customerlist['imgurl']);
        // dump($customerlist);die;
    	return view('index/customerEdit',['customerlist'=>$customerlist]);
    }

    //编辑客户
    public function editCustomerList(){
        if (array_key_exists('imgurl',$_POST)) {
            $_POST['imgurl'] = implode(";", $_POST['imgurl']);
        }
    	$num = Customer::edCustomerList($_POST);
    	if ($num >0) {
    		session('message','客户信息修改成功');
           	$this->redirect('Customers/index');
    	}else{
    		session('message','客户信息修改失败');
    		$this->redirect('Customers/index');
    	}
    }

     // 加载客户详情页面
    public function viewDetailCustomer($id){
        // 查询单个数据
        $customerlist = Customer::where('id',$id)->find()->toArray();
        $customerlist['imgurl'] = explode(';', $customerlist['imgurl']);
        return view('index/customerDetail',['customerlist'=>$customerlist]);
    }
}
