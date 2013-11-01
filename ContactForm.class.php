<?php
/**
 * @file
 * A contact form built on Perseus.
 */
namespace Perseus\Extensions;

use Perseus\System as Perseus;
use Perseus\System\System;
use Perseus\Services\Form;
use Perseus\Services\Form\Item;
use Perseus\Services\PhpMail;

class ContactForm extends Form {
  public $mail_template = 'contactform/email';
  public $from = 'you@example.com';

  // Constructor
  public function __construct(array $settings = array()) {
    parent::__construct($settings);

    // Build the From field
    $from = new Item\Text('from');
    $from->label = 'Your Name';
    $from->required = TRUE;
    $from->wrap = TRUE;
    $from->weight = 1;
    $this->addChild('from', $from);

    // Build the email field
    $mail = new Item\Text\Email('mail');
    $mail->required = TRUE;
    $mail->wrap = TRUE;
    $mail->weight = 2;
    $this->addChild('mail', $mail);

    // Build the subject field.
    $sub = new Item\Text('subject');
    $sub->label = 'Subject';
    $sub->required = TRUE;
    $sub->weight = 3;
    $sub->wrap = TRUE;
    $this->addChild('subject', $sub);

    // Build the message field.
    $message = new Item\Textarea('message', array(
      'label' => 'Your Message',
      'weight' => 4,
      'required' => TRUE,
    ));
    $this->addChild('message', $message);

    $select = new Item\Select('choose', array(
      'label' => 'Choose One',
      'weight' => 5,
      'options' => array(
        'one' => 'Number one',
        'two' => 'Number Two',
        'three' => 'Number THree',
      ),
      'wrap' => TRUE,
    ));
    $this->addChild('select', $select);

    // Build the Submit button
    $submit = new Item\Submit('op', array(
      'value' => 'Send',
      'weight' => 10,
      'wrap' => TRUE,
    ));
    $this->addChild('submit', $submit);

    // Run the validators and submittors.
    $this->executeForm();
  }

  // Validate the form
  public function validate() {
    parent::validate();
  }

  // Submit the form
  public function submit() {
    global $perseus;

    parent::submit();
    $mail = new PhpMail();
    $mail->from($this->data['mail'], $this->data['from']);
    $mail->addRecipient($this->from);
    $mail->subject($this->data['subject']);

    // Build the body.
    $args = array(
      'content' => $this->data['message'],
    );
    $body = $perseus->theme($this->mail_template, $args);
    if (!$body) {
      $body = $this->data['message'];
    }
    $mail->body($body);

    if ($mail->send()) {
      System::setMessage('Mail sent!');
    }
    else {
      System::setMessage('Error sending mail.', SYSTEM_ERROR);
    }
  }
}
