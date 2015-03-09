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

			$email = $this->send_email($user,'Order Posted',"Great success, your {$order['restaurant']} order has been posted. You'll get an email when someone claims to deliver it! \nJust know there isn't any guarantee that it will be picked up and delivered. If no one claims it, you may want to go and get it yourself (and maybe deliver some other orders for karma!).");
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
	
	// update status
	public function update_status($order=false,$status='closed')
	{
		if ($order === false) {
			return false;
		} else {
			$data = array(
				'status' => $status,
			);
			if ($status == 'complete') $data['timesuccess'] = time();
			$this->db->where('id', $order);
			$this->db->update('lunch_orders', $data); 


			$order = $this->get_orders($order);
			if ($status == 'complete') {
				$orderuser = $this->ion_auth->user($order['user'])->row();
				$claimuser = $this->ion_auth->user($order['claimuser'])->row();
				$claimuseremail = $this->send_email($claimuser,'Order Delivered',"Your delivery to {$orderuser->first_name} {$orderuser->last_name} has been marked as 'delivered' and you are officially awesome.");
				$orderuseremail = $this->send_email($orderuser,'Order Delivered',"Your order was delivered by {$claimuser->first_name} {$claimuser->last_name} and has been marked as 'delivered'. Great success! Next time, try delivering an order to gain karma!");
			}
			if ($status == 'open') {
				$orderuser = $this->ion_auth->user($order['user'])->row();
				$claimuser = $this->ion_auth->user($order['claimuser'])->row();
				$claimuseremail = $this->send_email($claimuser,'Order Un-claimed',"You are no longer set to deliver {$orderuser->first_name} {$orderuser->last_name}'s order. Sad day.");
				$orderuseremail = $this->send_email($orderuser,'Order Un-claimed',"{$claimuser->first_name} {$claimuser->last_name} has unclaimed your order. It is still on the site but you may want to consider getting it yourself or removing it if it has been more than an hour since you first posted it. Sorry, but sometimes life gives you lemons.");
			}
			return true;
		}
	}

	// claim order
	public function claim_order($order=false)
	{
		// need to be logged in
		if (!$this->ion_auth->logged_in()) return false;
		// get out claim user
		$user = $this->ion_auth->user()->row();
		if ($order === false || $user === false) {
			return false;
		} else {
			$data = array(
				'status' => 'inprogress',
				'timeclaimed' => time(),
				'claimuser' => $user->id,
			);
			$this->db->where('id', $order);
			$this->db->update('lunch_orders', $data); 

			$order = $this->get_orders($order);
			if (empty($order) || is_numeric($order)) return false;
			$orderuser = $this->ion_auth->user($order['user'])->row();
			$claimuser = $this->ion_auth->user($order['claimuser'])->row();
			
			//print_r(array('order'=>$order,'claimuser'=>$claimuser,'orderuser'=>$orderuser));die;
			
			// email to the claim user
			$claimuseremail = $this->send_email(
				$claimuser,
				'Order Claimed',
				"Great work, you claimed {$orderuser->first_name} {$orderuser->last_name}'s order from {$order['restaurant']}.<br><br>
				Restaurant: {$order['restaurant']}<br>Order Detail: {$order['order']}<br>Cost: \${$order['cost']}<br>Tip: {$order['tip']}<br>Location: {$order['location']}<br>Notes: {$order['notes']}<br><br>
				Name: {$orderuser->first_name} {$orderuser->last_name}<br>Phone Number: {$order['phone']} (be nice)<br>Email: {$order['email']}<br><br>
				Thank you, you are the reason this all works. Karma for you!!!"
			);
			
			// email to order creator
			$orderuseremail = $this->send_email(
				$orderuser,
				'Your order is on it\'s way',
				"Guess what! {$claimuser->first_name} {$claimuser->last_name} claimed your order from {$order['restaurant']}. The following details were sent to them.<br><br>
				Restaurant: {$order['restaurant']}<br>Order Detail: {$order['order']}<br>Cost: \${$order['cost']}<br>Tip: {$order['tip']}<br>Location: {$order['location']}<br>Notes: {$order['notes']}<br><br>
				Details about who claimed your order:<br>Name: {$claimuser->first_name} {$claimuser->last_name}<br>Phone Number: {$claimuser->phone}<br>Email: {$claimuser->email}<br><br>
				Have your cash/money ready and be sure to mark your order as delivered when you get it."
			);

			return true;
		}
	}

	// Time Difference
	public function twitterdate($date)
	{
		if(empty($date)) {
			return "No date provided"; 
		}
		
		//$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$periods = array("s", "m", "h", "d", "w", " month", " year", " decade");
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