<?php
/**
 * Plugin Name: Enable Application Passwords on HTTP
 * Description: Forces application passwords to be available even on HTTP environments for local development.
 */
add_filter( 'wp_is_application_passwords_available', '__return_true' ); 