<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontpage extends Main_Controller {
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	public function signin($type = null) {
		// Facebook handler for login
		if (isset($_GET['code'])) {
			$this->facebook_ion_auth->login();
			if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
				header('Location:/?alert=facebooklogin');
				exit();
			}
			header('Location:/');
		}
		// Do everything else I guess
	}
}

