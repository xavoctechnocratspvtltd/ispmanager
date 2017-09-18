<?php

namespace xavoc\ispmanager;


/**
* 
*/
class Tool_HotspotRegistration extends \xepan\cms\View_Tool{
	public $options = [
					'after_login_url'=>"user_dashboard",
	];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$otp_number = $this->app->stickyGET('secret_opt_pass_code');
		$mobile_no = $this->app->stickyGET('mobile_no');

		if(!$otp_number){
			$registration_form = $this->add('Form',null,null,['form/empty']);
			$registration_form->setLayout(['form/hotspot-registration']);
			$registration_form->layout->add('View',null,'form_title')->set('Hotspot Sign Up');
			$registration_form->addField('Number','mobile_no','Mobile No')->validate('required');
			// $form->addField('Number','otp','OTP');

			$registration_form->addSubmit("Registration")->addClass('btn btn-success btn-lg text-center btn-block');

			if($registration_form->isSubmitted()){
				
				$c_s_m = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'country'=>'DropDown',
									'state'=>'DropDown',
								],
							'config_key'=>'DEFAULT_ISPMANAGER_COUNTRY_STATE_ID',
							'application'=>'ispmanager'
					]);
				$c_s_m->tryLoadAny();

				$user = $this->add('xavoc\ispmanager\Model_User');
				$user->addCondition('radius_username',$registration_form['mobile_no']);
				$user->tryLoadAny();
				if($user->loaded()){
					$registration_form->error('mobile_no','user is already register');
				}
										
				$user['first_name'] = $registration_form['mobile_no'];
				$user['last_name'] = " (Guest user)";
				$user['radius_username'] = $registration_form['mobile_no'];

				$user['otp_send_time']=$this->app->now;
				$user['radius_password'] = rand(999,999999);
				$user['status']="InActive";
				$user['otp_verified']=0;
				$user['country_id']=$c_s_m['country'];
				$user['state_id']=$c_s_m['state'];
				$user->save();
				
				$sms_model = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'otp_msg_content'=>'Text',
								],
							'config_key'=>'ISPMANAGER_OTP_CONTENT',
							'application'=>'ispmanager'
					]);
				$sms_model->tryLoadAny();
				
				// send SMS
				if($this->app->getConfig('send_sms',false)){
					$message = $sms_model['otp_msg_content'];
					$temp = $this->add('GiTemplate');
					$temp->loadTemplateFromString($message);
					$msg=$this->add('View',null,null,$temp);
					$msg->template->trySetHTML('otp_number',$user['radius_password']);
					
					if(!$sms_model['otp_msg_content']) throw new \Exception("Please update OTP SMS Content");
					$this->add('xepan\communication\Controller_Sms')->sendMessage($registration_form['mobile_no'],$msg->getHtml());

					$otp = "";
				}else{
					$otp = $user['radius_password'];
				}


				$registration_form->js(null,
										$registration_form->js()
												->univ()
													->successMessage('Password send on your mobile number '.$otp)
									)->reload(
										[
											'mobile_no'=>$user['radius_username'],
											'secret_opt_pass_code'=>$user['radius_password']
										]
									)->execute();
			}
		}else{

			$verify_form = $this->add('Form',null,null,['form/empty']);
			$verify_form->setLayout(['form/hotspot-registration']);
			$verify_form->layout->add('View',null,'form_title')->set('Hotspot Verification');
			$verify_form->addField('Number','mobile_no','Mobile No')->validate('required')->set($mobile_no);
			$verify_form->addField('Number','otp','OTP/Password')->validate('required');//->set($otp_number);

			$verify_form->addSubmit("Verify Password")->addClass('btn btn-success btn-lg text-center btn-block');
			
			if($verify_form->isSubmitted()){
				$user=$this->add('xavoc\ispmanager\Model_User');	
				$user->addCondition('radius_username',$verify_form['mobile_no']);
				$user->tryLoadAny();
				if(!$user->loaded())
					$verify_form->displayError('mobile_no','mobile number is not registered');

				if($verify_form['otp']!=$user['radius_password'])
					$verify_form->displayError('otp','OTP did not match');


				//OTP SMS Expired Config
				$otp_m = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'expired_time'=>'Number',
								],
							'config_key'=>'ISPMANAGER_OTP_EXPIRED',
							'application'=>'ispmanager'
					]);
				$otp_m->tryLoadAny();

				$date = date("Y-m-d H:i:s", strtotime("+".$otp_m['expired_time'] ."minutes",strtotime($user['otp_send_time'])));
				$current_date = $this->app->now;
				// echo $user['otp_send_time']. "<br/>";
				// echo $date. "<br/>";
				// echo $current_date . "<br/>";
				if (strtotime($date) < strtotime($current_date)) {
					$verify_form->displayError('otp',"This OTP/Password IS Expired");   
				}

				$defalut_plan_model = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'default_hotspot_plan'=>'DropDown',
								],
							'config_key'=>'ISPMANAGER_DEFAULT_HOTSPOT_PLAN',
							'application'=>'ispmanager'
					]);
				$defalut_plan_model->tryLoadAny();
				

				$user['status']="Active";
				$user['otp_verified']=1;
				$user['plan_id']=$defalut_plan_model['default_hotspot_plan'];
				$user->save();
				$user->active();
				
				$username = $verify_form['mobile_no'];
				if($this->app->getConfig('username_is_email',true)){
					if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
						$username .= "@isp-fake.com";
					}
				}
				$auth = $this->app->auth;
				$auth->login($username);
				
				
				$this->app->stickyForget('secret_opt_pass_code');
				$this->app->stickyForget('mobile_no');
				return $verify_form->js(null,$verify_form->js()->univ()->successMessage('your account is verified successfully'))->redirect($this->app->url($this->options['after_login_url']))->execute();
			}




		}
		
	}
}