<?php


if (! defined('BASEPATH')) exit('No direct script access');





class Member extends CI_Model 


{


	function __construct()


	{


        parent::__construct();


		$this->load->database();


	}

	function checkAdminExist($username, $password)


	{		

		$query = $this->db->get_where('admin', array('username' => mysql_real_escape_string($username), 'password' => mysql_real_escape_string(md5($password))));

		$data = $query->row_array();


		$count = $query->num_rows();


		


		if($count>0)


			return $data;


		else


			return false;


	}


	


	function editUser($user_id)


	{


		$birthday = $this->input->post('u_b_yr')."-".$this->input->post('u_b_mon')."-".$this->input->post('u_b_day');	


		$data = array(


			'fname' => $this->input->post('fname'),


			'lname' => $this->input->post('lname'),


			'username' => addslashes($this->input->post('username')),


			'birthday' => $birthday,


			'height_cm' => $this->input->post('height_cm'),


			'sex' => $this->input->post('sex'),


			'seeking' => $this->input->post('seeking'),


			'prev_marriage' => $this->input->post('prev_marriage'),


			'children' => $this->input->post('children'),


			'ethnicity' => $this->input->post('ethnicity'),


			'education' => $this->input->post('education'),


			'religion' => $this->input->post('religion'),


			'smoking' => $this->input->post('smoking'),


			'drinking' => $this->input->post('drinking'),


			'body_type' => $this->input->post('body_type')


		); 





		


		$this->db->where('user_id', $user_id);


		$this->db->update('user', $data);


	}


	function editUserLocation($user_id)


	{


		$data = array(


			'country_id' => $this->input->post('country_id'),


			'state_id' => $this->input->post('state_id'),


			'city' => addslashes($this->input->post('city')),


			'address' => addslashes($this->input->post('address')),


			'zip' => $this->input->post('zip')


		); 





		


		$this->db->where('user_id', $user_id);


		$this->db->update('user', $data);


	}


	


	function editUserAbout($user_id)


	{


		$data = array(


			'your_story' => $this->input->post('your_story'),


			'perfect_match' => addslashes($this->input->post('perfect_match')),


			'ideal_first_date' => addslashes($this->input->post('ideal_first_date'))


		); 





		


		$this->db->where('user_id', $user_id);


		$this->db->update('user', $data);


	}


	


	function checkPrimary($user_id)


	{


		$query = $this->db->get_where('user_images', array('user_id' => $user_id ));


		$data = $query->row_array();


		$count = $query->num_rows();


		if($count>0)


			return 0;


		else


			return 1;


	}


	


	function addUserImage($user_id,$image_name,$primary)


	{


		$data = array(


			'user_id' => $user_id,


			'image_name' => $image_name,


			'primary' => $primary


		); 


				


		$this->db->insert('user_images', $data);


	}


	


	function makePrimary($user_image_id)


	{


		$data = array(


			'primary' => 1


		); 


		


		$this->db->where('user_image_id', $user_image_id);


		$this->db->update('user_images', $data); 


	}


	


	function makeSecondary($user_id)


	{


		$data = array(


			'primary' => '0'


		); 


		


		$this->db->where('user_id', $user_id);


		$this->db->update('user_images', $data); 


	}


	


	function getUserImgByImgid($user_image_id)


	{


		$query = $this->db->get_where('user_images', array('user_image_id' => $user_image_id ));


		$data = $query->row_array();


		$count = $query->num_rows();


		if($count>0)


			return $data;


		else


			return 0;


	}


	


	function deletePhoto($user_image_id)


	{


		$this->db->where('user_image_id', $user_image_id);


		$this->db->delete('user_images');


	}


	


	function editNextprimary($user_id)


	{


		$this->db->select('user_image_id as id');    


		$this->db->from('user_images');


		$this->db->where('user_id', $user_id);


		$this->db->order_by('user_image_id','asc');


		$this->db->limit(1);


		


		$query = $this->db->get();


		//echo $this->db->last_query();


		$data = $query->row_array();


		$count = $query->num_rows();


		


		//print_r( $data);


		


		if($count)


		{


			$dataarr = array(


				'primary' => 1


			); 


			


			$this->db->where('user_image_id',$data['id']);


			$this->db->update('user_images', $dataarr); 


		}


		


	}


	


	


	function getUserById($user_id)


	{


		$this->db->select('*');


		$this->db->from('customer as C');


		$this->db->where('C.customer_id',$user_id);





		$query = $this->db->get();


		


		$data = $query->row_array();


		$count = $query->num_rows();


		


		return ($count>0) ? $data : 0;


	}



	// email deyar por session e email set krbi...
	// controller code

		$currenTime = date('Y-m-d H:i:s');
		$forgotPassSession = $this->session->userdata('forgot-pass');
		$email = $forgotPassSession[0]['email'];
		$otp = $this->input->post('otp');

		$result = $this->member->checkOTP($email,$otp,$currenTime);
		if(!empty($result)) {
			redirect('home/change-password','refresh');
		} else {
			echo "invalid";
		}
	//model code
	function sendOTP($email) {
		$condition = array('status'=>'0', 'email'=>$email);
		$query = $this->db->get_where('admin',$condition);
		$res = $query->result_array();
		if(!empty($res)) {
			// email code
			$randomCode = mt_rand(100000,999999);
			mail();

			$time = time();
			$expireTime = date('Y-m-d H:i:s', strtotime('+5 minutes', $time));
			$insertArr = array('userId' => $res[0]['id'], 'otp'=>$randomCode, 'emailId'=>$email, 'expireTime'=>$expireTime);
			$this->db->insert('otp',$insertArr);
			return true;
		} else {
			return false;
		}
	}


	function checkOTP($email, $otp, $currentTime) {
		$this->db->select('otp');
		$this->db->from('otp');
		$this->db->where('email'=>$email);
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$sql = $this->db->get();
		$res = $sql->result_array();
		if(!empty($res)) {

			$to_time = strtotime($currentTime);
			$from_time = strtotime($res[0]['expireTime']);
			$diff = round(abs($to_time - $from_time) / 60,2)


			if($otp == $res[0]['otp'] && ($diff < 5)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function changePassword($email,$password) {
		$hashedPassword = password_hash(trim($password), PASSWORD_DEFAULT);
		$updateArr = array('password',$password);
		$sql = $this->db->update('otp',$updateArr,array('email'=>$email));
		if($sql) {
			return true;
		} else {
			return false;
		}

	}


}





?>
