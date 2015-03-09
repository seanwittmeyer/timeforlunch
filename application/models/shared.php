<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * Shared Model
 *
 * This model contains functions that are used in multiple parts of the site allowing
 * a single spot for them instead of having duplicate functions all over.
 *
 * Version 1.0 (2012.10.18.0017)
 * Edited by Sean Wittmeyer (sean@zilifone.net)
 * 
 */

class shared extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	/* send email
	public function start_mandrill()
	{
		// Mandrill setup
		$this->load->library('mandrill');
		$mandrill_ready = NULL;
		try {
			$this->mandrill->init( '-nnn' );
			$mandrill_ready = TRUE;
		} catch(Mandrill_Exception $e) {
			$mandrill_ready = FALSE;
		}
		return $mandrill_ready;
	}

	// Prepare and send a notification via mandrill
	public function go_mandrill($message,$recipients=false)
	{
		/* Send some email. Format the source as:
		$email = array(
			'html' => '<p>This is my message<p>', //Consider using a view file
			'text' => 'This is my plaintext message',
			'subject' => 'This is my subject',
			'from_email' => 'me@ohmy.com',
			'from_name' => 'Me-Oh-My',
			'to' => array(array('email' => 'joe@example.com' )) //Check documentation for more details on this one
			//'to' => array(array('email' => 'joe@example.com' ),array('email' => 'joe2@example.com' )) //for multiple emails
			);
		* /
		//show_error(serialize($message));
		$email = array(
			'html' => '<p>This is my message<p>', // Consider using a view file
			'text' => 'This is my plaintext message',
			'subject' => 'This is my subject',
			'from_email' => 'me@ohmy.com',
			'from_name' => 'Me-Oh-My',
			'to' => array(array('email' => 'joe@example.com' )) //Check documentation for more details on this one
			//'to' => array(array('email' => 'joe@example.com' ),array('email' => 'joe2@example.com' )) //for multiple emails
			);
		$this->mandrill->messages_send($message);
	}*/

	// Send an email, send in an id or the user object
	public function send_email($to,$subject,$message) {
	    $this->load->library('email');
		if (is_numeric($to)) {
			$to = $this->ion_auth->user($to)->row();
			if ($to === false) return false;
		} elseif (!is_object($to)) {
			return false;
		}

	    $defaults = array(
		    'site' => 'Time for Lunch - ',
		    'footer' => "<br><br>Sent by Time for Lunch."
	    );
	    // just in case
	    $this->email->clear();
	
	    $this->email->to($to->email);
	    $this->email->from('lunch@zilifone.net');
	    $this->email->subject($defaults['site'].$subject);
	    $this->email->message("Hi {$to->first_name},<br>{$message}{$defaults['footer']}");
	    $this->email->send();
	}

	// get order(s)
	public function get_orders($id=false,$where=false)
	{
		if ($id === false) {
			if ($where !== false)
			$this->db->where($where);
			$this->db->order_by("time", "desc");
			$query = $this->db->get('lunch_orders');
			$result = $query->result_array();
		} else {
			$query = $this->db->get_where('lunch_orders', array('id'=>$id));
			$result = $query->row_array();
		}
		if (empty($result)) return false;
		return $result;
	}

	// new order
	public function new_order($user,$details)
	{
		// validate
		$this->load->library('form_validation');
		$this->form_validation->set_rules('restaurant', 'restaurant', 'required');
		$this->form_validation->set_rules('order', 'order', 'required');
		$this->form_validation->set_rules('cost', 'cost', 'required');
		$this->form_validation->set_rules('tip', 'tip', 'required');
		$this->form_validation->set_rules('location', 'location', 'required');
		$this->form_validation->set_rules('phone', 'phone', 'required');
		$this->form_validation->set_rules('orderemail', 'Email', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)	{
			// fail
			$return = array(
				'result' => false,
				'error' => 'Everything is required.',
			);
			return $return;
		} else {
			// success
			$id = $user->id;
			$data = array(
				'phone' => $details['phone'],
				'location' => $details['location'],
				'first_name' => (isset($details['first_name'])) ? $details['first_name']: $user->first_name,
				'last_name' => (isset($details['last_name'])) ? $details['last_name']: $user->last_name,
			);
			$this->ion_auth->update($id, $data);
			$order = array(
				'restaurant' => $details['restaurant'],
				'order' => $details['order'],
				'cost' => $details['cost'],
				'tip' => $details['tip'],
				'location' => $details['location'],
				'email' => $user->email,
				'phone' => $details['phone'],
				'user' => $user->id,
				'trusted' => (isset($details['trusted'])) ? $details['trusted']: 'off',
				'prepaid' => (isset($details['prepaid'])) ? $details['prepaid']: 'off',
				'notes' => (isset($details['notes'])) ? $details['notes']: '',
				'time' => time(),
			);

			$query = $this->db->insert('lunch_orders',$order);
			$return = array(
				'result' => $query,
				'order' => $order,
			);
			return $return;
		}
	}

	// new user
	public function new_user($details)
	{
		// validate
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('orderpassword', 'Password', 'required');
		$this->form_validation->set_rules('orderemail', 'Email', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)	{
			// fail
			return false;
		} else {
			// login if user is already an user
			if ($this->ion_auth->email_check($details['orderemail'])) {
				$login = $this->ion_auth->login($details['orderemail'], $details['orderpassword'], true);
				if (!$login) return false;
			} else {
				$username = $details['orderemail'];
				$password = $details['orderpassword'];
				$email = $details['orderemail'];
				$additional_data = array(
					'first_name' => $details['first_name'],
					'last_name' => $details['last_name'],
					'phone' => $details['phone'],
					'location' => $details['location'],
				);
				$result = $this->ion_auth->register($username, $password, $email, $additional_data);
				if ($result === false) return false;
				$this->ion_auth->login($details['orderemail'], $details['orderpassword'], true);
			}

			$user = $this->ion_auth->user($result)->row();
			return $user;
		}
	}
	
	// Time Difference
	public function twitterdate($date)
	{
		if(empty($date)) {
		return "No date provided"; 
		}
		
		//$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$periods = array("s", "m", "h", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		
		$now = time();
		//$unix_date = strtotime($date);
		$unix_date = $date;
		// Check validity of date
		if(empty($unix_date)) {
		return "Bad date"; 
		}
		
		// Determine tense of date
		if($now > $unix_date) {
		$difference = $now - $unix_date;
		$tense = "ago"; } else {
		$difference = $unix_date - $now;
		$tense = "from now";
		}
		
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
		}
		
		$difference = round($difference);
		
		if($difference != 1) {
		//$periods[$j].= "s";
		}
		
		//return "$difference $periods[$j] {$tense}";
		return "$difference$periods[$j] {$tense}";
		
		/*
		$result = twitterdate($date);
		echo $result;
		*/
	}	

}