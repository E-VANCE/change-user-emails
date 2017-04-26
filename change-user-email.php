<?php

/*
Plugin Name: Change User Emails
Description: Changes the default user emails (requires Polylang plugin + registered strings), based upon https://paulund.co.uk/change-wordpress-emails
Version: 1.0
Author: E-VANCE / Henning Orth
Author URI: https://www.e-vance.net
*/

function wp_new_user_notification($user_id, $plaintext_pass = '') {

  $user = get_userdata( $user_id );
  $lang = pll_current_language();

  // The blogname option is escaped with esc_html on the way into the database in sanitize_option
  // we want to reverse this for the plain text arena of emails.
  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
  $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
  $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

  @wp_mail(get_option('admin_email'), sprintf(__('%s | New User registration'), $blogname), $message);

  if ( empty($plaintext_pass) )
    return;

  $message  = sprintf(__( pll__('Registration Email Username') . ': %s'), $user->user_login) . "\r\n";
  $message .= sprintf(__( pll__('Registration Email Password') . ': %s'), $plaintext_pass) . "\r\n";
  $message .= pll__('Registration Email Message') . home_url() . "\r\n";

  wp_mail($user->user_email, sprintf(__('%s | '. pll__('Registration Email Title')), $blogname), $message);

}