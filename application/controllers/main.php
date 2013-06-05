<?php
class main extends CI_Controller {

	function index()
	{
		// $header['title'] = 'Pinpoint Africa Media | Cost Effective Advertising in Tanzania';
		// $header['intro'] = '';
		
		// $header['h1'] ='The Most Cost-effective Way to Advertise to Middle Class Tanzania!  Reach Hundrends of Thousands of Unique / Different People for < $0.01 each.';
		
		$this->load->view('header');
		
		$this->load->view('slider');
		$this->load->view('footer');
	}


	function about()
	{
		$this->db->where('url','about');
		$data = $this->db->get('content');
		$contents = $data->row();
		
		$header['title'] =  $contents->title;
		$header['h1'] = $contents->title;
		$header['intro'] = '';
		$datas['content'] = $contents->text;
		$datas['peopleimg']=   '<img src="images/Home-Page-Image.png" style="margin: 84px 0px 0px 0px;" />';
		$datas['width'] = 'width:700px';
		
		$this->load->view('header',$header);
		$this->load->view('slider');
		
		$this->load->view('about');
			
		$this->load->view('footer');
	}
	
	function network()
	{
		// $this->db->where('url','the-contect-network');
		// $data = $this->db->get('content');
		// $contents = $data->row();
		
		// $header['title'] =  $contents->title;
		// $header['h1'] = $contents->title;
		// $datas['content'] = $contents->text;
		
		// $this->db->where('url','the-contect-network-intro');
		// $data = $this->db->get('content');
		// $contents = $data->row();
		
		

		$this->db->order_by('order');
		
		$datas['sites'] = $this->db->get('network_sites');
		


		$footer['noClients'] = 1;
		
		$this->load->view('header');
		
		$this->load->view('network',$datas);
		$this->load->view('footer',$footer);
	}
	
	
	function targeting()
	{
		// $this->db->where('url','target-options-intro');
		// $data = $this->db->get('content');
		// $contents = $data->row();
		
		// $header['title'] =  $contents->title;
		// $header['h1'] = $contents->title;
		// $header['intro'] = strip_tags($contents->text);
		
		
		// $this->db->where('url','target-options-content');
		// $data = $this->db->get('content');
		// $contents = $data->row();
		// $datas['content'] = $contents->text;
		
		// $datas['options'] = $this->db->get('targeting_options');
		
		// $this->db->where('url','target-options-content-bottom');
		// $data = $this->db->get('content');
		// $contents = $data->row();
		// $datas['footer'] = $contents->text;
		$footer['noClients'] = 1;
		$this->load->view('header');
		
		$this->load->view('targeting');
		$this->load->view('footer',$footer);
	}

	function details($bundleID)
	{
		$this->db->select('network_sites.url, network_sites.title, network_sites.text as main_site_description, content_bundles.name, sites_bundles.description as text,network_sites.logo, content_bundles.description as bundle_description ');
		$this->db->from('sites_bundles');
		$this->db->join('network_sites','sites_bundles.siteID=network_sites.id');
		$this->db->join('content_bundles', 'content_bundles.id=sites_bundles.bundleID');
		$this->db->order_by('order');
		$this->db->where('sites_bundles.bundleID', $bundleID);
		$datas['sites']=$this->db->get();

		if($datas['sites']->num_rows() == 0)
		{
			$this->db->where('id', $bundleID);
			$datas['bundle'] = $this->db->get('content_bundles')->row();
		}

		$footer['noClients'] = 1;
		$datas['details'] = 1;
		
		$this->load->view('header');
		
		$this->load->view('network',$datas);
		$this->load->view('footer',$footer);

		
	}

	function gallery()
	{
		$footer['noClients'] = 1;
		$this->load->view('header');
		
		$this->load->view('gallery');
		$this->load->view('footer',$footer);
	}

	function reporting()
	{
		$footer['noClients'] = 1;
		$this->load->view('header');
		
		$this->load->view('reporting');
		$this->load->view('footer',$footer);
	}
	
	function rates()
	{
		$footer['noClients'] = 1;
		$this->load->view('header');
		
		$this->load->view('rates');
		$this->load->view('footer',$footer);
	}

	function example($view)
	{
		$this->load->view($view);
	}
	

	
	function randomAlphaNum($length){ 

		/*$rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36 
		$rangeMax = pow(36, $length)-1; //largest number to give length digits in base 36 
		$base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number 
		$newRand = base_convert($base10Rand, 10, 36); //convert it 
		
		return $newRand; //spit it out */
		
		$arr = str_split('ABCDEFGHJKMNPQRSTUVWXYZ23456789'); // get all the characters into an array
		shuffle($arr); // randomize the array
		$arr = array_slice($arr, 0, $length); // get the first six (random) characters out
		return implode('', $arr); // smush them back into a string

	} 
	
	function contact()
	{
		$this->db->where('url','contact-pinpoint-africa-media');
		$data = $this->db->get('content');
		$contents = $data->row();
		
		$word = strtoupper($this->randomAlphaNum(7));
		
		$this->load->helper('captcha');
		
		$vals = array(
		'word' => $word,
		'img_path'	 => './captcha/',
		'img_url'	 => 'captcha/',
		'font_path'	 => './captcha/fonts/arial.ttf',
		'img_width'	 => '200',
		'img_height' => 50,
		);
		
		$datas['cap'] = create_captcha($vals);
	
		$cap_data = array(
		'captcha_time'	=> $datas['cap']['time'],
		'ip_address'	=> $this->input->ip_address(),
		'word'	 => $datas['cap']['word']
		);
		
		$query = $this->db->insert_string('captcha', $cap_data);	
		$this->db->query($query);
		
		$header['title'] = $contents->title;
		$header['h1'] = $contents->title;
		$header['intro'] = strip_tags($contents->text);
		$this->load->view('header');
		$this->load->view('slider');
		$this->load->view('contact');
		$this->load->view('footer');
		
		
	}
	
	function validate_captcha($captcha)
	{
		$expiration = time()-7200; // Two hour limit
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);	

	
		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($captcha, $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		
		
		if($row->count == 0){		// validate??
			$this->form_validation->set_message('validate_captcha', 'Incorrect Captcha');
			return FALSE;
		}else{
			return TRUE;
		}
		
	}
	
	function send_message()
	{
		if(isset($_POST))
		{
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('message', 'The Message', 'required');
			$this->form_validation->set_rules('captcha', 'The Captcha', 'required|callback_validate_captcha');
			$this->form_validation->set_rules('email', 'Email', 'matches[confirm_email]');
			$this->form_validation->set_rules('name', 'Your Name', 'required');
			$this->form_validation->set_rules('company', 'Company Name', 'required');
			
			if ($this->form_validation->run() == TRUE)
			{
				$this->load->library('email');
				
				$config['protocol'] = 'smtp';
				$config['smtp_port'] = 25; 

				$config['smtp_host'] = 'relay-hosting.secureserver.net';
				$config['mailtype'] = 'html';
				$config['wordwrap'] = TRUE;
				$config['charset']='utf-8';  
				$config['newline']="\r\n";  
					
				$this->email->initialize($config);

				$this->email->from('info@pinpointafricamedia.com', 'PinPoint Africa Media');
			
				$this->email->subject($_POST['subject']);
					
				$message = '';
				$message .= '<html><head></head><body>';
				$message .= '<br><br><strong>Name:</strong> ' . $_POST['name'] ;
				$message .= '<br><br><strong>Company:</strong> ' . $_POST['company'] ;
				$message .= '<br><br><strong>Subject:</strong> ' . $_POST['subject'] ;
				$message .= '<br><br><strong>Message:</strong> ' . $_POST['message'] ;
				$message .= '<br><br><strong>Email:</strong> ' . $_POST['email'] ;
				if(isset($_POST['phone']))
					$message .= '<br><br><strong>Phone:</strong> ' . $_POST['phone'] ;
				$message .= '</body></html>';
		
				$this->email->message($message);	
				$this->email->set_alt_message(strip_tags($message));
				$this->email->to('kirk@pinpointafricamedia.com'); 
				$this->email->bcc('terence@zoomtanzania.com'); 
				if($this->email->send())
				{

					
					$header['title'] = 'Message Sent Succesfully';
					$header['h1'] = 'Message Sent Succesfully';
					$header['intro'] = '';
					$data['results'] = "Your message was sent successfully, we will respond to you shortly.";
					
					$this->load->view('Header',$header);
					$this->load->view('navigation');
					$this->load->view('Page',$data);
					$this->load->view('Footer');
					
				}
				else
				{
					$data['results'] = "Oops, something went wrong, Your message was not sent successfully, please try again.";
					
					$header['title'] = 'Message Was NOT Sent Succesfully';
					$header['h1'] = 'Message NOT Sent Succesfully';
					$header['intro'] = '';
					$data['results'] = "Oops, something went wrong, Your message was not sent successfully, please try again.";
					
					$this->load->view('Header',$header);
					$this->load->view('navigation');
					$this->load->view('Page',$data);
					$this->load->view('Footer');					
				}

			}
			else
				$this->contact();
			
		}
		else
			redirect('contact');
	}


	
	function login($success = 0)
	{
		

		

		$data['h1']=$header['title'] = 'Login';
		$header['intro']='';
		$header['h1']='Login';

		$this->load->view('header');
		$this->load->view('login',$data);
		
		$this->load->view('footer');
	}
	
	function login_user()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == TRUE)
		{
			$identity = $_POST['email'];
			$password = $_POST['password'];
			$remember = false; // remember the user
			if($this->ion_auth->login($identity, $password, $remember))
			{
				if ($this->session->userdata('current_page')!='')
					redirect($this->session->userdata('current_page'));
				else
					redirect(base_url());
			}
			else
				redirect('login/1');
		}
		else
			$this->login();
	
	}
	
	function logout()
	{
		if($this->ion_auth->logout())
			redirect('login/2');
	}
	


	
	function concepts($url)
	{
		$this->db->where('url',$url);
		$data = $this->db->get('content');

		if($data->num_rows() > 0)
		{
			$contents = $data->row();	
			$header['title'] =  $contents->title;
			$header['h1'] = $contents->title;
			$header['intro'] = $contents->intro;
			$datas['content'] = $contents->text;
		}

		else
		{
			$sizes_array=array();
			$this->db->where('url', $url);
			$data = $this->db->get('concept_pages');
			$contents = $data->row();
			$header['title'] =  $contents->name;
			$header['h1'] = $contents->name;
			$header['intro'] = $contents->intro;

			$this->db->where('concept_id', $contents->id);
			$datas['ads'] = $this->db->get('ads');

			$sizes = $this->db->get('ad_sizes');

			foreach($sizes->result() as $size)
			{
				$sizes_array[$size->id]['width'] = $size->width;
				$sizes_array[$size->id]['height'] = $size->height;
			}

			$datas['sizes'] = $sizes_array;
		}

		$this->load->view('Header',$header);
		$this->load->view('navigation');
		$this->load->view('adpages',$datas);
		$this->load->view('Footer');
	}

	function register_test()
	{
		$username = 'michela@zoomtanzania.com';
		$email = 'michela@zoomtanzania.com';
		$password = 'maikamaika';
		$additional_data = array(
			'first_name' => 'Michela',
		);								
//		$group = array('1'); // Sets user to admin. No need for array('1', '2') as user is always set to member by default

		$id = $this->ion_auth->register($username, $password, $email, $additional_data);
		echo($id);		
		
		
	}
	
	
	
}
