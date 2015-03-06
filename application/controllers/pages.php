<?php defined('BASEPATH') OR exit('No direct script access allowed');


/* 
 * Pages Controller
 *
 * This script is the controller for the static pages on the site. Based on the CI tutorial.
 *
 * Version 1.4.5 (2014 04 23 1530)
 * Edited by Sean Wittmeyer (sean@zilifone.net)
 * 
 */

class Pages extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//$this->load->library('ion_auth');
	}

	public function index()
	{
		// Welcome to Beagle, here is our homepage
		$this->load->view('pages/home');
	}

	public function page($page = 'demo')
	{
		if ( ! file_exists('application/views/pages/'.$page.'.php')) show_404();
		$data['section'] = $page;
		$data['title'] = ucfirst($page); // Capitalize the first letter
		$this->load->view('pages/'.$page, $data);
	
	}
}
?>