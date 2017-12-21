<?php
/**
 * @package SP Page Builder Calendar Booking Addon
 * @author Alexander Yershov (https://www.upwork.com/o/profiles/users/_~0175333b8b4aefa403/)
*/
//no direct accees
defined ('_JEXEC') or die ('restricted access');

class SppagebuilderAddonBooking extends SppagebuilderAddons{

	public function render() {

		$class = (isset($this->addon->settings->class) && $this->addon->settings->class) ? $this->addon->settings->class : '';
		$title = (isset($this->addon->settings->title) && $this->addon->settings->title) ? $this->addon->settings->title : '';
		$heading_selector = (isset($this->addon->settings->heading_selector) && $this->addon->settings->heading_selector) ? $this->addon->settings->heading_selector : 'h3';

		//Addon options
		$recipient_email = (isset($this->addon->settings->recipient_email) && $this->addon->settings->recipient_email) ? $this->addon->settings->recipient_email : '';
		$from_email = (isset($this->addon->settings->from_email) && $this->addon->settings->from_email) ? $this->addon->settings->from_email : '';
		$from_name = (isset($this->addon->settings->from_name) && $this->addon->settings->from_name) ? $this->addon->settings->from_name : '';

		//Output
		$fieldset = file_get_contents(__DIR__ . '/form.php');
		
		$output  = '<div class="sppb-addon sppb-addon-booking ' . $class . '">';
		$output .= ($title) ? '<'.$heading_selector.' class="sppb-addon-title">' . $title . '</'.$heading_selector.'>' : '';
		$output .= '<div class="sppb-addon-content">';
		$output .= '<form role="form" id="booking-form" class="form-horizontal" enctype="multipart/form-data">';
		$output .= $fieldset;

		$output .= '<input type="hidden" name="recipient" value="'. base64_encode($recipient_email) .'">';
		$output .= '<input type="hidden" name="from_email" value="'. base64_encode($from_email) .'">';
		$output .= '<input type="hidden" name="from_name" value="'. base64_encode($from_name) .'">';

		$output .= '</form>';
		$output .= '<div style="display:none;margin-top:10px;" class="sppb-booking-form-status"></div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	public static function getAjax() {
		
		$input = JFactory::getApplication()->input;
		$mail = JFactory::getMailer();

		$inputs = $input->get('data', array(), 'ARRAY');

		foreach ($inputs as $input) {

			if( $input['name'] == 'firstname' ) {
				$firstname 					= $input['value'];
			}
			
			if( $input['name'] == 'lastname' ) {
				$lastname 					= $input['value'];
			}

			if( $input['name'] == 'address' ) {
				$address 					= $input['value'];
			}

			if( $input['name'] == 'phonenum' ) {
				$phonenum 					= $input['value'];
			}

			if( $input['name'] == 'phoneext' ) {
				$phoneext 					= $input['value'];
			}

			if( $input['name'] == 'emailfield' ) {
				$emailfield 				= $input['value'];
			}

			if( $input['name'] == 'datetime' ) {
				$datetime					= $input['value'];
			}

			
			if( $input['name'] == 'recipient' ) {
				$recipient 					= base64_decode($input['value']);
			}

			if( $input['name'] == 'from_email' ) {
				$from_email 				= base64_decode($input['value']);
			}

			if( $input['name'] == 'from_name' ) {
				$from_name 					= base64_decode($input['value']);
			}
		}

		$message .= 'Booking details:' . PHP_EOL;
		
		if (!empty($datetime)) {
			$message .= 'Booking date & time: ' . $datetime . PHP_EOL;
		}
		$message .= 'Name: ' . $firstname;
		$message .= (empty($lastname)) ? PHP_EOL : ' ' . $lastname . PHP_EOL;
		
		if (!empty($address)) {
			$message .= 'Address: ' . $address . PHP_EOL;
		}

		if (!empty($phonenum)) {
			$message .= 'Phone number: ' . $phonenum;
		}
		$message .= (empty($phoneext)) ? PHP_EOL : ' ext: ' . $phoneext . PHP_EOL;
		
		$message .= 'Email Address: ' . $emailfield . PHP_EOL;

		$message = nl2br( $message );

		$output = array();
		$output['status'] = false;

		$sender = array($emailfield, $firstname . ' ' . $lastname);
		
		if (!empty($from_email)) {
			$sender = array($from_email, $from_name);
			$mail->addReplyTo($emailfield, $firstname . ' ' . $lastname);
		}
		
		$mail->setSender($sender);
		$mail->addRecipient($recipient);
		$requid = date("n/j/y:B");
		$mail->setSubject('Booking'. ' #' . $requid);
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		$mail->setBody($message);

		if ($mail->Send()) {
			$output['status'] = true;
			$output['content'] = '<span class="sppb-text-success">'. 'Success!' .'</span>';
		} else {
			$output['content'] = '<span class="sppb-text-danger">'. 'Error!' .'</span>';
		}
		return json_encode($output);
	}

	public function stylesheets() {
		$app    = JFactory::getApplication();
		return array(
			JURI::base(true) . '/templates/' . $app->getTemplate() . '/sppagebuilder/addons/booking/assets/css/bootstrap-datetimepicker.min.css'
		);
	}

	public function scripts() {
		$app    = JFactory::getApplication();
		return array(
			JURI::base(true) . '/templates/' . $app->getTemplate() . '/sppagebuilder/addons/booking/assets/js/moment.min.js',
			JURI::base(true) . '/templates/' . $app->getTemplate() . '/sppagebuilder/addons/booking/assets/js/bootstrap-datetimepicker.min.js',
			JURI::base(true) . '/templates/' . $app->getTemplate() . '/sppagebuilder/addons/booking/assets/js/app.js'
		);
	}

	public function css() {
		$css = '.btn-default.active { background: #222 !important; }';
		return $css;
	}
}
