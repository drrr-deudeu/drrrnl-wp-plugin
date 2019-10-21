<?php
/*
Plugin Name: Drrr News Letter Request
Plugin URI: http://wordpress/
Description: Allows the visitor to request to receive the newsletter. After install, create a public page with the short
 code: [drrrnl_form] for the form and an admin page with the short code [drrrnl_list] for the list.
Author: drrr
Version: 1.0
Author URI: http://wordpress/
 */

/* mandatory for wp_create_category usage */
require_once(ABSPATH. '/wp-admin/includes/taxonomy.php');

/* Contact Form Function treatment */
function drrrnl_form_treatment(){

	// Permet de styliser le form et les feedbacks
	wp_register_style('drrr_nl-css', // Il faut un "slug" ici. Evites un nom de fichier
					plugin_dir_url(__FILE__) . 'drrr_nl.css',
					'', // XXX ne rien passer
					false);
	wp_enqueue_style('drrr_nl-css');


	// XXX Ces var pourraient etre stockées dans un array
	$drrrnl_post_title = 'Drrr News Letter Request';
	$drrrnl_html_fields = array(
		'lastname' => 'drrrnl_LastName',
		'firstname' => 'drrrnl_FirstName',
		'email' => 'drrrnl_Email',
		'pro' => 'drrrnl_pro',
		'submit' => 'drrrnl_submit',
	);
	$user_inputs = array(
				'lastname' 	=> '',
				'firstname' 	=> '',
				'email'   	=> '',
				'pro' 	  	=> '',
				'submit'	=> 'unsubmitted'
	);
	foreach(array('lastname','firstname','email') as $field){
		// Ajout et init des variables "messages d'erreur" nécessaires à drrr-nlrequest.php
		add_option('drrrnl_error_'.$field,'','','no');
		update_option('drrrnl_error_'.$field,'',null);
		// Ajout des variables "valeurs par défaut" du formulaire
		// Ainsi, en cas d'erreur de saisie, les valeurs déjà saisies seront affichées par drrr-nlrequest.php
		add_option('drrrnl_value_'.$field,'','','no');
	}

	// POUR TRAITER CHAQUE SAISIE DU FORM
	// We check if the requester has submitted the form
	if( isset($_POST[$drrrnl_html_fields['submit']])){
		// The requester submitted the form
		$user_inputs['submit'] = 'submitted';

		// Récupération du nom et du prenom
		foreach (array('lastname','firstname') as $field){
			if(isset($_POST[$drrrnl_html_fields[$field]])){
				$user_inputs[$field] = sanitize_text_field($_POST[$drrrnl_html_fields[$field]]);
				update_option('drrrnl_value_'.$field,$user_inputs[$field],null);
				if(strlen($user_inputs[$field])< 2){
					$user_inputs['submit'] = 'unvalidated';
					update_option('drrrnl_error_'.$field,'2 lettres minimum',null);
				}
			}
			else {
				$user_inputs['submit'] = 'unvalidated';
			}
		}

		// Récupération de l'email
		if(isset($_POST[$drrrnl_html_fields['email']])){
			$user_inputs['email'] = sanitize_text_field($_POST[$drrrnl_html_fields['email']]);
			update_option('drrrnl_value_email',$user_inputs['email'],null);
			if(!is_email($user_inputs['email'])){
				$user_inputs['submit'] = 'unvalidated';
				update_option('drrrnl_error_email', 'Invalid email address',null);
			}
			elseif(drrrnl_email_already_in_use($user_inputs['email'],$drrrnl_post_title) == true){
				$user_inputs['submit'] = 'unvalidated';
				update_option('drrrnl_error_email', "Email d&eacute;j&agrave; dans nos bases",null);
			}
		}
		else {
			$user_inputs['submit'] = 'unvalidated';
		}

		if(isset($_POST['drrrnl_Pro']))
			$user_inputs['pro'] = "pro";
		else
			$user_inputs['pro'] = "";

		// XXX Si tout s'est bien passé on enregistre la demande et informe l'admin
		if($user_inputs['submit'] == 'submitted'){
			$t_form = '<p> Welcome and thanks for your interest '.$user_inputs['firstname']
						.' '
						.$user_inputs['lastname']
						. '<br/>Your request has been sent to the webmaster<br/>'
						. 'Your email address is : '
						. $user_inputs['email']
						. '</p>';

			$save_demande = drrrnl_save_alert_post($user_inputs,$drrrnl_post_title);

			// Envoi email admin.
			// Si l'enregistrement ne s'est pas bien passé dans WP, un message d'alerte
			// est envoyé à l'admin pour action.
			// En effet on vient quand meme de dire a l'user que tout s'était bien passé et que l'admin
			// serait informé de sa demande
			drrrnl_send_alert_to_admin($user_inputs, $save_demande);

			// On clean ces 3 variables sinon des effets indésirables sont ressentis lors des tests
			foreach(array('lastname','firstname','email') as $field)
			       update_option('drrrnl_value_'.$field,'',null);

			// Finalement on renvoie ce texte pour affichage
			return ($t_form);
		}
	}

	// If the user request isn't validated we display the form
	if($user_inputs['submit'] != 'submitted'){
		// Construction du cache d'affichage
		ob_start();
		include( __DIR__ . '/drrr-nlpage.php');
		$t_form = ob_get_contents();
		ob_end_clean();
		// On retourne pour affichage
		return($t_form);
	}
}

/* Renvoie le nom de la category associé à ce plugin */
function drrrnl_get_category_post_name()
{
		return("nl contact");
}

/* Cette fonction retourne l'ID de la categorie dans laquelle sont rangés nos posts */
function drrrnl_getcatid_contact()
{
		/* On peut appeler directement le create qui rend l'ID de la category */
		/* si elle existe déjà et qui sinon la crée */
		/* Si la category n'existe pas et n'a pu être crée, 0 est renvoyé */
		return(wp_create_category(drrrnl_get_category_post_name()));
}

// XXX Enregistre la demande de souscription dans WP
function drrrnl_save_alert_post($user_inputs,$title){
	$drrrnl_message = "";
	foreach(array('lastname','firstname','email','pro') as $field)
		$drrrnl_message .= $user_inputs[$field].", ";
	$drrrnl_message .= " new request";

	$catid = drrrnl_getcatid_contact();

	$wp_insert_post = wp_insert_post(
		array(
			'post_title'		=> $title,
			'post_status'		=>'publish',
			'post_content' 		=> $drrrnl_message,
			'post_content_filtered' => $user_inputs['email'],
			'meta_input' => array('email' => $user_inputs['email'],
						'lastname' => $user_inputs['lastname'],
						'firstname' => $user_inputs['firstname'],
						'pro' => $user_inputs['pro'],
						'validated' => false)
		)
	);
	if($wp_insert_post){
			$term_taxonomy_id = wp_set_object_terms($wp_insert_post,array(drrrnl_get_category_post_name()),'category');
	}
	// Cette fonction return le post ID ou 0 ou nul ou un objet WP_ERROR
	return $wp_insert_post;
}

/* Send alert mail to admin user */
function drrrnl_send_alert_to_admin($user_inputs, $postid){
	$drrrnl_message = "";
	$error_request = false;
	if($postid == null or $postid == 0 or is_wp_error($postid) == true){
		$drrrnl_message = "ERROR !!! WE CAN'T SAVE AN USER REQUEST. REASON: ";
		if(is_wp_error($postid)== true)
			$drrrnl_message .= $postid->get_error_message();
		else
			$drrrnl_message .= "Undetermined";
		$error_request = true;
	}
	$drrrnl_message = "A new user newsletter request has been sent by:\n";
	foreach(array('lastname','firstname','email','pro') as $field)
		$drrrnl_message .= $user_inputs[$field]." ";
	
	wp_mail(get_option( 'admin_email' ), ($error_request ? 'ERROR: ':'').'User Newsletter Request', $drrrnl_message, "", "");
}

/* list the existing contacts emails */
function drrrnl_requests_list($title)
{
	$user = wp_get_current_user();
	if( !is_user_logged_in() )
	{
		return("Not Authorized. Please connect first");
	}
	if(!current_user_can('administrator')){
			$ret_msg = "Insufficient rights "
					.$user->user_login
					.'<br/>';
			foreach($user->roles as $role)
					$ret_msg = $ret_msg
							 .$role
							 .', ';
		return($ret_msg);
	}
	$drrrnl_list 	= '<table class="drrrnl_list"><tr>';
	$drrrnl_bottom 	= '</table>';
	$drrrnl_args 	=	array(
					'numberposts' 	=> -1,
					'post_title' 	  => $title,
					'category' => drrrnl_getcatid_contact()
					);
	$drrrnl_posts = get_posts($drrrnl_args);

	if($drrrnl_posts == null || empty($drrrnl_posts)){
		$drrrnl_list .= $drrrnl_bottom;
		return $drrrnl_list;
	}
	foreach($drrrnl_posts as $drrrnl_post => $drrrnl_post_val){
		if($drrrnl_post_val != null && !empty($drrrnl_post_val)){
			$drrrnl_list .= '<tr>';
			$drrrnl_list .= '<td>';
			$drrrnl_list .= $drrrnl_post_val->post_content_filtered;
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '<td>';
			
			$drrrnl_list .= get_post_meta($drrrnl_post_val->ID,'lastname',true);
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '<td>';
			$drrrnl_list .= get_post_meta($drrrnl_post_val->ID,'firstname',true);
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '<td>';
			$drrrnl_list .= get_post_meta($drrrnl_post_val->ID,'email',true);
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '<td>';
			$drrrnl_list .= get_post_meta($drrrnl_post_val->ID,'pro',true);
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '<td>';
			$drrrnl_list .= get_post_meta($drrrnl_post_val->ID,'validated',true);
			$drrrnl_list .= '</td>';
			$drrrnl_list .= '</tr>';
		}
	}
	$drrrnl_list .= $drrrnl_bottom;
	return $drrrnl_list;
}

/* check if the this email address already exists in the contacts list */
function drrrnl_email_already_in_use( $email , $title )
{
	// XXX Il faut verifier si les params de la f() sont valides
	$drrrnl_args =	array(
		'numberposts' 			=> -1,
		'post_title' 			  => $title,
		'post_content_filtered' => $email,
		'category' => drrrnl_getcatid_contact()
	);
	$drrrnl_posts = get_posts($drrrnl_args);

	if($drrrnl_posts == null || empty($drrrnl_posts)) return false;

	foreach($drrrnl_posts as $drrrnl_post => $drrrnl_post_val){
		if($drrrnl_post_val != null && !empty($drrrnl_post_val)){

			if(	$drrrnl_post_val->post_content_filtered == $email )  return true;

		}
	}
	return false;
}




add_shortcode('drrrnl_form','drrrnl_form_treatment');
add_shortcode('drrrnl_list','drrrnl_requests_list');
?>
