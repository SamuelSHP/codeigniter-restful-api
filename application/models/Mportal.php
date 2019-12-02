<?php
/**
 * 
 */
class Mportal extends CI_Model
{
	
	private $_table;
	private $_key;
	private $_id;
	private $_resultData;
	private $_errors = [];

	function __construct()
	{
		parent::__construct();
		$this->_resultData = [];
	}

	public function get($param, $id = 0,$key = 'id')
	{
		$this->_key = $key;
		$this->_id 	= $id;

		$this->_findTable($param);

		$this->_resultData = $this->_findData();

		return $this->_resultData; 
	}

	public function post($param, $arrData)
	{
		$this->_findTable($param);

		$this->_resultData = $this->_insertData($arrData);

		return $this->_resultData;

	}

	public function update($param, $id, $arrData)
	{
		$this->_findTable($param);

		$this->_resultData = $this->_updateData($arrData, $id);

		return $this->_resultData;

	}

	public function delete($param, $id)
	{
		$this->_findTable($param);

		$this->_resultData = $this->_deleteData($id);

		return $this->_resultData;
	}

	// Set Table Name too
	private function _findTable($param)
	{		

		$this->db->select('table_name');
		$this->db->where('api_parameter',$param);
		$this->db->from('api_url_params');

		$result = $this->db->get();
		
		if ($result->num_rows() > 0) {

			$this->_table = $result->row()->table_name; //set table name

		}else $this->_errors = array('error_message' => "Table Not Found");
	}

	private function _findData()
	{		
		if (!empty($this->_table)) {
			
			$this->db->from($this->_table);			

			if (!empty($this->_id) ) {
				$this->db->where($this->_key, $this->_id);
			}

			$result = $this->db->get();			

			if ($this->db->count_all_results() > 0) {

				if($this->db->count_all_results() == 1 && !empty($this->_id)) {
					return $result->row();
				} 

				return $result->result();
			} else return false;	

		} else return $this->_errors;
	}

	private function _insertData($arrData)
	{	
		$this->db->insert($this->_table,$arrData);

		if ($this->db->affected_rows() == 1) return $this->db->insert_id();
		else return false;		
	}

	private function _updateData($arrData, $id)
	{		

		if ($this->_table != '' && !empty($arrData)) {
			
			$this->db->update($this->_table, $arrData, "id = {$id}");

			if ($this->db->affected_rows() == 1) return $id;
		}else{

			$strErrorMessage = '';

			if ($this->_table == '') $strErrorMessage .= "Table not found";

			if (empty($arrData)) {
				$strErrorMessage .= ($strErrorMessage != '') ? "," : "";
				$strErrorMessage .= "Data Post is empty";
			}			

			$this->_errors = array('error_message'=>$strErrorMessage);

			return $this->_errors;

		}		

	}

	private function _deleteData($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->_table);

		if ($this->db->affected_rows() == 1) return $id;
		else return false;

	}
}