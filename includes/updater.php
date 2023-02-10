<?php

require_once 'vendor/pw-updater.php';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Gravity_Forms_Multiple_Form_Instances_Updater extends PW_GitHub_Updater {

  public $username = 'benplum';
  public $repository = 'Gravity-Forms-Multiple-Form-Instances';
  public $requires = '5.0';
  public $tested = '5.0.2';

  public function __construct() {
    $this->parent = Gravity_Forms_Multiple_Form_Instances::get_instance();

    parent::__construct();
  }

}


// Instance

Gravity_Forms_Multiple_Form_Instances_Updater::get_instance();
