<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
2015年2月8日PHP
*/

class Login extends CI_Controller{
	
	//传递到视图的数据
	private $_data;
	
	public $referrer;
	
	public function __construct(){
		parent::__construct();
		//加载控制用户登录和登出类
		$this->load->library('auth');
		$this->load->library('form_validation');
		
		//加载用户处理model,起别名users
		$this->load->model('users_mdl','users');
		
		//加载自定义的common类
		$this->load->library('common');
		
	}
	
	public function index(){
		
		//检查是否已经登录,如果已经登录,那么就跳转到后台
	/* 	if($this->auth->hasLogin()){
			redirect($this->referrer);
		} */
		
		
		//前端验证输入的用户名密码
		$this->form_validation->set_rules('name','用户名','required');
		$this->form_validation->set_rules('password','密码','required');
		$this->form_validation->set_error_delimiters('<li>','</li>');
		
		//如果不通过,回到登录页面
		if($this->form_validation->run() == FALSE){
			
			$this->load->view('admin/login',$this->_data);
		}
		else{
			//数据库验证登录,如果正确,那么得到用户信息,如果错误,返回FALSE
			$user = $this->users->validate_user(
						//第二个参数是可选的，如果想让取得的数据经过跨站脚本过滤（XSS Filtering），把第二个参数设为TRUE。
						$this->input->post('name',TRUE),
						$this->input->post('password',TRUE)
					);	
			//如果登录信息正确
			if(!empty($user)){
				
				//process_login()处理登录信息,如果正确,更新登录信息
				if($this->auth->process_login($user)){
					//跳转
					//redirect($this->referrer);
					$this->load->view('admin/admin');
				}
				
			}	
			
			//如果用户名密码错误
			else{
				//先休眠3秒,可以稍微防止一下爆破
				sleep(3);
				
				$this->session->setflashdata('login_error','TRUE');
				$this->_data['login_error_msg'] = '用户名或密码无效';
				$this->load->view('admin/login',$this->_data);
			}
			
		}
		
		

	}
	
} 

/*
End of file
Location:login.php
*/