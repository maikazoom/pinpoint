<?php
class Welcome extends CI_Controller {

	function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$data['title'] = 'Welcome';
			$data['tables'] = $this->db->list_tables();
			$this->load->view('Header',$data);
			$this->load->view('Front_page',$data);	
			$this->load->view('Footer');
		}
	}
}