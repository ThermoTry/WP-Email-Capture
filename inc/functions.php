<?php

/* function wp_email_capture_sanitize($string)
{

  $string = esc_attr($string);

  $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

  return $string;

}
*/


function wp_email_capture_checkIfPresent( $email ) {

  global $wpdb;

  $get_email = 'SELECT COUNT(*)

  FROM '. WP_EMAIL_CAPTURE_REGISTERED_MEMBERS_TABLE . ' WHERE email = "%s"';

  $prepared_get_email = $wpdb->prepare( $get_email, $email );

  $number_of_email_instances = $wpdb->get_var( $prepared_get_email );

  if ( $number_of_email_instances > 0 ) {

    return true;

  } else {

    return false;

  }

}



function wp_email_capture_admin_notice() {

  /* Check Tracking First */
  if ( !get_option( 'wpec_set_tracking' ) && current_user_can('activate_plugins') ) {
    echo '<div class="updated">';
    echo '<h4>';
    _e( 'Allow Tracking?', 'WPEC' );
    echo '</h4>';
    echo '<p>';
    _e( 'Thank you for installing WP Email Capture. Please help us improve by allowing us to gather anonymous usage stats such as themes and plugins used on your site, to help us test.','WPEC');
    echo '</p>';
    printf ( __('<p><a href="%1$s" class="button-primary">Allow Tracking</a> <a href="%2$s" class="button-secondary">Do Not Allow Tracking</a></p>', 'WPEC' ), '?wp_email_capture_tracking=1', '?wp_email_capture_tracking=2' );
    echo '</div>';
  } else {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if ( !get_user_meta( $user_id, 'wp_email_capture_setup_ignore' ) ) {
      if ( get_option( 'wp_email_capture_signup' ) == "" || get_option( 'wp_email_capture_redirection' ) == "" ) {
        echo '<div class="error"><p>';
        printf( __( '<strong>Please Note: </strong> You have not created a subscription page, confirmation page or both in WP Email Capture, please go to the WP Email Capture Settings Page to add them. | <a href="%1$s">Hide Notice</a>' ), '?wp_email_capture_setup_ignore=0' );
        echo "</p></div>";
      }
    }
  }
}

function wp_email_capture_admin_upsell() {
  global $current_user ;
  $user_id = $current_user->ID;
  $upgradeurl = "http://wpemailcapture.com";
  $discountcode = "TESTCODE";
  $discountamount = "15%";
  /* Check that the user hasn't already clicked to ignore the message */

  if ( !get_user_meta( $user_id, 'wp_email_capture_upsell_ignore' ) ) {
    if ( 500 < wp_email_capture_get_number_of_registered_users() ) {
      echo '<div class="updated welcome-panel" style="padding: 23px 10px 0;">';
      printf( __( '<a href="%1$s" class="welcome-panel-close">Hide Notice</a>
        <div class="welcome-panel-content">
        <h3>WP Email Capture - Over 500 Rows</h3>
        <p>WP Email Capture has over 500 entries. Whilst the plugin is free for use forever, it does struggle a bit with very large lists.</p>
        <p>WP Email Caputre Premium is better suited to large lists, so please consider upgrading. As a thank you for using us for so long, use discount code <strong>%3$s</strong> for <strong>%4$s</strong> off.</p>
        <p><a href="%2$s" class="button button-primary button-hero"><strong>Update WP Email Capture</strong></a></p></div>' ), '?wp_email_capture_upsell_ignore=0', $upgradeurl, $discountcode, $discountamount );
      echo "</div>";
    }
  }
}

function wp_email_capture_nag_ignore() {
  global $current_user;
  $user_id = $current_user->ID;
  /* If user clicks to ignore the notice, add that to their user meta */
  if ( isset( $_GET['wp_email_capture_setup_ignore'] ) && '0' == $_GET['wp_email_capture_setup_ignore'] ) {
    add_user_meta( $user_id, 'wp_email_capture_setup_ignore', 'true', true );
  }

  if ( isset( $_GET['wp_email_capture_upsell_ignore'] ) && '0' == $_GET['wp_email_capture_upsell_ignore'] ) {
    add_user_meta( $user_id, 'wp_email_capture_upsell_ignore', 'true', true );
  }

  if ( isset( $_GET[ 'wp_email_capture_tracking' ] ) ) {
    update_option( 'wpec_set_tracking', $_GET['wp_email_capture_tracking'] );
  }
}

function wp_email_capture_fetch_rss_feed() {

  include_once ABSPATH . WPINC . '/feed.php';
  $rss = fetch_feed( "http://wpemailcapture.com/feed?cat=-4" );

  if ( is_wp_error( $rss ) ) { return false; }

  $rss_items = $rss->get_items( 0, 5 );

  return $rss_items;

}


function wp_email_capture_get_number_of_registered_users() {
  $registered_members_table = WP_EMAIL_CAPTURE_REGISTERED_MEMBERS_TABLE;
  global $wpdb;
  $get_number_of_regs_sql = 'SELECT COUNT(*) FROM '. WP_EMAIL_CAPTURE_REGISTERED_MEMBERS_TABLE;

  $get_number_of_regs = $wpdb->get_var( $get_number_of_regs_sql );

  return $get_number_of_regs;

}

?>
