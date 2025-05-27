<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Database {

	public static function execute( $params, QNAP_Database $mysql = null ) {
		global $wpdb;

		// Skip database import
		if ( ! is_file( qnap_database_path( $params ) ) ) {
			return $params;
		}

		// Set query offset
		if ( isset( $params['query_offset'] ) ) {
			$query_offset = (int) $params['query_offset'];
		} else {
			$query_offset = 0;
		}

		// Set total queries size
		if ( isset( $params['total_queries_size'] ) ) {
			$total_queries_size = (int) $params['total_queries_size'];
		} else {
			$total_queries_size = 1;
		}

		// Read blogs.json file
		$handle = qnap_open( qnap_blogs_path( $params ), 'r' );

		// Parse blogs.json file
		$blogs = qnap_read( $handle, filesize( qnap_blogs_path( $params ) ) );
		$blogs = json_decode( $blogs, true );

		// Close handle
		qnap_close( $handle );

		// Read package.json file
		$handle = qnap_open( qnap_package_path( $params ), 'r' );

		// Parse package.json file
		$config = qnap_read( $handle, filesize( qnap_package_path( $params ) ) );
		$config = json_decode( $config, true );

		// Close handle
		qnap_close( $handle );

		// What percent of queries have we processed?
		$progress = (int) ( ( $query_offset / $total_queries_size ) * 100 );

		// Set progress
		QNAP_Status::info( sprintf( __( 'Restoring database...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

		$old_replace_values = $old_replace_raw_values = array();
		$new_replace_values = $new_replace_raw_values = array();

		// Get Blog URLs
		foreach ( $blogs as $blog ) {

			// Handle old and new sites dir style
			if ( defined( 'UPLOADBLOGSDIR' ) ) {

				// Get plain Files Path
				if ( ! in_array( qnap_blog_files_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = qnap_blog_files_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = qnap_blog_files_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Files Path
				if ( ! in_array( urlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( qnap_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Files Path
				if ( ! in_array( rawurlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( qnap_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Files Path
				if ( ! in_array( addcslashes( qnap_blog_files_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( qnap_blog_files_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( qnap_blog_files_url( $blog['New']['BlogID'] ), '/' );
				}

				// Get plain Sites Path
				if ( ! in_array( qnap_blog_sites_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = qnap_blog_sites_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = qnap_blog_files_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Sites Path
				if ( ! in_array( urlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( qnap_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Sites Path
				if ( ! in_array( rawurlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( qnap_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Sites Path
				if ( ! in_array( addcslashes( qnap_blog_sites_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( qnap_blog_sites_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( qnap_blog_files_url( $blog['New']['BlogID'] ), '/' );
				}
			} else {

				// Get plain Files Path
				if ( ! in_array( qnap_blog_files_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = qnap_blog_files_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = qnap_blog_uploads_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Files Path
				if ( ! in_array( urlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( qnap_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Files Path
				if ( ! in_array( rawurlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( qnap_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( qnap_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Files Path
				if ( ! in_array( addcslashes( qnap_blog_files_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( qnap_blog_files_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( qnap_blog_uploads_url( $blog['New']['BlogID'] ), '/' );
				}

				// Get plain Sites Path
				if ( ! in_array( qnap_blog_sites_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = qnap_blog_sites_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = qnap_blog_uploads_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Sites Path
				if ( ! in_array( urlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( qnap_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Sites Path
				if ( ! in_array( rawurlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( qnap_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( qnap_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Sites Path
				if ( ! in_array( addcslashes( qnap_blog_sites_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( qnap_blog_sites_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( qnap_blog_uploads_url( $blog['New']['BlogID'] ), '/' );
				}
			}

			$site_urls = array();

			// Add Site URL
			if ( ! empty( $blog['Old']['SiteURL'] ) ) {
				$site_urls[] = $blog['Old']['SiteURL'];
			}

			// Add Internal Site URL
			if ( ! empty( $blog['Old']['InternalSiteURL'] ) ) {
				if ( parse_url( $blog['Old']['InternalSiteURL'], PHP_URL_SCHEME ) && parse_url( $blog['Old']['InternalSiteURL'], PHP_URL_HOST ) ) {
					$site_urls[] = $blog['Old']['InternalSiteURL'];
				}
			}

			// Get Site URL
			foreach ( $site_urls as $site_url ) {

				// Get www URL
				if ( stripos( $site_url, '//www.' ) !== false ) {
					$site_url_www_inversion = str_ireplace( '//www.', '//', $site_url );
				} else {
					$site_url_www_inversion = str_ireplace( '//', '//www.', $site_url );
				}

				// Replace Site URL
				foreach ( array( $site_url, $site_url_www_inversion ) as $url ) {

					// Get domain
					$old_domain = parse_url( $url, PHP_URL_HOST );
					$new_domain = parse_url( $blog['New']['SiteURL'], PHP_URL_HOST );

					// Get path
					$old_path = parse_url( $url, PHP_URL_PATH );
					$new_path = parse_url( $blog['New']['SiteURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['SiteURL'], PHP_URL_SCHEME );

					// Add domain and path
					if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
						$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
						$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
					}

					// Add domain and path with single quote
					if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
					}

					// Add domain and path with double quote
					if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
					}

					// Add Site URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Site URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Handle old and new sites dir style
						if ( ! defined( 'UPLOADBLOGSDIR' ) ) {

							// Add plain Uploads URL
							if ( ! in_array( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), $old_replace_values ) ) {
								$old_replace_values[] = qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] );
								$new_replace_values[] = qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
							}

							// Add URL encoded Uploads URL
							if ( ! in_array( urlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = urlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = urlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add URL raw encoded Uploads URL
							if ( ! in_array( rawurlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = rawurlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = rawurlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add JSON escaped Uploads URL
							if ( ! in_array( addcslashes( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
								$old_replace_values[] = addcslashes( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' );
								$new_replace_values[] = addcslashes( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
							}
						}

						// Add plain Site URL
						if ( ! in_array( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
							$new_replace_values[] = qnap_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] );
						}

						// Add URL encoded Site URL
						if ( ! in_array( urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Site URL
						if ( ! in_array( rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Site URL
						if ( ! in_array( addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ), '/' );
						}
					}

					// Add email
					if ( ! isset( $config['NoEmailReplace'] ) ) {
						if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( '@%s', $old_domain );
							$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
						}
					}
				}
			}

			$home_urls = array();

			// Add Home URL
			if ( ! empty( $blog['Old']['HomeURL'] ) ) {
				$home_urls[] = $blog['Old']['HomeURL'];
			}

			// Add Internal Home URL
			if ( ! empty( $blog['Old']['InternalHomeURL'] ) ) {
				if ( parse_url( $blog['Old']['InternalHomeURL'], PHP_URL_SCHEME ) && parse_url( $blog['Old']['InternalHomeURL'], PHP_URL_HOST ) ) {
					$home_urls[] = $blog['Old']['InternalHomeURL'];
				}
			}

			// Get Home URL
			foreach ( $home_urls as $home_url ) {

				// Get www URL
				if ( stripos( $home_url, '//www.' ) !== false ) {
					$home_url_www_inversion = str_ireplace( '//www.', '//', $home_url );
				} else {
					$home_url_www_inversion = str_ireplace( '//', '//www.', $home_url );
				}

				// Replace Home URL
				foreach ( array( $home_url, $home_url_www_inversion ) as $url ) {

					// Get domain
					$old_domain = parse_url( $url, PHP_URL_HOST );
					$new_domain = parse_url( $blog['New']['HomeURL'], PHP_URL_HOST );

					// Get path
					$old_path = parse_url( $url, PHP_URL_PATH );
					$new_path = parse_url( $blog['New']['HomeURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['HomeURL'], PHP_URL_SCHEME );

					// Add domain and path
					if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
						$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
						$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
					}

					// Add domain and path with single quote
					if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
					}

					// Add domain and path with double quote
					if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
					}

					// Set Home URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Home URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Handle old and new sites dir style
						if ( ! defined( 'UPLOADBLOGSDIR' ) ) {

							// Add plain Uploads URL
							if ( ! in_array( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), $old_replace_values ) ) {
								$old_replace_values[] = qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] );
								$new_replace_values[] = qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
							}

							// Add URL encoded Uploads URL
							if ( ! in_array( urlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = urlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = urlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add URL raw encoded Uploads URL
							if ( ! in_array( rawurlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = rawurlencode( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = rawurlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add JSON escaped Uploads URL
							if ( ! in_array( addcslashes( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
								$old_replace_values[] = addcslashes( qnap_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' );
								$new_replace_values[] = addcslashes( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
							}
						}

						// Add plain Home URL
						if ( ! in_array( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
							$new_replace_values[] = qnap_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] );
						}

						// Add URL encoded Home URL
						if ( ! in_array( urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Home URL
						if ( ! in_array( rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Home URL
						if ( ! in_array( addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ), '/' );
						}
					}

					// Add email
					if ( ! isset( $config['NoEmailReplace'] ) ) {
						if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( '@%s', $old_domain );
							$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
						}
					}
				}
			}

			$uploads_urls = array();

			// Add Uploads URL
			if ( ! empty( $blog['Old']['WordPress']['UploadsURL'] ) ) {
				$uploads_urls[] = $blog['Old']['WordPress']['UploadsURL'];
			}

			// Get Uploads URL
			foreach ( $uploads_urls as $uploads_url ) {

				// Get www URL
				if ( stripos( $uploads_url, '//www.' ) !== false ) {
					$uploads_url_www_inversion = str_ireplace( '//www.', '//', $uploads_url );
				} else {
					$uploads_url_www_inversion = str_ireplace( '//', '//www.', $uploads_url );
				}

				// Replace Uploads URL
				foreach ( array( $uploads_url, $uploads_url_www_inversion ) as $url ) {

					// Get path
					$old_path = parse_url( $url, PHP_URL_PATH );
					$new_path = parse_url( $blog['New']['WordPress']['UploadsURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['WordPress']['UploadsURL'], PHP_URL_SCHEME );

					// Add path with single quote
					if ( ! in_array( sprintf( "='%s", trailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s", trailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s", trailingslashit( $new_path ) );
					}

					// Add path with double quote
					if ( ! in_array( sprintf( '="%s', trailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s', trailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s', trailingslashit( $new_path ) );
					}

					// Set Uploads URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Uploads URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Add plain Uploads URL
						if ( ! in_array( qnap_url_scheme( $url, $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = qnap_url_scheme( $url, $old_schemes[ $i ] );
							$new_replace_values[] = qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
						}

						// Add URL encoded Uploads URL
						if ( ! in_array( urlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Uploads URL
						if ( ! in_array( rawurlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Uploads URL
						if ( ! in_array( addcslashes( qnap_url_scheme( $url, $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( qnap_url_scheme( $url, $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( qnap_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
						}
					}
				}
			}
		}

		// Get plain Sites Path
		if ( ! in_array( qnap_blog_sites_url(), $old_replace_values ) ) {
			$old_replace_values[] = qnap_blog_sites_url();
			$new_replace_values[] = qnap_blog_uploads_url();
		}

		// Get URL encoded Sites Path
		if ( ! in_array( urlencode( qnap_blog_sites_url() ), $old_replace_values ) ) {
			$old_replace_values[] = urlencode( qnap_blog_sites_url() );
			$new_replace_values[] = urlencode( qnap_blog_uploads_url() );
		}

		// Get URL raw encoded Sites Path
		if ( ! in_array( rawurlencode( qnap_blog_sites_url() ), $old_replace_values ) ) {
			$old_replace_values[] = rawurlencode( qnap_blog_sites_url() );
			$new_replace_values[] = rawurlencode( qnap_blog_uploads_url() );
		}

		// Get JSON escaped Sites Path
		if ( ! in_array( addcslashes( qnap_blog_sites_url(), '/' ), $old_replace_values ) ) {
			$old_replace_values[] = addcslashes( qnap_blog_sites_url(), '/' );
			$new_replace_values[] = addcslashes( qnap_blog_uploads_url(), '/' );
		}

		$site_urls = array();

		// Add Site URL
		if ( ! empty( $config['SiteURL'] ) ) {
			$site_urls[] = $config['SiteURL'];
		}

		// Add Internal Site URL
		if ( ! empty( $config['InternalSiteURL'] ) ) {
			if ( parse_url( $config['InternalSiteURL'], PHP_URL_SCHEME ) && parse_url( $config['InternalSiteURL'], PHP_URL_HOST ) ) {
				$site_urls[] = $config['InternalSiteURL'];
			}
		}

		// Get Site URL
		foreach ( $site_urls as $site_url ) {

			// Get www URL
			if ( stripos( $site_url, '//www.' ) !== false ) {
				$site_url_www_inversion = str_ireplace( '//www.', '//', $site_url );
			} else {
				$site_url_www_inversion = str_ireplace( '//', '//www.', $site_url );
			}

			// Replace Site URL
			foreach ( array( $site_url, $site_url_www_inversion ) as $url ) {

				// Get domain
				$old_domain = parse_url( $url, PHP_URL_HOST );
				$new_domain = parse_url( site_url(), PHP_URL_HOST );

				// Get path
				$old_path = parse_url( $url, PHP_URL_PATH );
				$new_path = parse_url( site_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( site_url(), PHP_URL_SCHEME );

				// Add domain and path
				if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
					$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
					$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
				}

				// Add domain and path with single quote
				if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
				}

				// Add domain and path with double quote
				if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
				}

				// Set Site URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Site URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Site URL
					if ( ! in_array( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
						$new_replace_values[] = qnap_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] );
					}

					// Add URL encoded Site URL
					if ( ! in_array( urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Site URL
					if ( ! in_array( rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Site URL
					if ( ! in_array( addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ), '/' );
					}
				}

				// Add email
				if ( ! isset( $config['NoEmailReplace'] ) ) {
					if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '@%s', $old_domain );
						$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
					}
				}
			}
		}

		$home_urls = array();

		// Add Home URL
		if ( ! empty( $config['HomeURL'] ) ) {
			$home_urls[] = $config['HomeURL'];
		}

		// Add Internal Home URL
		if ( ! empty( $config['InternalHomeURL'] ) ) {
			if ( parse_url( $config['InternalHomeURL'], PHP_URL_SCHEME ) && parse_url( $config['InternalHomeURL'], PHP_URL_HOST ) ) {
				$home_urls[] = $config['InternalHomeURL'];
			}
		}

		// Get Home URL
		foreach ( $home_urls as $home_url ) {

			// Get www URL
			if ( stripos( $home_url, '//www.' ) !== false ) {
				$home_url_www_inversion = str_ireplace( '//www.', '//', $home_url );
			} else {
				$home_url_www_inversion = str_ireplace( '//', '//www.', $home_url );
			}

			// Replace Home URL
			foreach ( array( $home_url, $home_url_www_inversion ) as $url ) {

				// Get domain
				$old_domain = parse_url( $url, PHP_URL_HOST );
				$new_domain = parse_url( home_url(), PHP_URL_HOST );

				// Get path
				$old_path = parse_url( $url, PHP_URL_PATH );
				$new_path = parse_url( home_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( home_url(), PHP_URL_SCHEME );

				// Add domain and path
				if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
					$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
					$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
				}

				// Add domain and path with single quote
				if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
				}

				// Add domain and path with double quote
				if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
				}

				// Add Home URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Home URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Home URL
					if ( ! in_array( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
						$new_replace_values[] = qnap_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] );
					}

					// Add URL encoded Home URL
					if ( ! in_array( urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( qnap_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Home URL
					if ( ! in_array( rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( qnap_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Home URL
					if ( ! in_array( addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( qnap_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ), '/' );
					}
				}

				// Add email
				if ( ! isset( $config['NoEmailReplace'] ) ) {
					if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '@%s', $old_domain );
						$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
					}
				}
			}
		}

		$uploads_urls = array();

		// Add Uploads URL
		if ( ! empty( $config['WordPress']['UploadsURL'] ) ) {
			$uploads_urls[] = $config['WordPress']['UploadsURL'];
		}

		// Get Uploads URL
		foreach ( $uploads_urls as $uploads_url ) {

			// Get www URL
			if ( stripos( $uploads_url, '//www.' ) !== false ) {
				$uploads_url_www_inversion = str_ireplace( '//www.', '//', $uploads_url );
			} else {
				$uploads_url_www_inversion = str_ireplace( '//', '//www.', $uploads_url );
			}

			// Replace Uploads URL
			foreach ( array( $uploads_url, $uploads_url_www_inversion ) as $url ) {

				// Get path
				$old_path = parse_url( $url, PHP_URL_PATH );
				$new_path = parse_url( qnap_get_uploads_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( qnap_get_uploads_url(), PHP_URL_SCHEME );

				// Add path with single quote
				if ( ! in_array( sprintf( "='%s", trailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( "='%s", trailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( "='%s", trailingslashit( $new_path ) );
				}

				// Add path with double quote
				if ( ! in_array( sprintf( '="%s', trailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( '="%s', trailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( '="%s', trailingslashit( $new_path ) );
				}

				// Add Uploads URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Uploads URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Uploads URL
					if ( ! in_array( qnap_url_scheme( $url, $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = qnap_url_scheme( $url, $old_schemes[ $i ] );
						$new_replace_values[] = qnap_url_scheme( qnap_get_uploads_url(), $new_schemes[ $i ] );
					}

					// Add URL encoded Uploads URL
					if ( ! in_array( urlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( qnap_url_scheme( qnap_get_uploads_url(), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Uploads URL
					if ( ! in_array( rawurlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( qnap_url_scheme( $url, $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( qnap_url_scheme( qnap_get_uploads_url(), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Uploads URL
					if ( ! in_array( addcslashes( qnap_url_scheme( $url, $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( qnap_url_scheme( $url, $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( qnap_url_scheme( qnap_get_uploads_url(), $new_schemes[ $i ] ), '/' );
					}
				}
			}
		}

		// Get WordPress Content Dir
		if ( isset( $config['WordPress']['Content'] ) && ( $content_dir = $config['WordPress']['Content'] ) ) {

			// Add plain WordPress Content
			if ( ! in_array( $content_dir, $old_replace_values ) ) {
				$old_replace_values[] = $content_dir;
				$new_replace_values[] = WP_CONTENT_DIR;
			}

			// Add URL encoded WordPress Content
			if ( ! in_array( urlencode( $content_dir ), $old_replace_values ) ) {
				$old_replace_values[] = urlencode( $content_dir );
				$new_replace_values[] = urlencode( WP_CONTENT_DIR );
			}

			// Add URL raw encoded WordPress Content
			if ( ! in_array( rawurlencode( $content_dir ), $old_replace_values ) ) {
				$old_replace_values[] = rawurlencode( $content_dir );
				$new_replace_values[] = rawurlencode( WP_CONTENT_DIR );
			}

			// Add JSON escaped WordPress Content
			if ( ! in_array( addcslashes( $content_dir, '/' ), $old_replace_values ) ) {
				$old_replace_values[] = addcslashes( $content_dir, '/' );
				$new_replace_values[] = addcslashes( WP_CONTENT_DIR, '/' );
			}
		}

		// Get replace old and new values
		if ( isset( $config['Replace'] ) && ( $replace = $config['Replace'] ) ) {
			for ( $i = 0; $i < count( $replace['OldValues'] ); $i++ ) {
				if ( ! empty( $replace['OldValues'][ $i ] ) && ! empty( $replace['NewValues'][ $i ] ) ) {

					// Add plain replace values
					if ( ! in_array( $replace['OldValues'][ $i ], $old_replace_values ) ) {
						$old_replace_values[] = $replace['OldValues'][ $i ];
						$new_replace_values[] = $replace['NewValues'][ $i ];
					}

					// Add URL encoded replace values
					if ( ! in_array( urlencode( $replace['OldValues'][ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( $replace['OldValues'][ $i ] );
						$new_replace_values[] = urlencode( $replace['NewValues'][ $i ] );
					}

					// Add URL raw encoded replace values
					if ( ! in_array( rawurlencode( $replace['OldValues'][ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( $replace['OldValues'][ $i ] );
						$new_replace_values[] = rawurlencode( $replace['NewValues'][ $i ] );
					}

					// Add JSON Escaped replace values
					if ( ! in_array( addcslashes( $replace['OldValues'][ $i ], '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( $replace['OldValues'][ $i ], '/' );
						$new_replace_values[] = addcslashes( $replace['NewValues'][ $i ], '/' );
					}
				}
			}
		}

		// Get site URL
		$site_url = get_option( QNAP_SITE_URL );

		// Get home URL
		$home_url = get_option( QNAP_HOME_URL );

		// Get secret key
		$secret_key = get_option( QNAP_SECRET_KEY );

		// Get HTTP user
		$auth_user = get_option( QNAP_AUTH_USER );

		// Get HTTP password
		$auth_password = get_option( QNAP_AUTH_PASSWORD );

		// Get Uploads Path
		$uploads_path = get_option( QNAP_UPLOADS_PATH );

		// Get Uploads URL Path
		$uploads_url_path = get_option( QNAP_UPLOADS_URL_PATH );

		// Get backups labels
		$backups_labels = get_option( QNAP_BACKUPS_LABELS, array() );

		// Get sites links
		$sites_links = get_option( QNAP_SITES_LINKS, array() );

		$old_table_prefixes = array();
		$new_table_prefixes = array();

		// Set site table prefixes
		foreach ( $blogs as $blog ) {
			if ( qnap_is_mainsite( $blog['Old']['BlogID'] ) === false ) {
				$old_table_prefixes[] = qnap_qeek_prefix( $blog['Old']['BlogID'] );
				$new_table_prefixes[] = qnap_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set global table prefixes
		foreach ( $wpdb->global_tables as $table_name ) {
			$old_table_prefixes[] = qnap_qeek_prefix( 'mainsite' ) . $table_name;
			$new_table_prefixes[] = qnap_table_prefix() . $table_name;
		}

		// Set base table prefixes
		foreach ( $blogs as $blog ) {
			if ( qnap_is_mainsite( $blog['Old']['BlogID'] ) === true ) {
				$old_table_prefixes[] = qnap_qeek_prefix( 'basesite' );
				$new_table_prefixes[] = qnap_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set main table prefixes
		foreach ( $blogs as $blog ) {
			if ( qnap_is_mainsite( $blog['Old']['BlogID'] ) === true ) {
				$old_table_prefixes[] = qnap_qeek_prefix( $blog['Old']['BlogID'] );
				$new_table_prefixes[] = qnap_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set table prefixes
		$old_table_prefixes[] = qnap_qeek_prefix();
		$new_table_prefixes[] = qnap_table_prefix();

		// Get database client
		if ( is_null( $mysql ) ) {
			$mysql = QNAP_Database_Utility::create_client();
		}

		// Set database options
		$mysql->set_old_table_prefixes( $old_table_prefixes )
			->set_new_table_prefixes( $new_table_prefixes )
			->set_old_replace_values( $old_replace_values )
			->set_new_replace_values( $new_replace_values )
			->set_old_replace_raw_values( $old_replace_raw_values )
			->set_new_replace_raw_values( $new_replace_raw_values );

		// Set atomic tables (do not stop the current request for all listed tables if timeout has been exceeded)
		$mysql->set_atomic_tables( array( qnap_table_prefix() . 'options' ) );

		// Set Visual Composer
		$mysql->set_visual_composer( qnap_validate_plugin_basename( 'js_composer/js_composer.php' ) );

		// Set Oxygen Builder
		$mysql->set_oxygen_builder( qnap_validate_plugin_basename( 'oxygen/functions.php' ) );

		// Set Optimize Press
		$mysql->set_optimize_press( qnap_validate_plugin_basename( 'optimizePressPlugin/optimizepress.php' ) );

		// Set Avada Fusion Builder
		$mysql->set_avada_fusion_builder( qnap_validate_plugin_basename( 'fusion-builder/fusion-builder.php' ) );

		// Set BeTheme Responsive
		$mysql->set_betheme_responsive( qnap_validate_theme_basename( 'betheme/style.css' ) );

		// Import database
		if ( $mysql->import( qnap_database_path( $params ), $query_offset ) ) {

			// Set progress
			QNAP_Status::info( __( 'Done restoring database.', QNAP_PLUGIN_NAME ) );

			// Unset query offset
			unset( $params['query_offset'] );

			// Unset total queries size
			unset( $params['total_queries_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Get total queries size
			$total_queries_size = qnap_database_bytes( $params );

			// What percent of queries have we processed?
			$progress = (int) ( ( $query_offset / $total_queries_size ) * 100 );

			// Set progress
			QNAP_Status::info( sprintf( __( 'Restoring database...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

			// Set query offset
			$params['query_offset'] = $query_offset;

			// Set total queries size
			$params['total_queries_size'] = $total_queries_size;

			// Set completed flag
			$params['completed'] = false;
		}

		// Delete active plugins
		delete_option( QNAP_ACTIVE_PLUGINS );

		// Flush WP cache
		qnap_cache_flush();

		// Activate plugins
		qnap_activate_plugins( qnap_active_qeek_plugins() );

		// Set the new site URL
		update_option( QNAP_SITE_URL, $site_url );

		// Set the new home URL
		update_option( QNAP_HOME_URL, $home_url );

		// Set the new secret key value
		update_option( QNAP_SECRET_KEY, $secret_key );

		// Set the new HTTP user
		update_option( QNAP_AUTH_USER, $auth_user );

		// Set the new HTTP password
		update_option( QNAP_AUTH_PASSWORD, $auth_password );

		// Set the new Uploads Path
		update_option( QNAP_UPLOADS_PATH, $uploads_path );

		// Set the new Uploads URL Path
		update_option( QNAP_UPLOADS_URL_PATH, $uploads_url_path );

		// Set the new backups labels
		update_option( QNAP_BACKUPS_LABELS, $backups_labels );

		// Set the new sites links
		update_option( QNAP_SITES_LINKS, $sites_links );

		return $params;
	}
}
