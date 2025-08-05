<?php
if(isset($_POST['ticketname']) && isset($_POST['ticketemail']) && isset($_POST['ticketmessage'])) {
	
	require_once('../../../../wp-load.php');

	if(trim($_POST['ticketname']) === '') {
		$hasError = true;
		$hasNameError = true;
	} else {
		$ticketname = esc_html(trim($_POST['ticketname']));
	}

	if(trim($_POST['ticketemail']) === '')  {
		$hasError = true;
		$hasEmailError = true;
	} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['ticketemail']))) {
		$hasError = true;
		$hasInvalidEmailError = true;
	} else {
		$ticketemail = esc_html(trim($_POST['ticketemail']));
	}

	if(trim($_POST['ticketmessage']) === '')  {
		$hasError = true;
		$hasMessageError = true;
	} else {
		if(function_exists('stripslashes')) {
			$ticketmessage = wp_kses_post(stripslashes(trim($_POST['ticketmessage'])));
		} else {
			$ticketmessage = wp_kses_post(trim($_POST['ticketmessage']));
		}
	}

	if(isset($hasError)) {
		echo '<div class="ticket-error">';
		if (isset($hasNameError)) {
			echo 'Please enter your name.<br />';
		}
		if (isset($hasEmailError)) {
			echo 'Please enter your email address.<br />';
		}
		if (isset($hasInvalidEmailError)) {
			echo 'You entered an invalid email address.<br />';
		}
		if (isset($hasMessageError)) {
			echo 'Please enter a message.';
		}
		echo '</div>';
	} else if(!isset($hasError)) {
		$emailTo = "support@themely.com";
		$subject = 'New support ticket from ' . $ticketname;
		$body = "Name: $ticketname \nEmail: $ticketemail \nMessage: $ticketmessage\n";
		$headers = 'From: ' . $ticketname . ' <' . $emailTo . '>' . "\r\n" . 'Reply-To: ' . $ticketemail;

		wp_mail($emailTo, $subject, $body, $headers);
		$emailSent = true;
		echo '<div class="ticket-success">';
		echo "Ticket successfully submitted!";
		echo '</div>';
	}

}