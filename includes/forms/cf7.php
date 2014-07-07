<?php
/**
 * Contact Form 7 support.
 *
 * @since WP Job Manager - Contact Listing 1.0.0
 *
 * @return void
 */
class Astoundify_Job_Manager_Contact_Listing_Form_CF7 extends Astoundify_Job_Manager_Contact_Listing_Form {

	/**
	 * Load the base form class.
	 *
	 * @since WP Job Manager - Contact Listing 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Hook into processing and attach our own things.
	 *
	 * @since WP Job Manager - Contact Listing 1.0.0
	 *
	 * @return void
	 */
	public function setup_actions() {
		add_filter( 'wpcf7_mail_components', array( $this, 'notification_email' ), 10, 2 );
		add_action( 'job_manager_contact_listing_form_cf7', array( $this, 'output_form' ) );
	}

	/**
	 * Output the shortcode.
	 *
	 * @since WP Job Manager - Contact Listing 1.0.0
	 *
	 * @return void
	 */
	public function output_form( $form ) {
		$args = apply_filters( 'job_manager_contact_listing_cf7_apply_form_args', '' );

		echo do_shortcode( sprintf( '[contact-form-7 id="%s" %s]', $form, $args ) );
	}

	/**
	 * Set the notification email when sending an email.
	 *
	 * @since WP Job Manager - Contact Listing 1.0.0
	 *
	 * @return string The email to notify.
	 */
	public function notification_email( $components, $cf7 ) {
		$unit = $cf7->posted_data[ '_wpcf7_unit_tag' ];

		if ( ! preg_match( '/^wpcf7-f(\d+)-p(\d+)-o(\d+)$/', $unit, $matches ) )
			return $components;

		$post_id = (int) $matches[2];
		$object = get_post( $post_id );

		if ( ! array_search( $cf7->id, $this->forms[ $object->post_type ] ) ) {
			return $components;
		}

		$recipient = $object->_application ? $object->_application : $object->_candidate_email;

		$components[ 'recipient' ] = $recipient;

		return $components;
	}

	/**
	 * Get all forms and return in a simple array for output.
	 *
	 * @since WP Job Manager - Contact Listing 1.0.0
	 *
	 * @return void
	 */
	public function get_forms() {
		$forms  = array( 0 => __( 'Please select a form', 'wp-job-manager-contact-listing' ) );

		$_forms = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'wpcf7_contact_form',
			)
		);

		if ( ! empty( $_forms ) ) {

			foreach ( $_forms as $_form ) {
				$forms[ $_form->ID ] = $_form->post_title;
			}
		}

		return $forms;
	}

}