<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * API Controller
 *
 * This is the controller that handles all of the API calls. It handles calls 
 * authenticated by key, session user, or the public..
 *
 * Version 1.4.5 (2014 04 23 1530)
 * Edited by Sean Wittmeyer (sean@zilifone.net)
 * 
 */

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('api_model');
	}

	public function index()
	{
		redirect('help', 'refresh');
	}

	public function status($id=false,$status='open')
	{
		if ($id===false) {
			$this->output->set_status_header('404');
			print json_encode(array('result'=>'404','type'=>'error','message'=>"You forgot to specify a valid order ID (usually a number)."));
			die;
		}
		$result = $this->shared->update_status($id,$status);
		if ($result) {
			$this->output->set_status_header('403');
			print json_encode(array('result'=>'403','type'=>'error','message'=>"Update failed."));
		} else {
			$this->output->set_status_header('200');
			print json_encode(array('result'=>'200','type'=>'success','message'=>"Order status updated as {$status}!"));
		}
	}

	public function claim($id=false)
	{
		if ($id===false) {
			$this->output->set_status_header('404');
			print json_encode(array('result'=>'404','type'=>'error','message'=>"You forgot to specify a valid order ID (usually a number)."));
			die;
		}
		if (!$this->ion_auth->logged_in()) {
			$this->output->set_status_header('403');
			print json_encode(array('result'=>'403','type'=>'error','message'=>"You can't claim orders unless you are logged in."));
			die;
		}
		$result = $this->shared->claim_order($id);
		$this->output->set_status_header('200');
		print json_encode(array('result'=>'200','type'=>'success','message'=>"Order claimed!"));
		
	}

	public function success($id=false)
	{
		if ($id===false) {
			$this->output->set_status_header('404');
			print json_encode(array('result'=>'404','type'=>'error','message'=>"You forgot to specify a valid order ID (usually a number)."));
			die;
		}
		if (!$this->ion_auth->logged_in()) {
			$this->output->set_status_header('403');
			print json_encode(array('result'=>'403','type'=>'error','message'=>"You can't claim orders unless you are logged in."));
			die;
		}
		$result = $this->shared->update_status($id, 'complete');
		$this->output->set_status_header('200');
		print json_encode(array('result'=>'200','type'=>'success','message'=>"Order marked as delivered!"));
	}

	public function unclaim($id=false)
	{
		if ($id===false) {
			$this->output->set_status_header('404');
			print json_encode(array('result'=>'404','type'=>'error','message'=>"You forgot to specify a valid order ID (usually a number)."));
			die;
		}
		if (!$this->ion_auth->logged_in()) {
			$this->output->set_status_header('403');
			print json_encode(array('result'=>'403','type'=>'error','message'=>"You can't unclaim orders unless you are logged in."));
			die;
		}
		$result = $this->shared->update_status($id, 'open');
		$this->output->set_status_header('200');
		print json_encode(array('result'=>'200','type'=>'success','message'=>"Order unclaimed, sad day."));
	}

	public function orders($id=FALSE)
	{
		// Get all orders
		if ($id === FALSE) {
			$orders = $this->shared->get_orders();
			$statusheader = ($orders) ? '200': '403';
			$this->output->set_status_header($statusheader);
			print json_encode($orders);
		} elseif ($id == 'new') {
			$post = $this->input->post();
			// use isset(post[user]) to validate request 
			if (isset($post['user'])) {
				// user id is set (a number)
				if (is_numeric($post['user'])) {
					// user is set but not logged in
					if (!$this->ion_auth->logged_in()) {
						$this->output->set_status_header('403');
						print json_encode(array('result'=>'403','type'=>'error','message'=>"You can't post orders for an existing user unless you are logged in."));
						die;
					}
					// logged in
					$user = $this->ion_auth->user()->row();
					// user mismatch
					if ($post['user'] !== $user->id) {
						$this->output->set_status_header('403');
						print json_encode(array('result'=>'403','type'=>'error','message'=>"You can't post orders for other people."));
						die;
					}
					$result = $this->shared->new_order($user,$post);
					$statusheader = ($result['result']) ? '200': '403';
					$this->output->set_status_header($statusheader);
					if ($result['result']) {
						print json_encode($result['order']);
					} else {
						print json_encode(array('result'=>'403','type'=>'error','message'=>"Order was not posted, make sure the order details have all been filled out."));
					}
					die;
				} 
				// user is new
				elseif ($post['user'] == 'anon') {
					$flag = false;
					$user = $this->shared->new_user($post);
					// user was created 
					if (is_object($user)) {
						$result = $this->shared->new_order($user,$post);
						$statusheader = ($result['result']) ? '200': '403';
						$this->output->set_status_header($statusheader);
						if ($result['result']) {
							print json_encode($result['order']);
						} else {
							print json_encode(array('result'=>'403','type'=>'error','message'=>"The user was created but the order was not posted, make sure the order details have all been filled out."));
						}
						die;
					} else {
						$this->output->set_status_header('403');
						print json_encode(array('result'=>'403','type'=>'error','message'=>"User could not be created, make sure to include an email and password. As a result, the order was not placed either."));
						die;
					}
				}
				// error for non anon user setting
				else {
					$this->output->set_status_header('403');
					print json_encode(array('result'=>'403','type'=>'error','message'=>"Invalid user. If not logged in, use the value 'anon' and specify an email and password to create an account with the order."));
					die;
				}
			} else {
				// user field wasn't set so we'll spit out an error
				$this->output->set_status_header('403');
				print json_encode(array('result'=>'403','type'=>'error','message'=>"The user field is required. If not logged in, use the value 'anon' and specify an email and password to create an account with the order."));
				die;
			}
		} elseif (is_numeric($id)) {
			$orders = $this->shared->get_orders($id);
			$statusheader = ($orders) ? '200': '403';
			$this->output->set_status_header($statusheader);
			print json_encode($orders);
		} else {
			$statusheader = '404';
			$this->output->set_status_header($statusheader);
			print json_encode(array('result' => '404','message'=>'Nothing happened, probably an invalid order id.'));
		}	
	}
}
