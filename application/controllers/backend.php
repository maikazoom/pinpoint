<?php
class backend extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->helper('url');
		
		$this->load->library('grocery_CRUD');	
	}
	
	function _example_output($output = null)
	{
		$this->load->view('example.php',$output);	
	}
	
	function index()
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('login');
		}
		else
		{
			$datas['datasent'] = '<ul>';

			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/content"), "Content" , 'title="Content"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/network_sites"), "Network Sites" , 'title="Network Sites"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/targeting_options"), "Targeting Options" , 'title="Targeting Options"') . "</li>"; 			
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/advertising"), "Advertising Rates" , 'title="Advertising Rates"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/desc_advertising"), "Advertising Categories" , 'title="Advertising Categories"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/creative_services"), "Creative Services" , 'title="Creative Services"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/cpm_advertising_bundles"), "CPM Advertising Bundles" , 'title="CPM Advertising Bundles"') . "</li>"; 			
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/ad_sizes"), "Ad Sizes" , 'title="Ad Sizes"') . "</li>"; 
			$datas['datasent'] .= '<li   style = "background: #3366FF">' . anchor(site_url("backend/concept_pages"), "Concept Pages" , 'title="Concept Pages"') . "</li>"; 
			
			
			$datas['datasent'] .= '</ul>';
			$header['title'] = 'Pinpoint Africa Media Dashboard';
						
			$this->load->view('backend-header',$header);
			$this->load->view('dashboard',$datas);
		}
	}
	
	function make_url_from_title($title,$table,$id)
	{
		$url = strtolower(url_title($title));
		

		$this->db->where('url',$url);
		$obj=$this->db->get($table);
		
		if($obj->num_rows() > 0)
		{
			$this->db->where('id',$id);
			$this->db->where('url',$url);
			$obj=$this->db->get($table);
			
			if( $obj->num_rows() == 0 )
				$url = $this->make_url_from_title($url . '-' . $url,$table,$id);
		}
		
		return $url;
		
	}

	function content()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}
	
	function advertising()
	{
	
		$this->grocery_crud->set_relation('category_id','desc_advertising','title');
		$output = $this->grocery_crud->render();
		

		$this->_example_output($output);
	}
	
	function desc_advertising()
	{
		$this->grocery_crud->set_field_upload('icon','images');
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}

	function content_bundles()
	{
		
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}
			
	function ad_sizes()
	{
		
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}
					
	function concept_pages()
	{
		$this->grocery_crud->callback_after_insert(array($this, 'concept_pages_callback'));
		$this->grocery_crud->unset_fields('url','page_url','ads');
		$this->grocery_crud->unset_columns('url');
		$output = $this->grocery_crud->render();


		$this->_example_output($output);
	}
		

	function concept_pages_callback($post_array, $primary_key)
	{
		$url = $this->make_url_from_title($post_array['name'],'concept_pages',$primary_key);
		$data = array(
			'url'=>$url,
			'page_url' => base_url() . 'concepts/' . $url,
			'ads' => "<a href = '" . base_url() . "backend/ads/" . $primary_key . "'>Ads</a>"

			);

		$this->db->where('id', $primary_key);
		$this->db->update('concept_pages', $data);
		//print_r($data);
	}

	function ads($concept_id)
	{

		$this->grocery_crud->where('concept_id',$concept_id);
		$this->grocery_crud->set_relation('size','ad_sizes','size_name');
		$this->grocery_crud->set_field_upload('ad','uploads');
		$this->grocery_crud->set_field_upload('gif','uploads');
		$this->grocery_crud->display_as('width', 'Full Expansion Width');
		$this->grocery_crud->display_as('height', 'Full Expansion Height');
		$this->grocery_crud->unset_fields('concept_id');
		$this->grocery_crud->unset_columns('concept_id');
		$this->grocery_crud->callback_after_insert(array($this, 'ads_callback'));

		$output = $this->grocery_crud->render();
		$output->additional_text = "<a href = '" . base_url() . "backend/concept_pages/" . $this->uri->segment(4) . "'>Return to Concept Pages</a>";

		$this->_example_output($output);
	}

	function ads_callback($post_array,$primary_key)
	{
		$data['concept_id'] = $this->uri->segment(3);

		$this->db->where('id', $primary_key);
		$this->db->update('ads', $data);
	}
		
	function network_sites()
	{
		$this->grocery_crud->order_by('order');
		$this->grocery_crud->set_field_upload('logo','img');
		$this->grocery_crud->set_relation_n_n('content_bundles','sites_bundles','content_bundles','siteID','bundleID','name','priority');
		$this->grocery_crud->unset_columns('content_bundles');
		$this->grocery_crud->unset_fields('bundles_description','order');
		$this->grocery_crud->callback_after_insert(array($this, 'network_sites_insert_callback'));
		$this->grocery_crud->callback_after_update(array($this, 'network_sites_update_callback'));
   		$this->grocery_crud->add_action('Move Down', base_url() . 'img/movedownbtn.png', 'backend/move_site_down');
		$this->grocery_crud->add_action('Move Up', base_url() . 'img/moveupbtn.png', 'backend/move_site_up');

		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}

	function network_sites_insert_callback($post_array,$primary_key)
	{
		$data['bundles_description'] = "<a href = '" . base_url() . "backend/sites_bundles/" . $primary_key . "'>Bundles Description</a>";
		$data['order'] = $primary_key;


		$this->db->where('id',$primary_key);
		$this->db->update('network_sites', $data);

	}

	function network_sites_update_callback($post_array,$primary_key)
	{
		$data['bundles_description'] = "<a href = '" . base_url() . "backend/sites_bundles/" . $primary_key . "'>Bundles Description</a>";
		

		$this->db->where('id',$primary_key);
		$this->db->update('network_sites', $data);

	}

	function move_site_up($siteID)
	{
		$this->db->where('id', $siteID);
		$siteObj=$this->db->get('network_sites');
		$site = $siteObj->row();

		$this->db->where('order <', $site->order);
		$this->db->order_by('order','desc');
		$this->db->limit(1);
		$higherSiteObj = $this->db->get('network_sites');

		if($higherSiteObj->num_rows > 0 )
		{

			$higherSite = $higherSiteObj->row();

			$higherSiteRank = array ('order' => $site->order);
			$this->db->where('id', $higherSite->id);
			$this->db->update('network_sites',$higherSiteRank);

			$siteRank = array ('order' => $higherSite->order);
			$this->db->where('id', $site->id);
			$this->db->update('network_sites',$siteRank);
            
		}

		redirect('backend/network_sites');
		
	}


	function move_site_down($siteID)
	{
		$this->db->where('id', $siteID);
		$siteObj=$this->db->get('network_sites');
		$site = $siteObj->row();


		$this->db->where('order >', $site->order);
		$this->db->limit(1);
		$lowerSiteObj = $this->db->get('network_sites');

		if($lowerSiteObj->num_rows > 0)
		{

			$lowerSite = $lowerSiteObj->row();

			$lowerSiteRank = array ('order' => $site->order);
			$this->db->where('id', $lowerSite->id);
			$this->db->update('network_sites',$lowerSiteRank);

			$siteRank = array ('order' => $lowerSite->order);
			$this->db->where('id', $site->id);
			$this->db->update('network_sites',$siteRank);
		}   

		redirect('backend/network_sites');
	}


	function sites_bundles($siteID)
	{
		$this->grocery_crud->where('siteID',$siteID);
		$this->grocery_crud->set_relation('siteID','network_sites','title');
		$this->grocery_crud->set_relation('bundleID','content_bundles','name');
		$this->grocery_crud->display_as('siteID','Website / Blog');
		$this->grocery_crud->display_as('bundleID','Content Bundle');
		$this->grocery_crud->order_by('order');
		$this->grocery_crud->unset_columns('priority');
		$this->grocery_crud->unset_fields('priority');
		$output = $this->grocery_crud->render();

		$this->_example_output($output);

	}
	
	function targeting_options()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}
	
	function creative_services()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}	
	
	function cpm_advertising_bundles()
	{
		$this->grocery_crud->set_field_upload('image','images');
		$output = $this->grocery_crud->render();
		$this->_example_output($output);
	}
	
	
	
	
	
	function view($table)
	{
		$this->vedlib->index($table);
	}

	
	
	
	
	
	function logout()
	{
		if($this->ion_auth->logout())
			redirect('login/2');
	}
	
	
	
	
}