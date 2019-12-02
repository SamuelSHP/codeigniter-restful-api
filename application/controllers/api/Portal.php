<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Portal extends \Restserver\Libraries\REST_Controller
{
	
	function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

        //Load model
        $this->load->model('Mportal');
    }

	public function index_get($strParam)
	{		
		$result = $this->Mportal->get($strParam);

		if (!empty($result) && array_key_exists('error_message', $result) === FALSE) {

			$this->response(array(
				'success' => TRUE, 
				'data' => $result
			), \Restserver\Libraries\REST_Controller::HTTP_OK);

		}else{

			$this->response(array(
				 'success'	=>  FALSE,
				 'data' => [],
				 'message' => (array_key_exists('error_message', $result)) ? $result['error_message'] : "No Data"
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}

		
	}

	public function item_get($strParam, $id)
	{

		$result = $this->Mportal->get($strParam, $id);

		if ($result) {

			$this->response(array(
				'success' => TRUE, 
				'data' => $result),
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}else{

			$this->response(array(
				 'success'	=>  FALSE,
				 'message' => 'No data was found'
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}
	}


	// Save or Update Data
	public function item_post($strParam, $id = 0)
	{
		$arrData = $this->post();

		// if request has ID, it's an update, else it's an insert
		if ($id !== 0) $result = $this->Mportal->update($strParam, $id, $arrData);
		else $result = $this->Mportal->post($strParam, $arrData);
		

		if (!empty($result) && (!is_array($result) || array_key_exists('error_message', $result) === FALSE)) {

			$this->response(array(
				 'success'	=>  TRUE,
				 'message' => ($id === 0) ? 'Data inserted' : 'Data updated',
				 'id' => $result
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		} elseif (empty($result)) {
			
			$this->response(array(
				 'success'	=>  TRUE,
				 'message' => ($id === 0) ? 'No Data inserted' : 'No Data updated',
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		} else {

			$strErrorMessage = (array_key_exists('error_message', $result)) ? $result['error_message'] : "Undefined error.";

			$this->response(array(
				 'success'	=>  FALSE,
				 'message' => ($id === 0) ? 'Fail to insert data.'.$strErrorMessage : 'Fail to update data.'.$strErrorMessage
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}
	}

	public function item_delete($strParam, $id)
	{
		if (isset($id) && $id !== 0) {
			$result = $this->Mportal->delete($strParam, $id);
		} else $result = [];

		if ($result) {

			$this->response(array(
				 'success'	=>  TRUE,
				 'message' => 'Data deleted',
				 'id' => $result
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}else{

			$this->response(array(
				 'success'	=>  FALSE,
				 'message' => 'Fail to delete data'
				), 
				\Restserver\Libraries\REST_Controller::HTTP_OK);

		}
		
	}


	public function fetch_post($strParam)
	{
		
	}

	private function __debug()
	{
		$this->Mportal->db->last_query();
	}

}