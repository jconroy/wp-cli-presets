<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Implements the preset command.
 */
class Preset_Command extends WP_CLI_Command {

	/**
	 * Some presets I like to use
	 *
	 * [--type=<type>]
	 * : Whether or not to apply extra presets such as cloning repos like woocommerce or subscriptions.
	 * ---
	 * default: default
	 * options:
	 *   - default
	 *   - woo
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp presets apply
	 *
	 * @when before_wp_load
	 */
	public function apply( $args, $assoc_args ) {

		// Default presets to tidy things up
		$presets = array(
			array(
				'command'    => 'rewrite structure',
				'args'       => array( '/%year%/%monthnum%/%postname%' ),
				'assoc_args' => array(),
				'success'    => 'Rewrite structure updated',
				'fail'       => 'Rewrite structure not updated',
			),
			array(
				'command'    => 'rewrite flush',
				'args'       => array(),
				'assoc_args' => array( 'hard' => true ),
				'success'    => 'Rewrite rules flushed',
				'fail'       => 'Rewrite rules not flushed',
			),
			array(
				'command'    => 'option set',
				'args'       => array( 'blog_public', 0 ),
				'assoc_args' => array(),
				'success'    => 'Search engines discouraged',
				'fail'       => 'Search engines not discouraged',
			),
			array(
				'command'    => 'plugin uninstall',
				'args'       => array( 'hello' ),
				'assoc_args' => array(),
				'success'    => 'Hello Dolly successfully removed',
				'fail'       => 'Hello Dolly could not be removed',
			),
			array(
				'command'    => 'plugin uninstall',
				'args'       => array( 'akismet' ),
				'assoc_args' => array(),
				'success'    => 'Akismet successfully removed',
				'fail'       => 'Akismet could not be removed',
			),
			array(
				'command'    => 'theme delete',
				'args'       => array( 'twentyfourteen' ),
				'assoc_args' => array(),
				'success'    => 'Twentyfourteen successfully removed',
				'fail'       => 'Twentyfourteen could not be removed',
			),
			array(
				'command'    => 'theme delete',
				'args'       => array( 'twentyfifteen' ),
				'assoc_args' => array(),
				'success'    => 'Twentyfifteen successfully removed',
				'fail'       => 'Twentyfifteen could not be removed',
			),
			array(
				'command'    => 'plugin install',
				'args'       => array( 'wordpress-importer' ),
				'assoc_args' => array(),
				'success'    => 'Wordpress Importer successfully installed',
				'fail'       => 'Wordpress Importer could not be installed',
			),
		);

		// WooCommerce specific presets
		$woo_presets = array(
			array(
				'command'    => 'theme install',
				'args'       => array( 'storefront' ),
				'assoc_args' => array( 'activate' => true ),
				'success'    => 'Storefront successfully installed',
				'fail'       => 'Storefront could not be installed',
			),
			array(
				'exec'       => true,
				'command'    => 'git clone git@github.com:woocommerce/woocommerce.git ' . ABSPATH  . 'wp-content/plugins/woocommerce --quiet' ,
				'args'       => array(),
				'assoc_args' => array(),
				'success'    => 'WooCommerce cloned successfully',
				'fail'       => 'WooCommerce could not be cloned',
			),
			array(
				'command'    => 'plugin activate',
				'args'       => array( 'woocommerce' ),
				'assoc_args' => array(),
				'success'    => 'WooCommerce activated successfully',
				'fail'       => 'WooCommerce could not be activated',
			),
			array(
				'command'    => 'option update',
				'args'       => array( 'woocommerce_force_ssl_checkout', 'yes' ),
				'assoc_args' => array(),
				'success'    => 'Force SSL checkout activated',
				'fail'       => 'Force SSL checkout could not be activated',
			),
			array(
				'exec'       => true,
				'command'    => 'git clone git@github.com:Prospress/woocommerce-subscriptions.git ' . ABSPATH . 'wp-content/plugins/woocommerce-subscriptions --quiet',
				'args'       => array(),
				'assoc_args' => array(),
				'success'    => 'Subscriptions cloned successfully',
				'fail'       => 'Subscriptions could not be cloned',
			),
			array(
				'command'    => 'plugin activate',
				'args'       => array( 'woocommerce-subscriptions' ),
				'assoc_args' => array(),
				'success'    => 'Subscriptions activated successfully',
				'fail'       => 'Subscriptions could not be activated',
			),
			array(
				'exec'       => true,
				'command'    => 'git clone git@github.com:woocommerce/woocommerce-gateway-stripe.git ' . ABSPATH . 'wp-content/plugins/woocommerce-gateway-stripe --quiet',
				'args'       => array(),
				'assoc_args' => array(),
				'success'    => 'Stripe cloned successfully',
				'fail'       => 'Stripe could not be cloned',
			),
			array(
				'command'    => 'plugin activate',
				'args'       => array( 'woocommerce-gateway-stripe' ),
				'assoc_args' => array(),
				'success'    => 'Stripe activated successfully',
				'fail'       => 'Stripe could not be activated',
			),
		);

		$type = $assoc_args['type'];

		// Optionally clone WooCommerce related repos
		if ( 'woo' == $type ) {
			$presets = array_merge( $presets, $woo_presets);
		}

		// Export a copy of the db at the end
		$presets[] = array(
			'command'    => 'db export',
			'args'       => array(),
			'assoc_args' => array( 'add-drop-table' => true ),
			'success'    => 'Database successfully exported',
			'fail'       => 'Database could not be exported',
		);

		$fail = 0;

		foreach ( $presets as $preset ) {

			if ( ! isset( $preset['exec'] ) || empty( $preset['exec'] ) ) {

				$process = WP_CLI::launch_self( $preset['command'], $preset['args'], $preset['assoc_args'], false, true, array() );

				if ( 0 == $process->return_code ) {
					WP_CLI::log( $preset['success'] );
				} else {
					$fail++;
					WP_CLI::warning( $preset['fail'] );
				}

			} else {

				exec( $preset['command'], $output, $status );

				if ( 0 == $status ) {
					WP_CLI::log( $preset['success'] );
				} else {
					$fail++;
					WP_CLI::warning( $preset['fail'] );
				}

			}

		}

		if ( 0 === $fail ) {
			WP_CLI::success( 'Presets successfully applied' );
		} else {
			WP_CLI::error( 'Presets unsuccessfully applied' );
		}
	}
}

WP_CLI::add_command( 'presets', 'Preset_Command' );