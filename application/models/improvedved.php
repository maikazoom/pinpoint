<?php
class Vedmodel extends CI_Model {
	function list_tables($tables)
	{
		$tables_list = '<ul>';
		foreach($tables as $table)
		{
			$tables_list .= '<li   style = "background: 3366FF">' .  anchor(site_url("hello/preIndex/$table[0]"), ucfirst(str_replace('_',' ',$table[0])) , 'title="' . ucfirst(str_replace('_',' ',$table[0])) . '"') . "</li>";
			if(isset($table[1]))
				$this->session->set_userdata(array ($table[0]=>$table[1]));
		}
		$tables_list .= '</ul>';
		return $tables_list;
	}
	
	function get_primary_key($table)
	{
	///FOR PRE MYSQL-5 
		/*$query = "show index from test_options";
		$result = $this->db->query($query);
		foreach ($result->result() as $res)
			echo $res->Column_name;*/
			
		$query = "SELECT k.column_name as pk
			FROM information_schema.table_constraints t
			JOIN information_schema.key_column_usage k
			USING(constraint_name,table_schema,table_name)
			WHERE t.constraint_type='PRIMARY KEY'
			  AND t.table_schema='" . $this->db->database . "'
			  AND t.table_name='" . $table . "'";
			  
		$primaryKeyResult = $this->db->query($query);
		
		foreach($primaryKeyResult->result() as $primaryKey)
			$pk = $primaryKey->pk;
			
		return $pk;
	}
}