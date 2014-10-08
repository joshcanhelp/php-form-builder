<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>PHPFormBuilder test</title>
</head>

<body>


<?php

require_once( 'PhpFormBuilder.php' );

/*
Create a new instance
Pass in a URL to set the action
*/
$form = new PhpFormBuilder();

/*
Form attributes are modified with the set_att function.
First argument is the setting
Second argument is the value
*/

$form->set_att('method', 'post');
$form->set_att('enctype', 'multipart/form-data');
$form->set_att('markup', 'html');
$form->set_att('class', 'class_1');
$form->set_att('class', 'class_2');
$form->set_att('id', 'a_contact_form');
$form->set_att('novalidate', true);
$form->set_att('add_honeypot', true);
$form->set_att('add_nonce', 'a_contact_form');
$form->set_att('form_element', true);
$form->set_att('add_submit', true);


/*
Uss add_input to create form fields
First argument is the name
Second argument is an array of arguments for the field
Third argument is an alternative name field, if needed
*/
$form->add_input( 'Name', array(
	'request_populate' => false
), 'contact_name' );

$form->add_input( 'Email', array(
	'type' => 'email',
	'class' => array( 'class_1', 'class_2', 'class_3' )
), 'contact_email' );

$form->add_input( 'Filez', array(
	'type' => 'file'
), 'filez_here' );

$form->add_input( 'Should we call you?', array(
	'type'  => 'checkbox',
	'value' => 1
) );

$form->add_input( 'True or false', array(
	'type'    => 'radio',
	'checked' => false,
	'value'   => 1
) );

$form->add_input( 'Reason for contacting', array(
	'type'    => 'checkbox',
	'options' => array(
		'say_hi'     => 'Just saying hi!',
		'complain'   => 'I have a bone to pick',
		'offer_gift' => 'I\'d like to give you something neat',
	)
) );

$form->add_input( 'Bad Headline', array(
	'type'    => 'radio',
	'options' => array(
		'say_hi_2'     => 'Just saying hi! 2',
		'complain_2'   => 'I have a bone to pick 2',
		'offer_gift_2' => 'I\'d like to give you something neat 2',
	)
) );

$form->add_input( 'Reason for contact', array(
	'type'    => 'select',
	'options' => array(
		''           => 'Select...',
		'say_hi'     => 'Just saying hi!',
		'complain'   => 'I have a bone to pick',
		'offer_gift' => 'I\'d like to give you something neat',
	)
) );

$form->add_input( 'Question or comment', array(
	'required' => true,
	'type'     => 'textarea',
	'value'    => 'Type away!'
) );

$form->add_inputs( array(
	array( 'Field 1' ),
	array( 'Field 2' ),
	array( 'Field 3' )
) );

/*
Create the form
*/
$form->build_form();

/*
 * Debugging
 */
echo '<pre>';
print_r( $_REQUEST );
echo '</pre>';
echo '<pre>';
print_r( $_FILES );
echo '</pre>';
?>
</body>
</html>