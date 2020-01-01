<?php 
/**
 * 
 */
class Loginmodel extends CI_model
{
	
	public function __construct()
	  {
	    parent::__construct();
	    $this->load->database();
	  }
	 public function is_valid($phone){
	    $this->db->select('*');
	    $this->db->from('user');
	    $this->db->where('phone',$phone);
	    $query = $this->db->get();
	    return $query->row();
  	}
  public function is_valid_num($phone){
    $this->db->select('*');
    $this->db->from('user');
    $this->db->where('phone',$phone);
    $query = $this->db->get();
    return $query->num_rows();
  }
}
?>