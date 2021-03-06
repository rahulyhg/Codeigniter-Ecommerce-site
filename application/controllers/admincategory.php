<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admincategory extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!validateAccess($this->session->userdata('uid')))
		{
			$this->session->sess_destroy();
			redirect('/', 'refresh');
		}
		if(!checkForValidAccess(array('0'))){
			$this->session->sess_destroy();
			redirect('/', 'refresh');
		}
		$this->_flushOutputArray();
		$this->load->model('categories','ca');
	}
	
	function _flushOutputArray(){
		$this->outputData = array();
	}
	
	public function index(){
		$this->outputData['data'] =$this->ca->getAll();
		$this->load->view('admin/categories/list',$this->outputData);
	}

	private function _categoryValidate(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Category Name', 'trim|required|callback_name_exist');	
	}

	public function name_exist($str){
		if ($this->ca->checkNameExists($str) && $this->uri->segment(2)  == 'add' ){
			$this->form_validation->set_message('name_exist', 'Category Name Already Exist');
			return FALSE;
		} 	
		return TRUE;
	}

	public function add(){
		$this->_categoryValidate();
		$this->outputData['error'] = '';
		if($this->form_validation->run() === TRUE){
			$this->ca->add();
			redirect('admincategory','refresh');
		}
		$this->outputData['error'] = validation_errors();
		$this->load->view('admin/categories/add_edit',$this->outputData);
	}


	public function edit(){
		$this->_categoryValidate();
		$this->outputData['error'] = '';
		$this->outputData['data'] = $this->ca->getById($this->uri->segment(3));
		if($this->form_validation->run() === TRUE){
			$this->ca->edit($this->uri->segment(3));
			redirect('admincategory','refresh');
		}
		$this->outputData['error'] = validation_errors();
		$this->load->view('admin/categories/add_edit',$this->outputData);
	}

}