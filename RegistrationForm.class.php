<?php
/**
 * @file
 * A registration form built on Perseus.
 */
namespace Perseus\Extensions;

use Perseus\System\System;
use Perseus\Services\Form;
use Perseus\Services\Form\Item;
use Perseus\Services\MySQL;
use Perseus\Extensions\RegistrationSystemInstaller;
use Perseus\Services\PhpMail;

class RegistrationForm extends Form {

  //public $mail_template = 'registrationform/email';
  public $mail_template = '';
  //public $from = 'Shaun.Laws@nrel.gov';

  // Constructor
  public function __construct(array $settings = array()) {
    
    parent::__construct($settings);

    // Instantiate the database service.
    global $perseus;
    include($perseus->config_file);
    $perseus_db = new MySQL($perseus, $db);
    $perseus->setDb($perseus_db);

    // Instantiate the Installer and install.
    $installer = new RegistrationSystemInstaller($perseus);
    $installer->install();

    $provisions = '<strong>Provisions:</strong> Continental breakfast, lunch, and afternoon
                  breaks will be provided for each day.  Please indicate if you
                  will require a vegetarian meal for lunch or if you have any
                  other special dietary requests.';
    $contact = '<strong>Please submit this registration form no later than
                December 15th, 2013</strong><br /><br />
                If you have any questions or concerns please contact <strong>Morgan Beck</strong>:<br /><br />
                <strong>Phone:</strong>  (303) 384-6233<br />
                <strong>E-mail:</strong>  <a href="mailto:Morgan.beck@nrel.gov">Morgan.beck@nrel.gov</a>';

    // Build the form
    $this->createNameInput();
    $this->createAffiliationInput();
    $this->createAddressInput();
    $this->createCityInput();
    $this->createStateSelect();
    $this->createCountrySelect();
    $this->createZipInput();
    $this->createPhoneInput();
    $this->createFaxInput();
    $this->createEmailInput();
    //$this->createHtml('provisions', $provisions);
    //$this->createMealRadios();
    $this->createDietaryNeedTextarea();
    //$this->createHtml('contact', $contact);
    $this->createSubmit();

    // Run the validators and submittors.
    $this->executeForm();
  }


  /**
   * Create the address field.
   */
  private function createAddressInput() {
    $item = new Item\Text('address');
    $item->label = 'Address:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 128,
      'size'      => 39,
    );
    $item->weight = 3;
    $this->addChild('address', $item);
  }

  /**
   * Create the affiliation field.
   */
  private function createAffiliationInput() {
    $item = new Item\Text('affiliation');
    $item->label = 'Affiliation:';
    $item->required = TRUE;
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 128,
      'size'      => 39,
    );
    $item->weight = 2;
    $this->addChild('affiliation', $item);
  }

  /**
   * Create the affiliation field.
   */
  private function createCityInput() {
    $item = new Item\Text('city');
    $item->label = 'City:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 128,
      'size'      => 39,
    );
    $item->weight = 4;
    $this->addChild('city', $item);
  }

  /**
   * Create the select of US states.
   */
  private function createCountrySelect() {
    $select = new Item\Select('country', array(
      'label' => 'Country:',
      'weight' => 6,
      'options' => get_countries(),
      'wrap' => TRUE,
    ));
    $this->addChild('country', $select);
  }

  /**
   * Create the email field.
   */
  private function createDietaryNeedTextarea() {
    $item = new Item\Textarea('dietary_needs');
    $item->label = 'Other special dietary needs:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 255,
      'cols'      => 39,
      'rows'      => 5,
    );
    $item->weight = 1;
    $this->addChild('dietary_needs', $item);
  }

  /**
   * Create the email field.
   */
  private function createEmailInput() {
    $item = new Item\Text('mail');
    $item->label = 'E-mail:';
    $item->required = TRUE;
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 255,
      'size'      => 39,
    );
    $item->weight = 1;
    $this->addChild('mail', $item);
  }

  /**
   * Create the affiliation field.
   */
  private function createFaxInput() {
    $item = new Item\Text('fax');
    $item->label = 'Fax:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 20,
      'size'      => 39,
    );
    $item->weight = 1;
    $this->addChild('fax', $item);
  }

  /**
   * Create an HTML field.
   */
  private function createHtml($name, $html) {
    $data = array(
      'name' => $name,
      'html' => $html,
    );
    $this->addItem('html', $data);
  }

  /**
   * Create the affiliation field.
   */
  private function createMealRadios() {
    $data = array(
      'name' => 'meal',
      'label' => 'I will require a vegetarian meal:',
      'options' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'default' => 0,
    );
    $this->addItem('radios', $data);
  }

  /**
   * Create the name field.
   */
  private function createNameInput() {

    $item = new Item\Text('name');
    $item->label = 'First, Middle Initial & Last:';
    $item->required = TRUE;
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 128,
      'size'      => 39,
    );
    $item->weight = 1;
    $this->addChild('name', $item);
  }

  /**
   * Create the affiliation field.
   */
  private function createPhoneInput() {
    $item = new Item\Text('phone');
    $item->label = 'Phone:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 20,
      'size'      => 39,
    );
    $item->weight = 1;
    $this->addChild('phone', $item);
  }

  /**
   * Create the select of US states.
   */
  private function createStateSelect() {
    $select = new Item\Select('state', array(
      'label' => 'State/Province:',
      'weight' => 5,
      'options' => array_merge(get_us_states(), get_canadian_provinces()),
      'wrap' => TRUE,
    ));
    $this->addChild('select', $select);
  }

  /**
   * Create the select of US states.
   */
  private function createSubmit() {

    $submit = new Item\Submit('submit', array(
      'value' => 'Send',
      'weight' => 10,
      'wrap' => TRUE,
    ));
    $this->addChild('submit', $submit);
  }

  /**
   * Create the affiliation field.
   */
  private function createZipInput() {
    $item = new Item\Text('zip');
    $item->label = 'Zip/Postal Code:';
    $item->wrap = TRUE;
    $item->attribute = array(
      'maxlength' => 128,
      'size'      => 39,
    );
    $item->weight = 1;
    $this->addChild('zip', $item);
  }

  // Validate the form
  public function validate() {
    parent::validate();
  }

  // Submit the form
  public function submit() {
    global $perseus;

    parent::submit();

    // Take a copy of the data.
    $data = $this->data;

    //Converts the new line characters (\n) in the text area into HTML line breaks
    // (the <br /> tag).
    $data['dietary_needs'] = nl2br($data['dietary_needs']);

    // Store the submitted data.
    $perseus->db()->insert('registration', $data);

    // Get the field labels/data for the email body.
    foreach ($data as $label => $value) {
      if ('dietary_needs' == $label) {
        $label = 'Dietary needs';
      } elseif ('meal' == $label) {
        $value = (1 == $value) ? 'Yes' : 'No';
      }
      $submission .= ucfirst($label) . ': ' . $value . '<br />';
    }

    // Email the submitted data, if the site email has been set.
    /*if (!empty($perseus->settings['site_email']['mail'])) {
      $mailer = new PhpMail();
      $mailer->addRecipient($perseus->settings['site_email']['mail'], $perseus->settings['site_email']['name']);
      $mailer->from($data['mail'], $data['name']);
      $mailer->replyTo($data['mail'], $data['name']);
      $mailer->subject('BESC Characterization Workshop registration: ' . $data['name']);
      $body = 'The following information has been added to the BESC Characterization Workshop registration database:<br />';
      $body .= '<br />';
      $body .= $submission;
      pd($body);
      $args = array(
        'content' => $body,
      );
      //$themed_body = $perseus->theme($this->mail_template, $args);
      if ($themed_body) {
        $body = $themed_body;
      }
      $mailer->body($themed_body);
      
      if ($mailer->send()) {
        System::setMessage('Mail sent!');
      }
      else {
        System::setMessage('Error sending mail.', SYSTEM_ERROR);
      }
    } else {
      System::setMessage('Unable to email submission - site email not specified in settings/settings.php' . '.', SYSTEM_ERROR);
    }*/
  }

  public function submitted() {
    if ($this->state == self::VALID) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function getSubmittedData() {
    return $this->data;
  }
}