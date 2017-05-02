<?php

/*
Plugin Name: Change User Notification (Email)
Description: Changes the default user emails, based upon https://oikos.digital/2016/11/modifying-new-user-notification-emails-gravity-forms-user-registration-add
Version: 1.0
Author: E-VANCE / Henning Orth
Author URI: https://www.e-vance.net
*/

if ( ! function_exists( 'gf_new_user_notification' ) ) {

			/**
			 * Overrides wp_new_user_notification to allow sending passwords in plain text
			 *
			 * Forked from WordPress 4.4.1
			 *
			 * @see wp_new_user_notification()
			 * @see GF_User_Registration->log_wp_mail()
			 *
			 * @param int    $user_id        The ID of the user that the notification is being sent to.
			 * @param string $plaintext_pass The password being sent to the user.
			 * @param string $notify         Whether the admin should be notified.
			 *                               If 'admin', only the admin. If 'both', user and admin.
			 */
			function gf_new_user_notification( $user_id, $plaintext_pass = '', $notify = '' ) {
				$user = get_userdata( $user_id );

				// The blogname option is escaped with esc_html on the way into the database in sanitize_option
				// we want to reverse this for the plain text arena of emails.
				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

				$message = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
				$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
				$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

				$result = @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
				gf_user_registration()->log_wp_mail( $result, 'admin' );

				if ( 'admin' === $notify || ( empty( $plaintext_pass ) && empty( $notify ) ) ) {
					return;
				}

				$message = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";

				if ( empty( $plaintext_pass ) ) {
					$message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
					$message .= '<' . $this->get_set_password_url( $user ) . ">\r\n\r\n";
				} else {
					$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n\r\n";
				}
      
        // Replacing wp_login_url with a custom url here
				// $message .= wp_login_url() . "\r\n";
        $message .= home_url() . "\r\n";

				$result = wp_mail( $user->user_email, sprintf( __( '[%s] Your username and password info' ), $blogname ), $message );
				gf_user_registration()->log_wp_mail( $result, 'user' );
			}

		}
