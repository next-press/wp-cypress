<?php

namespace WP_Cypress\Fixtures;

use Exception;
use Throwable;
use WP_CLI;

class FixtureCommand {

  const DEFAULT_FIXTURES_DIR = 'wp-content/plugins/wp-cypress/src/Fixtures/*';

	const USER_FIXTURES_DIR = 'fixtures/*';

  /**
	 * Find and call the relevant fixture when invoked.
	 *
	 * @param array $args
	 * @return void
	 */
	public function __invoke( array $args, array $assoc_args = [] ): void {
		$fixture_name = $args[0];
		$fixture_opts = json_decode($args[1], true);

		if ( empty( $fixture_name ) ) {
			WP_CLI::error(
				sprintf( 'You need to provide the name of a fixture.' )
			);

			return;
		}

		$this->include_dir( self::USER_FIXTURES_DIR );
		$this->include_dir( self::DEFAULT_FIXTURES_DIR );

		if ( isset( $assoc_args['update'] ) ) {
			$this->update( $fixture_name, $fixture_opts );
			return;
		}

		$this->create( $fixture_name, $fixture_opts );
	}

	/**
	 * Recursively include all files in a directory
	 *
	 * @param string $dir
	 * @return void
	 */
	public function include_dir( string $dir ): void {
		$files = glob( $dir );

		foreach ( $files as $filename ) {
			if ( is_dir( $filename ) ) {
				$this->include_dir( $filename . '/*' );
			}

			if ( is_file( $filename ) && preg_match("/\.php$/", $filename) ) {

        require_once $filename;
  
      }

		}
	}

  /**
	 * Validate whether the supplied fixture is a sub class of fixture.
	 *
	 * @param string $fixture_name
	 * @return void
	 */
	public function validate_fixture( string $fixture_name ): void {
		if ( ! strpos( get_parent_class( $fixture_name ), 'Fixture' ) ) {
			WP_CLI::error(
				sprintf( '"%s" is not a fixture.', $fixture_name )
			);
		}
	}

  /**
	 * Run an individual fixture.
	 *
	 * @param string $fixture_name
	 * @param array $fixture_opts
	 * @return void
	 */
	public function create( string $fixture_name, array $fixture_opts ): void {

		$this->validate_fixture( $fixture_name );

		$start_time = microtime( true );

		try {

			/** @var Fixture $fixture */
			$fixture = new $fixture_name($fixture_opts);
			$fixture->create();

		} catch ( Exception $e ) {

			WP_CLI::error( $e->getMessage() );

		}

		$run_time = round( microtime( true ) - $start_time, 2 );

		WP_CLI::success( 'Created ' . $fixture_name . ' in ' . $run_time . ' seconds' );

	}

  /**
	 * Run an individual fixture update.
	 *
	 * @param string $fixture_name
	 * @param array $fixture_opts
	 * @return void
	 */
	public function update( string $fixture_name, array $fixture_opts ): void {

		$this->validate_fixture( $fixture_name );

		$start_time = microtime( true );

    if(!method_exists($fixture_name, 'update')) {

      WP_CLI::error( 
				sprintf( '"%s" not implement the method update.', $fixture_name )
      );

    }

		try {

			$fixture = new $fixture_name($fixture_opts);
			$fixture->update();

		} catch ( Exception $e ) {

			WP_CLI::error( $e->getMessage() );

		}

		$run_time = round( microtime( true ) - $start_time, 2 );

		WP_CLI::success( 'Update ' . $fixture_name . ' in ' . $run_time . ' seconds' );

	}

}
