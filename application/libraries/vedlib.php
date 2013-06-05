<?php
class Vedlib {
	
	function index($table,$offset='',$resource='',$base_url='')
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('login');
		}
		else
		{
			$CI->load->library('table');
			$CI->load->library('pagination');
			
			if($table == 'piano_uploaded_songs')
			{
				if(!$CI->ion_auth->is_admin())
				{
					$theUser = $CI->ion_auth->user()->row();
					$user = $theUser->id;
					$CI->db->where('uploaded_by', $user);
				}
				$songs = $CI->db->get('piano_uploaded_songs');
				$data['total_rows'] = $config['total_rows'] = $songs->num_rows();
			}
			
			else if($resource != '')
				$data['total_rows'] = $config['total_rows'] = $resource->num_rows();	
			
			else
				$data['total_rows'] = $config['total_rows'] = $CI->db->count_all($table);	
			

			if($base_url == '')
				$config['base_url'] = base_url().'backend/view/'.$table;
			else
				$config['base_url'] = base_url().$base_url;

			
			$config['uri_segment'] = 4;
				
			$config['per_page'] = 50;
			
			$CI->pagination->initialize($config);
			
			$order = 0;
			$fields = $this->table_fields($table);
			foreach($fields as $field)
			{
				if($field == 'order_column')
					$order = 1;
			}
		
			$data['title'] = ucfirst(str_replace('_',' ',$table));
			$datas = array();
			$datas['pk'] = $pk = $this->get_primary_key($table);
			
			if($resource != '')
				$datas['data'] = $resource;
			else
				$datas['data'] = $CI->db->get($table, $config['per_page'],$CI->uri->segment(4));

			$datas['table_name'] = $table;
				
				
			$datas['count'] = $datas['data']->num_rows();
			
			$type_info = array();
			$types = $this->data_type($CI->db->database,$table);
			foreach ($types->result() as $type)
				$type_info[$type->col] = $type->type;
				
			$datas['type_info'] = $type_info;
			
			/*REF DATA*/
			
			$query = $this->find_relationship($CI->db->database,$table);
				
			$ref_tab = array();
			$ref_col = array();
			$referring = array();
			
			foreach ($query->result() as $item)
			{
				$ref_tab[$item->column_name] = $item->referenced_table_name;
				$ref_col[] = $item->referenced_column_name;
				$referring[$item->column_name] = $item->column_name;
			}
			
			$ref_data = array();
			if($query->num_rows() > 0)
			{
				foreach ($ref_tab as $col=>$tab)
				{
					//$ref_data[$col] = $CI->db->get($tab,$query->num_rows()+5,$CI->db->count_all($tab));
					$ref_data[$col] = $CI->db->get($tab);
				}
			}
			
			$datas['rel'] = $ref_data;
			$datas['rel_info'] = $ref_col;
			$datas['referring'] = $referring;
			
			/*END REF DATA*/
			$CI->load->view('backend-header',$data);
			$CI->load->view('Viewer',$datas);
			$CI->load->view('backend-footer');
		}
	}
	
	function get_primary_key($table)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
			
		$query = "SELECT k.column_name as pk
			FROM information_schema.table_constraints t
			JOIN information_schema.key_column_usage k
			USING(constraint_name,table_schema,table_name)
			WHERE t.constraint_type='PRIMARY KEY'
			  AND t.table_schema='" . $CI->db->database . "'
			  AND t.table_name='" . $table . "'";
			  
		$primaryKeyResult = $CI->db->query($query);
		
		foreach($primaryKeyResult->result() as $primaryKey)
			$pk = $primaryKey->pk;
			
		return $pk;
	}

	function data_type ($schema, $table_name)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		$query = $CI->db->query ("SELECT i.column_name as col,i.data_type as type from information_schema.columns as i where  i.table_schema = '$schema' and i.table_name = '$table_name' ");

		return $query;
	}
	
	function delete ($table_name,$primaryKey,$id)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else 
		{
			if($CI->session->userdata($table_name . '_delete'))
			{
				redirect($CI->session->userdata($table_name . '_delete') .'/'.$id);
			}
			else
			{
				$CI->db->where($primaryKey,$id);
				$CI->db->delete($table_name);
				redirect("backend/view/$table_name");
			}
		}
	}
	
	function edit ($table_name,$id,$extra_fields='')
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$CI->load->helper('form');
			$CI->load->helper('array');
	
			$data = array();
			$data['id'] = $id;
			$extra = array();
			$extra['id'] = $id;
			
			$CI->db->where($this->get_primary_key($table_name),$id);
			$query = $CI->db->get($table_name);
			$extra['query'] = $query;
			
			$view = 'Editor';
			if (isset($extra_fields) and $extra_fields != '')
				$this->referenced($table_name, 'Edit', $view, $extra,$extra_fields);
			else
				$this->referenced($table_name, 'Edit', $view, $extra);
		}
	}
	
	function update ($table_name,$id)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('login');
		}
		else
		{
			$CI->load->model('Vedmodel');
			$set = '';
			$pk = $this->get_primary_key($table_name);
			$update_data = array();
			
			$fields = $CI->db->list_fields($table_name);
			foreach ($_POST as $field => $datum)
			{
				switch ($field)
				{			
					case 'last_modified_by':			
					$_POST[$field] = $CI->session->userdata('userid');
					break;
					
					case 'added_date':
					case 'birthday':
					case 'date':
					$_POST[$field]= date('Y-m-d',strtotime($datum));
					break;
					
					case 'last_modified_date':
					$_POST['last_modified_date'] = date('Y-m-d');
					break;
			
				}
			}
			

			foreach ($fields as $field)
			{
			
				if ($field == $pk)
					continue;
				$update_data[$field] = $_POST[$field];
				
			}
			
			$CI->db->where($pk,$id);
			if($query = $CI->db->update($table_name, $update_data))
				//$this->index($table_name);
				redirect("backend/view/$table_name");
		}
	}
	
	function add ($table_name,$extra_fields='')
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			if(isset($extra_fields) and $extra_fields != '')
				$this->referenced($table_name,'Add','Adder','',$extra_fields);
			else
				$this->referenced($table_name,'Add','Adder');
		}
	
	}
	
	function getter ($table_name)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$data = array();
			$query = $this->db->get($table_name);
			foreach ($query->result() as $row)
			{
				$data[$row->id] = $row->title;
			}
			
			return $data;
		}
	}
	
	function referenced($table,$title,$view,$extra='',$extra_fields='')
	{
	
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		
		else
		{
			$CI->load->model ('Vedmodel');
			$CI->load->helper('array');
			
			$CI->load->helper('form');
			$CI->load->model ('Vedmodel');
			$data['table_name'] = $table;
			$data['title'] = $title;
			
			$query = $this->find_relationship($CI->db->database,$table);
				
			$ref_tab = array();
			$ref_col = array();
			$referring = array();
			
			foreach ($query->result() as $item)
			{
				$ref_tab[$item->column_name] = $item->referenced_table_name;
				$ref_col[] = $item->referenced_column_name;
				$referring[$item->column_name] = $item->column_name;
			}
			
			$ref_data = array();
			if($query->num_rows() > 0)
			{
				foreach ($ref_tab as $col=>$tab)
				{
					//$ref_data[$col] = $CI->db->get($tab,$query->num_rows()+5,$CI->db->count_all($tab));
					$ref_data[$col] = $CI->db->get($tab);
				}
			}
			
			$type_info = array();
			$types = $this->data_type($CI->db->database, $table);
			
			foreach ($types->result() as $type)
				$type_info[$type->col] = $type->type;
			
			$validation = $this->validation_parameters($CI->db->database, $table);
			
			foreach($validation->result() as $validator)
			{
				if($validator->nul == 'NO')
				$data['validate'][$validator->col] = 'class = "validate[required]"';
			}
			
			$data['rel'] = $ref_data;
			$data['rel_info'] = $ref_col;
			$data['referring'] = $referring;
			$data['type_info'] = $type_info;
			$data['pk'] = $this->get_primary_key($table);
		
			if (isset($extra) and $extra != '')
				foreach ($extra as $index => $dat)
					$data[$index] = $dat;
			
			if (isset($extra_fields) and $extra_fields != '')
				$data['additional_fields']=$extra_fields;
				
			
			$CI->load->view ('backend-header',$data);
			
			$CI->load->view ($view,$data);
			$CI->load->view ('backend-footer');
		}
	}
	
	function find_relationship ($schema, $table_name)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		$query = $CI->db->query ("SELECT u.referenced_table_schema, u.referenced_table_name,
			u.referenced_column_name, u.column_name 
			FROM information_schema.table_constraints AS c
			INNER JOIN information_schema.key_column_usage AS u
			USING ( constraint_schema, constraint_name ) 
			WHERE c.constraint_type =  'FOREIGN KEY'
			AND c.table_schema =  '$schema'
			AND c.table_name =  '$table_name'
			LIMIT 0 , 30"
		);
		
		//$query = $CI->db->get('col_references');
		
		return $query;
	}
	
	function save ($table)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('login');
		}
		else
		{
			
			$validates = $this->validation_parameters ($CI->db->database,$table);
			
			foreach ($validates->result() as $validator)
			{
				if($validator->col == 'id')
					continue;
				
				else if($validator->nul == 'NO')
				{
					$required = 'required';
					$CI->form_validation->set_rules($validator->col, ucfirst($validator->col), $required);
				}
			}

			if ($CI->form_validation->run() == TRUE)
			{
				foreach ($_POST as $field => $datum)
				{
					switch ($field)
					{

						case 'password':
						$data[$field] = sha1($datum);
						break;
					
						case 'added_by':				
						$data[$field] = $this->session->userdata('userid');
						break;
					
						case 'added_date':
						$data[$field]= date('Y-m-d',strtotime($datum));
						break;
					
						case 'birthday':
						case 'date':
						$data[$field]=date('Y-m-d',strtotime($datum));
						break;
					
					}
				}
			
				$query_str = $CI->db->insert_string($table,$_POST);
				if($CI->db->query($query_str))
					redirect("backend/view/$table");
			}
			else
			{
				$this->referenced($table,'Add','Adder');	
			}
		}
	}
	
	function validation_parameters ($schema, $table_name)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		$query = $CI->db->query ("SELECT i.is_nullable as nul,i.column_name as col from information_schema.columns as i where  i.table_schema = '$schema' and i.table_name = '$table_name' ");

		return $query;
	}
	
	function confirm($table,$id)
	{
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		
		else
		{
			$data['table'] = $table;
			$primary_key = $this->get_primary_key($table);
			
			
			$CI->db->where($primary_key,$id);
			$data['id'] = $id;
			$data['pk'] = $primary_key;

			$result = $CI->db->get($table);
			foreach($result->result() as $res)
			{
				if(isset($res->title))
					$data['title'] = $res->title;
				else
					$data['title'] = 'this record';
			}
			
			$CI->load->view('Confirm',$data);
		}
	}
	
	function move_up($table, $id) 
	{
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$CI->db->where('id',$id);
			
			$original_order = $CI->db->get($table);
			
			foreach ($original_order->result() as $order)
			{
				$the_original_order = $order->order_column;
			}
			
			$CI->db->where ('order_column <', $the_original_order);
			
			$CI->db->select ('order_column,id');
			$CI->db->order_by('order_column','desc');
			$CI->db->limit(1);
			$above_order = $CI->db->get($table);
			
			foreach ($above_order->result() as $order)
			{
				$the_above_order = $order->order_column;
				$above_id = $order->id;
				//echo $order->id;
			}
			
			$data = array ('order_column' => $the_above_order);
			$CI->db->where ('id',$id);
			$CI->db->update ($table,$data);
			
			//echo $CI->db->last_query();
			
			$data = array ('order_column' => $the_original_order);
			$CI->db->where ('id',$above_id);
			$CI->db->update ($table,$data);
			
			//echo $CI->db->last_query();
			redirect("ved/index/". $table . "/non/non/pre/order_column");
		}
	}
	
	function move_down($table, $id)
	{
		if (!$CI->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$CI->db->where('id',$id);
		
			$original_order = $CI->db->get($table);
			
			foreach ($original_order->result() as $order)
			{
				$the_original_order = $order->order_column;

			}
			
			$CI->db->where ('order_column >', $the_original_order);
			
			$CI->db->select ('order_column,id');
			$CI->db->order_by('order_column','asc');
			$CI->db->limit(1);
			$above_order = $CI->db->get($table);
			
			foreach ($above_order->result() as $order)
			{
				$the_above_order = $order->order_column;
				$above_id = $order->id;
			}
			
			$data = array ('order_column' => $the_above_order);
			$CI->db->where ('id',$id);
			$CI->db->update ($table,$data);
			
			$data = array ('order_column' => $the_original_order);
			$CI->db->where ('id',$above_id);
			$CI->db->update ($table,$data);
			
			redirect("ved/index/". $table . "/non/non/pre/order_column");
		}
	}
	
	function table_fields($table)
	{
		$CI =& get_instance();
		return $CI->db->list_fields($table);

	}
	
		
	
	
}