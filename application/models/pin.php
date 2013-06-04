<?php

class Pin extends CI_Model{

    function __construct(){

    	parent::__construct();
    }

   function getAll(){
    	//$this->db->order_by('rate','desc');
    	$this->db->select('*');
    	$this->db->from('advertising');
    	$this->db->join('desc_advertising','advertising.id=desc_advertising.id');
     // $this->db->from('property');
        //$this->db->join('branch', 'branch.id = property.id');

    	$query = $this->db->get();

    	//foreach ($q->result() as $row) 
     //  {
       //  $data[] = $row;
        // }
      // return $data;
    	
 //foreach ($query->result() as $row)
    /// {
        
     // $row->title; 
     // $row->icon;
    //  $row->rate;
    //  $row->details;

    //  }
    //  return $row;
    	
    //  function getAll(){
   //  $this->db->order_by("advertising_id", "desc"); 
   // $q = $this->db->get('advertising');


       // foreach ($q->result() as $row) 
     //   {
      //      $data[] = $row;
   //     }
     //   return $data;
 

  //  foreach($data as $qq)
   // {
   //    $q = "SELECT * FROM desc_advertising WHERE advertising_id = ?";
    //   $query = $this->db->query($q,array($qq->advertising_id);
    //   $qd = $query->result();
       //$blog_e->n_comments = $ncomments[0]['count'];
   // }

  //  }


}
















