<?php
class Action extends CI_Controller {
	
	
	
	function get_song_categories($table,$id = 0)
	{
		$songs_categories_array = array();
		$category_select = '<select multiple = "multiple" name = "category[]" id = "category">';
		
		if($id != 0)
		{
			$this->db->where('songID',$id);
			$songs_categories = $this->db->get('piano_songs_categories');
			
			foreach ($songs_categories->result() as $songs_category)
			{
				$songs_categories_array[] = $songs_category->catID;
			}
		}
	
		$this->db->order_by('title');
		$categories=$this->db->get('piano_categories');
		
		foreach($categories->result() as $category)
		{
			if ($id != 0 and in_array($category->id,$songs_categories_array))
				$category_select .= '<option selected = "selected" value = "' . $category->id . '">' . $category->title . '</option>';
			else
				$category_select .= '<option value = "' . $category->id . '">' . $category->title . '</option>';
		}

		$category_select .= '</select>';		
		
		return $category_select;

	}
	
	function add($table)
	{
		$this->get_song_categories($table);
		$this->vedlib->add($table);
	}
	
	function save($table)
	{
		$this->vedlib->save($table);
	}
	
	function edit($table,$id)
	{
		$this->get_song_categories($table,$id);
		$this->db->where('songID',$id);
		$reasons = $this->db->get('piano_not_reviewed_reasons');
		
		foreach($reasons->result() as $reason)
		{
			if($this->ion_auth->is_admin())
				$reason_not_reviewed = '<label>Sababu ya kuto-review:</label><textarea name = "reason">' . $reason->reason . '</textarea>';
			else
				$reason_not_reviewed = '<label>Sababu ya kuto-review:</label><p class = "formlinks">' . $reason->reason . '</p>';
		}
		
		if(!isset($reason_not_reviewed) and )
			$reason_not_reviewed = '<label>Sababu ya kuto-review:</label><textarea name = "reason"></textarea>';
		
		$this->session->set_userdata(array ($table . '_append_lyrics_' . $id => $reason_not_reviewed));
		
		$this->vedlib->edit($table,$id);
	}
	
	function update($table,$id)
	{
		$this->vedlib->update($table,$id);
	}
	
	function confirm($table,$id)
	{
		$this->vedlib->confirm($table,$id);
	}
	
	function delete($table,$pk,$id)
	{
		$this->vedlib->delete($table,$pk,$id);
	}
	
	function update_uploaded_song($songID)
	{
	
		$user = $this->ion_auth->user()->row();
		$user = $user->id;
		
		$this->db->where('id',$songID);
		$uploaders=$this->db->get('piano_uploaded_songs');
		
		foreach($uploaders->result() as $uploader)
			$uploader_id = $uploader->uploaded_by;
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('jina_la_wimbo', 'Jina la Wimbo', 'required');
		$this->form_validation->set_rules('mtunzi', 'Mtunzi', 'required');
		$this->form_validation->set_rules('category[]', 'Category', 'required');
		

		if ($this->form_validation->run() == FALSE)
		{
			redirect('action/edit/piano_uploaded_songs/' . $songID . '/fail/validation');
		}
	
		if (!empty($_FILES['image_au_PDF']['name']))
        {
			
            // Specify configuration for File 1
            $config['upload_path'] = '../uploads/files/';
            $config['allowed_types'] = 'jpg|pdf'; 
 
            // Initialize config for File 1
            $this->upload->initialize($config);
 
            // Upload file 1
            if ($this->upload->do_upload('image_au_PDF'))
            {
				
                $data = $this->upload->data();
				$sheetname = $data['file_name'];
            }
			
			else
			{
				$error = array('error' => $this->upload->display_errors());
				redirect('action/edit/piano_uploaded_songs/' . $songID . '/fail/PDF');
			}	
        }
		
		if (!empty($_FILES['midi']['name']) and isset($_FILES['midi']['name']))
		{
			// Config for File 2 - can be completely different to file 1's config
			// or if you want to stick with config for file 1, do nothing!
			$config2['upload_path'] = '../uploads/files/';
			$config2['allowed_types'] = '*';
			$this->upload->initialize($config2);
			
			// Initialize the new config
 
			// Upload the second file
			if ($this->upload->do_upload('midi'))
			{
				$data = $this->upload->data();
				$midiname = $data['file_name'];
			}
			else
			{
				
				redirect('action/edit/piano_uploaded_songs/' . $songID . '/fail/midi');
				
			}
			

		}

		
		$update_data = array (
			'jina_la_wimbo'=>$_POST['jina_la_wimbo'],
			'mtunzi'=>$_POST['mtunzi']
		);
		
		
		if(isset($_POST['approved']))
			$update_data['approved'] = $_POST['approved'];
		
		if (isset($sheetname))
			$update_data['image_au_PDF'] = $sheetname;
		
		if (isset($_POST['response']))
			$update_data['response_to'] = $_POST['response'];
		
		if(isset($midiname))
			$update_data['midi'] = $midiname;
			
		if(isset($_POST['lyrics']))
			$update_data['lyrics'] = $_POST['lyrics'];
			
		$this->db->where('id',$songID);

		if($this->db->update('piano_uploaded_songs',$update_data))
		{				
			$this->db->where('id',$songID);
			$this->db->delete('piano_songs_categories');
			
			if($this->ion_auth->is_admin() and isset($_POST['reason']) and $_POST['reason'] != '')
			{
				$this->db->where('songID',$songID);
				$this->db->delete('piano_not_reviewed_reasons');
				$this->db->insert('piano_not_reviewed_reasons',array('songID'=>$songID, 'reason'=>$_POST['reason']));
			}
		
			$rows = array();
			foreach($_POST['category'] as $category)
			{
				$rows[] = array('songID' => $songID, 'catID' => $category);
			}
			
			$this->db->insert_batch('piano_songs_categories', $rows);			
			
			if(!$this->ion_auth->is_admin())
				$this->user_updated_song_email_to_admin($songID);
			else if($this->ion_auth->is_admin() and $_POST['approved'] == 1)
				$this->admin_reviewed_song_email_to_user($songID,$uploader_id);
			else if($this->ion_auth->is_admin() and $_POST['approved'] == 0 and $_POST['reason'] != '')
				$this->admin_not_reviewed_song_email_to_user($songID,$uploader_id);
			
				
			redirect('main/view_uploaded_songs');
		}	
		
	}
	
	function user_updated_song_email_to_admin($songID)
	{
			$this->load->library('email');
			
			$config['protocol'] = 'smtp';
			$config['smtp_host'] = 'mymail.brinkster.com';
			$config['smtp_user'] = 'admin@swahilimusicnotes.com';
			$config['smtp_pass'] = 'immaculata';
			$config['smtp_port'] = '25';
			$config['mailtype'] = 'html';
			$config['wordwrap'] = TRUE;
			$config['charset']='utf-8';  
			$config['newline']="\r\n";  

			$this->email->initialize($config);

			$this->email->from('admin@swahilimusicnotes.com', 'Swahili Music Notes');
			
			$this->email->cc('admin@swahilimusicnotes.com'); 
			

			$this->email->subject('Wimbo umekuwa edited!');
				
			$message = '';
			$message .= '<html><head></head><body>';
			$message .= 'Wimbo';
			
			$message .= ' unaoitwa <b>' . ucfirst($_POST['jina_la_wimbo']) . '</b>';
			$message .= ' umekuwa "edited" na sasa uko live kwenye site.<br><br>Ili kuuona wimbo huo, tembelea ukurasa huu > <a href="http://www.swahilimusicnotes.com/piano/details?song=' . base64_encode($songID) . '">' . ucfirst($_POST['jina_la_wimbo']) . '</a> <b><br><br>';
			$message .= 'Wako katika Kristu,<br><br>Vusile Terence Silonda,<br>Web Master,<br><a href="http://www.swahilimusicnotes.com/piano">Swahili Music Notes</a></body></html>';

			$this->email->message($message);	
			$this->email->set_alt_message(strip_tags($message));
			$this->email->to('admin@swahilimusicnotes.com'); 
			$this->email->send();
	}
	
	function admin_reviewed_song_email_to_user($songID,$user)
	{
		
		$this->db->where('id',$user);
		$user_data = $this->db->get('piano_backend_users');
		
		
		foreach ($user_data->result() as $user_info)
		{
			$user_name = $user_info->first_name . ' ' . $user_info->last_name;
			$user_email = $user_info->email;
		}
		
		
		$this->load->library('email');
		
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'mymail.brinkster.com';
		$config['smtp_user'] = 'admin@swahilimusicnotes.com';
		$config['smtp_pass'] = 'immaculata';
		$config['smtp_port'] = '25';
		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;
		$config['charset']='utf-8';  
		$config['newline']="\r\n";  

		$this->email->initialize($config);

		$this->email->from('admin@swahilimusicnotes.com', 'Swahili Music Notes');
		
		$this->email->cc('admin@swahilimusicnotes.com'); 
		

		$this->email->subject('Wimbo umekuwa reviewed!');
			
		$message = '';
		$message .= '<html><head></head><body>';
		$message .= 'Ndugu ' . $user_name . '<br><br>';
		
		$message .= 'Wimbo unaoitwa <b>' . ucfirst($_POST['jina_la_wimbo']) . '</b>';
		$message .= ' umekuwa "Reviewed" na sasa uko live kwenye site.<br><br>Ili kuuona wimbo huo, tembelea ukurasa huu > <a href="http://www.swahilimusicnotes.com/piano/details?song=' . base64_encode($songID) . '">' . ucfirst($_POST['jina_la_wimbo']) . '</a> <b><br><br>';
		$message .= 'Wako katika Kristu,<br><br>Vusile Terence Silonda,<br>Web Master,<br><a href="http://www.swahilimusicnotes.com/piano">Swahili Music Notes</a></body></html>';

		$this->email->message($message);	
		$this->email->set_alt_message(strip_tags($message));
		$this->email->to($user_email); 
		//$this->email->print_debugger();
		$this->email->send();
		//exit;
	}
	
	function admin_not_reviewed_song_email_to_user($songID,$user)
	{
		
		$this->db->where('id',$user);
		$user_data = $this->db->get('piano_backend_users');
		
		
		foreach ($user_data->result() as $user_info)
		{
			$user_name = $user_info->first_name . ' ' . $user_info->last_name ;
			$user_email = $user_info->email;
		}
		
		
		$this->load->library('email');
		
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'mymail.brinkster.com';
		$config['smtp_user'] = 'admin@swahilimusicnotes.com';
		$config['smtp_pass'] = 'immaculata';
		$config['smtp_port'] = '25';
		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;
		$config['charset']='utf-8';  
		$config['newline']="\r\n";  

		$this->email->initialize($config);

		$this->email->from('admin@swahilimusicnotes.com', 'Swahili Music Notes');
		
		$this->email->cc('admin@swahilimusicnotes.com'); 
		

		$this->email->subject('Wimbo HAUJAWA reviewed!');
			
		$message = '';
		$message .= '<html><head></head><body>';
		$message .= 'Ndugu ' . $user_name . '<br><br>';
		
		$message .= 'Wimbo unaoitwa <b>' . ucfirst($_POST['jina_la_wimbo']) . '</b>';
		$message .= ' haujawa "Reviewed" kutokana na makosa mbalimbali. Tafadhali ingia kwenye ukurasa wa Account yako ili upate maelezo zaidi.<br><br>';
		$message .= 'Wako katika Kristu,<br><br>Vusile Terence Silonda,<br>Web Master,<br><a href="http://www.swahilimusicnotes.com/piano">Swahili Music Notes</a></body></html>';

		$this->email->message($message);	
		$this->email->set_alt_message(strip_tags($message));
		$this->email->to($user_email); 
		$this->email->send();
	}
	
	
	function upload_song ()
	{
		$theUser = $this->ion_auth->user()->row();
		$user = $theUser->id;
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('jina_la_wimbo', 'Jina la Wimbo', 'required');
		$this->form_validation->set_rules('mtunzi', 'Mtunzi', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');
		

		if ($this->form_validation->run() == FALSE)
		{
			redirect('action/add/piano_uploaded_songs/fail/required');
		}
	
		if (!empty($_FILES['image_au_PDF']['name']))
        {
            // Specify configuration for File 1
            $config['upload_path'] = '../uploads/files/';
            $config['allowed_types'] = 'gif|jpg|png|pdf';    
			
			

 
            // Initialize config for File 1
            $this->upload->initialize($config);
 
            // Upload file 1
            if ($this->upload->do_upload('image_au_PDF'))
            {
                $data = $this->upload->data();
				$sheetname = $data['file_name'];
            }
            else
            {
				redirect('action/add/piano_uploaded_songs/fail/pdf');
                
            }
 
			if (!empty($_FILES['midi']['name']))
			{
				// Config for File 2 - can be completely different to file 1's config
				// or if you want to stick with config for file 1, do nothing!
				$config2['upload_path'] = '../uploads/files/';
				$config2['allowed_types'] = '*';
				$this->upload->initialize($config2);

	 
				// Initialize the new config
	 
				// Upload the second file
				if ($this->upload->do_upload('midi'))
				{
					$data = $this->upload->data();
					$midiname = $data['file_name'];
				}
				else
				{
					
					redirect('action/add/piano_uploaded_songs/fail/midi');
				}
 
			}
			
			$insert_data = array (
				'jina_la_wimbo'=>$_POST['jina_la_wimbo'],
				'mtunzi'=>$_POST['mtunzi'],
				'image_au_PDF'=>$sheetname,
				'uploaded_by'=>$user,
				'approved' => 0
			);
			
			
			if (isset($_POST['response']))
				$insert_data['response_to'] = $_POST['response'];
			
			if(isset($midiname))
				$insert_data['midi'] = $midiname;
				
			if(isset($_POST['lyrics']))
				$insert_data['lyrics'] = $_POST['lyrics'];

			
			if($this->db->insert('piano_uploaded_songs',$insert_data))
			{
				$id = $this->db->insert_id(); 
				$this->db->where('id',$user);
				$user_data = $this->db->get('piano_users');
				
				$rows = array();
				foreach($_POST['category'] as $category)
				{
					$rows[] = array('songID' => $id, 'catID' => $category);
				}
				
				$this->db->insert_batch('piano_songs_categories', $rows);

				
				foreach ($user_data->result() as $user_info)
				{
					$user_name = $user_info->display_name;
					$user_email = $user_info->user_email;
				}
				
			
				$this->load->library('email');
				
				$config['protocol'] = 'smtp';
				$config['smtp_host'] = 'mymail.brinkster.com';
				$config['smtp_user'] = 'admin@swahilimusicnotes.com';
				$config['smtp_pass'] = 'immaculata';
				$config['smtp_port'] = '25';
				$config['mailtype'] = 'html';
				$config['wordwrap'] = TRUE;
				$config['charset']='utf-8';  
				$config['newline']="\r\n";  

				$this->email->initialize($config);

				$this->email->from('admin@swahilimusicnotes.com', 'Swahili Music Notes');
				$this->email->to($user_email); 
				$this->email->cc('admin@swahilimusicnotes.com'); 
				

				$this->email->subject('Umefanikiwa ku-upload wimbo!');
					
				$this->email->message('Habari ndugu ' . $user_name . '. Hii ni kukutaarifu kuwa umefanikiwa ku-upload wimbo wa ' . $_POST['jina_la_wimbo'] . '. Tafadhali subiri kidogo, wakati tunaupitia na kuhakikisha kuwa kila kitu kipo kamili.');	

				$this->email->send();
				
				//TO ADMIN
				
				$this->email->from('admin@swahilimusicnotes.com', 'Swahili Music Notes');
				$this->email->to('bmidama@gmail.com'); 
				$this->email->cc('admin@swahilimusicnotes.com'); 
				

				$this->email->subject('Wimbo Unahitaji Review!');
				$message = '<html><head></head><body>';
				$message .= 'Habari ndugu Admin. Hii ni kukutaarifu kuwa wimbo <strong>' . $_POST['jina_la_wimbo'] . '</strong> umekuwa uploaded na <strong>'.$user_name.'</strong>. Tafadhali bofya link ifuatayo ili ku-review > <a href="' . base_url() . 'action/edit_song/piano_uploaded_songs/' . $id . '">Review</a></body></html>';
				
				$this->email->message($message);	

				$this->email->send();

				//echo $this->email->print_debugger();

				redirect('main/view_uploaded_songs/success');
			}
        }
 
       
			
		
	}
	
}