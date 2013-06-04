<?php
class Vedmodel extends CI_Model{
	
	function update ($data, $table_name,$id)
	{	
		$this->load->model('Vedmodel');
		$set = '';
		$pk = $this->Vedmodel->get_primary_key($table_name);
		$update_data = array();
		
		$fields = $this->db->list_fields($table_name);
		foreach ($data as $field => $datum)
		{
			switch ($field)
			{			
				case 'last_modified_by':			
				$data[$field] = $this->session->userdata('userid');
				break;
				
				case 'added_date':
				case 'birthday':
				case 'date':
				$data[$field]= date('Y-m-d',strtotime($datum));
				break;
				
				case 'last_modified_date':
				$data['last_modified_date'] = date('Y-m-d');
				break;
		
			}
		}
		
		

		foreach ($fields as $field)
		{
		
			if ($field == $pk)
				continue;
			$update_data[$field] = $data[$field];
			
		}
		
		$this->db->where($pk,$id);
		if($query = $this->db->update($table_name, $update_data))
		{
			return true;
		}
		
	}
	

	
	
	
	
	function data_type ($schema, $table_name)
	{
		$query = $this->db->query ("SELECT i.column_name as col,i.data_type as type from information_schema.columns as i where  i.table_schema = '$schema' and i.table_name = '$table_name' ");

		return $query;
	}
	
	
	
	function array_getter($table_name,$columns='', $where = '')
	{
	
		if($columns != '')
		{
			
			$cols = '';
			foreach ($columns as $column)
			{
				$cols .= $column .',';
			}
			$cols = substr ($cols,0,-1);
			
			$this->db->select ($cols);
		}
		
		if($where != '')
		{
		
			
			foreach ($where as $key=>$value)
			{
				$col =$key;
				$val = $value;
			}
			$this->db->where($col, $val);
		}
		
		$result=$this->db->get($table_name);
		$to_return = array();
		foreach($result->result() as $row)
		{
			
			if ($columns != '')
				$to_return[$row->id] = $row->$columns[1];
				
			else
				$to_return[] = $row;
			
			
		}
		
		
		return $to_return;
	
	}
	

	
}