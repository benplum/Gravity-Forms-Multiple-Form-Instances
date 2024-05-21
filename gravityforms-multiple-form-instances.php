<?php
/*
Plugin Name: Gravity Forms: Multiple Form Instances
Description: Allows multiple instances of the same form to be run on a single page when using AJAX. Working fork of https://github.com/tyxla/Gravity-Forms-Multiple-Form-Instances.
Plugin URI: https://github.com/benplum/Gravity-Forms-Multiple-Form-Instances
Version: 2.0.4
Author: Ben Plum
Author URI: https://benplum.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * The main plugin class.
 */
class Gravity_Forms_Multiple_Form_Instances {

  protected static $instance;

  public $file = __FILE__;
  public $directory = '';

  public static function get_instance() {
    if ( empty( self::$instance ) && ! ( self::$instance instanceof Gravity_Forms_Multiple_Form_Instances ) ) {
      self::$instance = new Gravity_Forms_Multiple_Form_Instances();
    }

    return self::$instance;
  }

  /**
   * Constructor.
   *
   * Used to initialize the plugin and hook the related functionality.
   *
   * @access public
   */
  public function __construct() {
    // hook the HTML ID string find & replace functionality
    add_filter( 'gform_get_form_filter', array( $this, 'gform_get_form_filter' ), 10, 2 );
    add_filter( 'gform_confirmation', array( $this, 'gform_get_form_filter' ), 10, 2 );
  }

  /**
   * Replaces all occurrences of the form ID with a new, unique ID.
   *
   * This is where the magic happens.
   *
   * @access public
   *
   * @param string $form_string The form HTML string.
   * @param array $form Array with the form settings.
   * @return string $form_string The modified form HTML string.
   */
  public function gform_get_form_filter( $form_string, $form ) {
    // if form has been submitted, use the submitted ID, otherwise generate a new unique ID
    if ( isset( $_POST['gform_random_id'] ) ) {
      $random_id = absint( $_POST['gform_random_id'] ); // Input var okay.
    } else {
      $random_id = mt_rand(200000, 999999); // weird issue with leading '1'
    }

    // this is where we keep our unique ID
    $hidden_field = "<input type='hidden' name='gform_field_values'";

    // define all occurrences of the original form ID that wont hurt the form input
    $strings = array(
      "gchoice_" . $form['id'] . '_'                                      => "gchoice_" . $random_id . '_',
      // "for='choice_"                                                      => "for='choice_" . $random_id . '_',
      "for='choice_" . $form['id'] . '_'                                  => "for='choice_" . $random_id . '_',
      // "id='label_"                                                        => "id='label_" . $random_id . '_',
      "id='label_" . $form['id'] . '_'                                    => "id='label_" . $random_id . '_',
      "'gform_wrapper_" . $form['id'] . "'"                               => "'gform_wrapper_" . $random_id . "'",
      "'gf_" . $form['id'] . "'"                                          => "'gf_" . $random_id . "'",
      "'gform_" . $form['id'] . "'"                                       => "'gform_" . $random_id . "'",
      "'gform_ajax_frame_" . $form['id'] . "'"                            => "'gform_ajax_frame_" . $random_id . "'",
      '#gf_' . $form['id'] . "'"                                          => '#gf_' . $random_id . "'",
      "'gform_fields_" . $form['id'] . "'"                                => "'gform_fields_" . $random_id . "'",
      'id="field_' . $form['id'] . '_'                                    => 'id="field_' . $random_id . '_', // New quoting?
      "id='field_" . $form['id'] . '_'                                    => "id='field_" . $random_id . '_',
      "for='input_" . $form['id'] . '_'                                   => "for='input_" . $random_id . '_',
      "id='input_" . $form['id'] . '_'                                    => "id='input_" . $random_id . '_',
      "id='choice_" . $form['id'] . '_'                                   => "id='choice_" . $random_id . '_',
      "'gform_submit_button_" . $form['id'] . "'"                         => "'gform_submit_button_" . $random_id . "'",
      '"gf_submitting_' . $form['id'] . '"'                               => '"gf_submitting_' . $random_id . '"',
      "'gf_submitting_" . $form['id'] . "'"                               => "'gf_submitting_" . $random_id . "'",
      '#gform_ajax_frame_' . $form['id']                                  => '#gform_ajax_frame_' . $random_id,
      '#gform_wrapper_' . $form['id']                                     => '#gform_wrapper_' . $random_id,

      '"gform_wrapper_' . $form['id'] . '"'                               => '"gform_wrapper_' . $random_id . '"', // new
      '"gform_visibility_test_' . $form['id'] . '"'                       => '"gform_visibility_test_' . $random_id . '"', // new

      '#gform_' . $form['id']                                             => '#gform_' . $random_id,
      "trigger('gform_post_render', [" . $form['id']                      => "trigger('gform_post_render', [" . $random_id,
      'gformInitSpinner( ' . $form['id'] . ','                            => 'gformInitSpinner( ' . $random_id . ',',
      "trigger('gform_page_loaded', [" . $form['id']                      => "trigger('gform_page_loaded', [" . $random_id,
      "'gform_confirmation_loaded', [" . $form['id'] . ']'                => "'gform_confirmation_loaded', [" . $random_id . ']',
      'gf_apply_rules(' . $form['id'] . ','                               => 'gf_apply_rules(' . $random_id . ',',
      'gform_confirmation_wrapper_' . $form['id']                         => 'gform_confirmation_wrapper_' . $random_id,
      'gforms_confirmation_message_' . $form['id']                        => 'gforms_confirmation_message_' . $random_id,
      'gform_confirmation_message_' . $form['id']                         => 'gform_confirmation_message_' . $random_id,
      'if(formId == ' . $form['id'] . ')'                                 => 'if(formId == ' . $random_id . ')',

      'formId: "' . $form['id'] . '",'                                    => 'formId: "' . $random_id . '",',
      'formId: ' . $form['id']                                            => 'formId: ' . $random_id,
      "gform_post_render', [" . $form['id']                               => "gform_post_render', [" . $random_id,

      "window['gf_form_conditional_logic'][" . $form['id'] . ']'          => "window['gf_form_conditional_logic'][" . $random_id . ']',
      "trigger('gform_post_conditional_logic', [" . $form['id'] . ','     => "trigger('gform_post_conditional_logic', [" . $random_id . ',',
      'gformShowPasswordStrength("input_' . $form['id'] . '_'             => 'gformShowPasswordStrength("input_' . $random_id . '_',
      "gformInitChosenFields('#input_" . $form['id'] . '_'                => "gformInitChosenFields('#input_" . $random_id . '_',
      "jQuery('#input_" . $form['id'] . '_'                               => "jQuery('#input_" . $random_id . '_',
      'gforms_calendar_icon_input_' . $form['id'] . '_'                   => 'gforms_calendar_icon_input_' . $random_id . '_',
      "id='ginput_base_price_" . $form['id'] . '_'                        => "id='ginput_base_price_" . $random_id . '_',
      "id='ginput_quantity_" . $form['id'] . '_'                          => "id='ginput_quantity_" . $random_id . '_',
      'gfield_price_' . $form['id'] . '_'                                 => 'gfield_price_' . $random_id . '_',
      'gfield_quantity_' . $form['id'] . '_'                              => 'gfield_quantity_' . $random_id . '_',
      'gfield_product_' . $form['id'] . '_'                               => 'gfield_product_' . $random_id . '_',
      'ginput_total_' . $form['id']                                       => 'ginput_total_' . $random_id,
      'GFCalc(' . $form['id'] . ','                                       => 'GFCalc(' . $random_id . ',',
      'gf_global["number_formats"][' . $form['id'] . ']'                  => 'gf_global["number_formats"][' . $random_id . ']',
      'gform_next_button_' . $form['id'] . '_'                            => 'gform_next_button_' . $random_id . '_',
      $hidden_field                                                       => "<input type='hidden' name='gform_random_id' value='" . $random_id . "' />" . $hidden_field,
      //
      'gform_submit_button_' . $form['id']                                => 'gform_submit_button_' . $random_id,
      "'gform_post_render', [" . $form['id'] . ", 1])"                    => "'gform_post_render', [" . $random_id . ", 1])",
      'data-js-reload="field_' . $form['id'] . '_'                        => 'data-js-reload="field_' . $random_id . '_',

      ' gform_wrapper'                                                    => ' gform_wrapper gform_wrapper_original_id_' . $form['id'],

      'name="is_submit_' . $form['id']                => 'name="is_submit_' . $random_id,
      'name="state_' . $form['id']                    => 'name="state_' . $random_id,
      'name="gform_target_page_number_' . $form['id'] => 'name="gform_target_page_number_' . $random_id,
      'name="gform_source_page_number_' . $form['id'] => 'name="gform_source_page_number_' . $random_id,
      '#gform_source_page_number_' . $form['id']      => '#gform_source_page_number_' . $random_id
    );

    // allow addons & plugins to add additional find & replace strings
    $strings = apply_filters( 'gform_multiple_instances_strings', $strings, $form['id'], $random_id );

    // replace all occurrences with the new unique ID
    foreach ( $strings as $find => $replace ) {
      $form_string = str_replace( $find, $replace, $form_string );
    }

    return $form_string;
  }

}

// Instance

global $gravity_forms_multiple_form_instances;
$gravity_forms_multiple_form_instances = Gravity_Forms_Multiple_Form_Instances::get_instance();

$path = plugin_dir_path( __FILE__ );

include $path . 'includes/updater.php';
