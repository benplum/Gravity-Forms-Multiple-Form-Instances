<?php

require_once 'vendor/pw-updater.php';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Media_Sync_Updater extends PW_GitHub_Updater {

  public $username = 'benplum';
  public $repository = 'Media Sync';
  public $requires = '5.0';
  public $tested = '5.0.2';

  public function __construct() {
    $this->parent = Media_Sync::get_instance();

    parent::__construct();
  }

}


// Instance

Media_Sync_Updater::get_instance();
