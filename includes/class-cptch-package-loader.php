<?php
/**
 * Load images for CAPTCHA
 *
 * @package Captcha by BestWebSoft
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Cptch_Package_Loader' ) ) {
	class Cptch_Package_Loader {
		/**
		 * Message about the errors, which have occurred during the action handling
		 *
		 * @var string
		 */
		private $error;
		/**
		 * Message about possible inaccuracies
		 *
		 * @var string
		 */
		private $notice;
		/**
		 * Message about the successfull implementation of the action
		 *
		 * @var string
		 */
		private $message;
		/**
		 * Contains 'includes/class-cptch-package-loader.php'
		 *
		 * @var string
		 */
		private $basename;
		/**
		 * Absolute path to the 'bws_captcha_images' folder in the 'uploads' folder
		 *
		 * @var string
		 */
		private $upload_dir;
		/**
		 * Absolute path to the content of the unpacked archive
		 *
		 * @var string
		 */
		private $packages_dir;
		/**
		 * Number of added(updated) packages or images
		 *
		 * @var array
		 */
		private $result;
		/**
		 * Contains the data about packages which are have to be added in to the database
		 *
		 * @var array
		 */
		private $packages;
		/**
		 * Contains the data about images which are have to be added in to the database
		 *
		 * @var array
		 */
		private $images;
		/**
		 * Contains the data about packages which are already in the database
		 *
		 * @var array
		 */
		private $saved_packages;
		/**
		 * Contains the data about images which are already in the database
		 *
		 * @var array
		 */
		private $saved_images;
		/**
		 * The action slug, what to do if the loaded package alredy exists
		 *
		 * @var string
		 */
		private $existed_action;

		/**
		 * Constructor of class
		 */
		public function __construct() {
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$this->upload_dir = $upload_dir['basedir'] . '/bws_captcha_images';
			if ( ! file_exists( $this->upload_dir ) ) {
				if ( is_writable( $upload_dir['basedir'] ) ) {
					mkdir( $this->upload_dir );
				} else {
					$this->error = esc_html__( 'Can not load images in to the "uploads" folder. Please, check your permissions.', 'captcha-bws' );
				}
			}
			$this->basename = plugin_basename( __FILE__ );
		}

		/**
		 * Display form for the package loading
		 *
		 * @since   1.6.9
		 */
		public function display() {
			$this->display_notices();
		}

		/**
		 * Shows info messages
		 *
		 * @since   1.6.9
		 */
		private function display_notices() {
			if ( $this->notice ) {
				?>
				<div class="error below-h2"><p><?php echo wp_kses_post( $this->notice ); ?></p></div>
				<?php
			}

			if ( $this->error ) {
				?>
				<div class="error below-h2"><p><?php echo wp_kses_post( $this->error ); ?></p></div>
				<?php
			}

			if ( $this->message ) {
				?>
				<div class="updated fade below-h2"><p><?php echo wp_kses_post( $this->message ); ?></p></div>
				<?php
			}
		}

		/**
		 * Handle packages data
		 *
		 * @param   string  $pakages_dir      Absolute path to folder with images.
		 * @param   boolean $remove_package   If true - original files will be removed after the end of recording to the database.
		 * @param   string  $existed_action   The action slug, what to do if the loaded package alredy exists (@since 4.2.3).
		 */
		public function save_packages( $pakages_dir = '', $remove_package = true, $existed_action = '' ) {
			global $wpdb;

			$this->saved_packages   = array();
			$this->saved_images     = array();
			$this->packages         = array();
			$this->images           = array();
			$this->result           = array( 0, 0 );
			$this->existed_action   = $existed_action;
			if ( empty( $existed_action ) ) {
				$this->existed_action = isset( $_POST['cptch_existed_package'] ) && in_array( sanitize_text_field( wp_unslash( $_POST['cptch_existed_package'] ) ), array( 'update', 'save_as_new' ) ) ? sanitize_text_field( wp_unslash( $_POST['cptch_existed_package'] ) ) : 'skip';
			}

			$this->check_tables();

			/* get info about already existed packages and images */
			$packages = $wpdb->get_results( 'SELECT `id`, `folder` FROM `' . $wpdb->base_prefix . 'cptch_packages`;' );
			if ( $packages ) {
				foreach ( $packages as $pack ) {
					$this->saved_packages[ $pack->id ] = $pack->folder;
				}
			}

			$images = $wpdb->get_results( 'SELECT `id`, `name`, `package_id` FROM `' . $wpdb->base_prefix . 'cptch_images`;' );
			if ( $images ) {
				foreach ( $images as $image ) {
					$this->saved_images[ $image->package_id ][ $image->id ] = $image->name;
				}
			}

			/**
			 * If folder with unzipped images placed not in the "uploads" folder
			 * fires during the uploading of default packages
			 */
			if ( $pakages_dir ) {
				$this->packages_dir = $pakages_dir;
			}

			if ( file_exists( "{$this->packages_dir}/packages.json" ) ) {
				$this->parse_from_json();
			} else {
				$this->parse_folders();
			}

			$this->insert_data();

			/* fires during the uploading of default packages */
			if ( $remove_package ) {
				$this->remove( $this->packages_dir );
				if ( ! $this->error ) {
					$packages_message   = sprintf( esc_html_n( 'One package has been updated or added to the database.', '%s packages have been updated or added to the database.', $this->result[0], 'captcha-bws' ), $this->result[0] );
					$images_message     = sprintf( esc_html_n( 'One image has been updated or added to the database.', '%s images have been updated or added to the database.', $this->result[1], 'captcha-bws' ), $this->result[1] );
					$this->message      = "{$packages_message}<br />{$images_message}";
				}
			}
		}

		/**
		 * Check for existing necessary database tables for the storing of packages data
		 *
		 * @since  1.6.9
		 */
		private function check_tables() {
			global $wpdb;
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			if ( is_multisite() ) {
				switch_to_blog( 1 );
			}
			if ( ! $wpdb->query( "SHOW TABLES LIKE '{$wpdb->base_prefix}cptch_images';" ) ) {
				$sql = "CREATE TABLE `{$wpdb->base_prefix}cptch_images` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` CHAR(100) NOT NULL,
					`package_id` INT NOT NULL,
					`number` INT NOT NULL,
					PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8;";
				dbDelta( $sql );
			}

			if ( ! $wpdb->query( "SHOW TABLES LIKE '{$wpdb->base_prefix}cptch_packages';" ) ) {
				$sql = "CREATE TABLE `{$wpdb->base_prefix}cptch_packages` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` CHAR(100) NOT NULL,
					`folder` CHAR(100) NOT NULL,
					`settings` LONGTEXT NOT NULL,
					`user_settings` LONGTEXT NOT NULL,
					`add_time` DATETIME NOT NULL,
					PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8;";
				dbDelta( $sql );
			}
			if ( is_multisite() ) {
				restore_current_blog();
			}
		}

		/**
		 * Forming necessary data from the packages.json file
		 *
		 * @since  1.6.9
		 * @return boolean
		 */
		private function parse_from_json() {
			$packages_data = json_decode( file_get_contents( "{$this->packages_dir}/packages.json" ), true );


			if ( empty( $packages_data ) ) {
				$this->parse_folders();
				return false;
			}

			/* parse data of each package */
			foreach ( $packages_data as $data ) {

				/* no any data about package */
				if ( ! $data['package'] ) {
					continue;
				}

				$package_path = "{$this->packages_dir}/{$data['package']}";
				/* if folder is not exists or folder is empty */
				if ( ! file_exists( $package_path ) || 2 >= count( scandir( $package_path ) ) ) {
					continue;
				}

				$images_data = empty( $data['images'] ) ? $this->get_files( $package_path ) : $data['images'];
				/* if can not get any data about images in the pacakage */
				if ( empty( $images_data ) || ! is_array( $images_data ) ) {
					continue;
				}

				if ( isset( $data['instances'] ) && is_array( $data['instances'] ) && ! empty( $data['instances'] ) ) {
					$counter            = 0;
					$new_pack_folder    = $data['package'];
					foreach ( $data['instances'] as $instance_data ) {
						$instance_data      = array_merge( array( 'package' => $data['package'] ), $instance_data );
						$add_data           = $this->add_package_data( $instance_data, $counter, $new_pack_folder );
						$new_pack_folder    = $add_data[1];
						$counter ++;
						if ( $add_data ) {
							$this->add_image_data( $images_data, $add_data );
						} else {
							continue;
						}
					}
				} else {
					$add_data = $this->add_package_data( $data );
					if ( $add_data ) {
						$this->add_image_data( $images_data, $add_data );
					} else {
						continue;
					}
				}
			}
			return true;
		}

		/**
		 * Forming necessary data by parsing of the contents of a folder
		 *
		 * @since  1.6.9
		 * @return boolean
		 */
		private function parse_folders() {
			$files      = scandir( $this->packages_dir );
			$unsorted   = array();

			if ( 2 >= count( $files ) ) {
				$this->error = esc_html__( 'Archive is empty', 'captcha-bws' );
				return false;
			}

			$count_files = count( $files );

			for ( $i = 2; $i < $count_files; $i ++ ) {
				if ( is_dir( "{$this->packages_dir}/{$files[ $i ]}" ) ) {
					/* folder is empty */
					$package_path = "{$this->packages_dir}/{$files[ $i ]}";
					if ( 2 >= count( scandir( $package_path ) ) ) {
						continue;
					}

					/* can not get any data about images in the pacakage */
					$images_data = $this->get_files( $package_path );
					if ( empty( $images_data ) || ! is_array( $images_data ) ) {
						continue;
					}

					$add_data = $this->add_package_data( array( 'package' => $files[ $i ] ) );
					if ( $add_data ) {
						$this->add_image_data( $images_data, $add_data );
					} else {
						continue;
					}
				} else {
					$unsorted[] = $files[ $i ];
				}
			}

			/* parse files which placed in the root of archive */
			if ( empty( $unsorted ) ) {
				return false;
			}

			$images_data = array();
			foreach ( $unsorted as $file ) {
				$file_info = pathinfo( "{$this->packages_dir}/{$file}" );
				/* the file name have to contain one or two digits at the end of */
				if ( preg_match( '/^(.*?)([0-9]{1,2})$/', $file_info['filename'], $matches ) ) {
					$args[] = array( $file_info['basename'], intval( $matches[2] ) );
				}
			}

			if ( empty( $images_data ) ) {
				return false;
			}

			$data       = array( 'package' => 'uncategorized' );
			$add_data   = $this->add_package_data( $data );
			if ( $add_data ) {
				$add_data[2]['package'] = '';
				$this->add_image_data( $images_data, $add_data );
			}
			return true;
		}

		/**
		 * Insert data in to data base
		 */
		private function insert_data() {
			global $wpdb, $cptch_options;
			$used_packages = $cptch_options['used_packages'];
			$need_update = false;
			$insert_data = array();
			/* insert packages data */
			if ( is_multisite() ) {
				switch_to_blog( 1 );
			}
			if ( ! empty( $this->packages ) ) {
				$time = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
				foreach ( $this->packages as $package ) {
					if ( ! in_array( $package['id'], $used_packages ) && ! $package['disabled'] ) {
						$used_packages[] = $package['id'];
					}
					$insert_data = array( $package['id'], $package['name'], $package['folder'], $package['settings'], $time );

					$wpdb->query(
						$wpdb->prepare(
							'INSERT INTO `' . $wpdb->base_prefix . 'cptch_packages`
								( `id`, `name`, `folder`, `settings`, `add_time` )
							VALUES (
								%d, %s, %s, %s, %s
							)
							ON DUPLICATE KEY UPDATE
								`id` = VALUES( `id` ),
								`name` = VALUES( `name` ),
								`folder` = VALUES( `folder` ),
								`settings` = VALUES( `settings` );',
							$insert_data[0],
							$insert_data[1],
							$insert_data[2],
							$insert_data[3],
							$insert_data[4]
						)
					);
					if ( $wpdb->last_error ) {
						$this->error .= '<br />' . $wpdb->last_error;
					} else {
						$need_update = true;
					}
				}
				$this->result[0]    += count( $this->packages );
				$this->packages     = array();
			}
			$insert_data = array();
			/* insert images data */
			if ( ! empty( $this->images ) ) {
				foreach ( $this->images as $image ) {
					$insert_data = array( $image['id'], $image['name'], $image['package_id'], $image['number'] );
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO `{$wpdb->base_prefix}cptch_images`
								( `id`, `name`, `package_id`, `number` )
							VALUES (
								%d, %s, %d, %d
							)
							ON DUPLICATE KEY UPDATE
								`id`=VALUES( `id` ),
								`name`=VALUES( `name` ),
								`package_id`=VALUES( `package_id` ),
								`number`=VALUES( `number` );",
							$insert_data[0],
							$insert_data[1],
							$insert_data[2],
							$insert_data[3]
						)
					);
					if ( $wpdb->last_error ) {
						$this->error .= '<br />' . $wpdb->last_error;
					} else {
						$need_update = true;
					}
				}
				$this->result[1]    += count( $this->images );
				$this->images       = array();
			}
			if ( is_multisite() ) {
				restore_current_blog();
			}

			if ( $need_update ) {
				$cptch_options['used_packages'] = $used_packages;
				if ( ! in_array( 'images', $cptch_options['operand_format'] ) ) {
					$cptch_options['operand_format'][] = 'images';
				}
				update_option( 'cptch_options', $cptch_options );
			}
		}

		/**
		 * Remove all files and folders by the specified path
		 *
		 * @since  1.6.9
		 * @param  string $folder    path to the folder, which we have to delete.
		 */
		private function remove( $folder ) {
			$files = scandir( $folder );
			if ( 2 > count( $files ) ) {
				/* remove empty folder */
				rmdir( $folder );
			} else {
				$count_files = count( $files );
				for ( $i = 2; $i < $count_files; $i ++ ) {
					if ( is_dir( "{$folder}/{$files[ $i ]}" ) ) { /* clear subfolder */
						$this->remove( "{$folder}/{$files[ $i ]}" );
					} else {
						unlink( "{$folder}/{$files[ $i ]}" ); /* remove file */
					}
				}
				rmdir( $folder );
			}
		}

		/**
		 * Fetc the list of files, which placed by the specified path
		 *
		 * @since 1.6.9
		 * @param  string $path   absolute path to the folder.
		 * @return array   $args   list of files.
		 */
		private function get_files( $path ) {
			$files  = scandir( $path );
			$args   = array();
			$count_files = count( $files );
			for ( $i = 2; $i < $count_files; $i++ ) {
				$file_info = pathinfo( "{$path}/{$files[ $i ]}" );
				/* the file name have to contain one or two digits at the end of */
				if ( preg_match( '/^(.*?)([0-9]{1,2})$/', $file_info['filename'], $matches ) ) {
					$args[] = array( $file_info['basename'], intval( $matches[2] ) );
				}
			}
			return $args;
		}

		/**
		 * Prepare package data before the inserting in to the database
		 *
		 * @since 1.6.9
		 * @param   array       $data                The current package data.
		 * @param   boolean/int $instance            The index number of the package instance in the "instances" field, which may received from packages.json or false.
		 *                                           It is used to handle the data of the current package instance and prevent the creation of duplicate packages if it not necessary.
		 * @param   string      $new_pack_folder     The folder name, which was created when user try to reload package witth "Save as new" enabled radiobutton in the loader form.
		 *                                           it is used in case of the handling of package instances only to prevent the creation of different folders with images for each package instance,
		 *                                           because all packages instances must use the same folder.
		 *                                           in case of the single package it is empty and it is not used.
		 * @return  array           array (
		 *                               [0] - int,    ID of current package.
		 *                               [1] - string, folder name where package images will be stored.
		 *                               [2] - array,  raw package data.
		 *                           )
		 */
		private function add_package_data( $data, $instance = false, $new_pack_folder = '' ) {
			/* if pacakage is already in the database */
			$go_to_next = false;
			if ( in_array( $data['package'], $this->saved_packages ) ) {
				switch ( $this->existed_action ) {
					case 'update': /* replace the existed package with the new one */
						$keys   = array_keys( $this->saved_packages, $data['package'] );
						$id     = false === $instance ? $keys[0] : ( isset( $keys[ $instance ] ) ? $keys[ $instance ] : max( array_keys( $this->saved_packages ) ) + 1 );
						$folder = esc_html( trim( $data['package'] ) );
						break;
					case 'save_as_new': /* save the package to the another folder */
						$id = empty( $this->saved_packages ) ? 1 : max( array_keys( $this->saved_packages ) ) + 1;
						if ( false === $instance || 0 === $instance ) {
							$new_folder = $this->get_dir( $data['package'] );
						} else {
							$keys       = array_keys( $this->saved_packages, $data['package'] );
							$new_folder = isset( $keys[ $instance ] ) ? $new_pack_folder : $this->get_dir( $data['package'] );
						}
						$folder = esc_html( trim( basename( $new_folder ) ) );
						break;
					default: /* fires also if $this->existed_action == 'skip' */
						if ( false === $instance ) {
							$go_to_next = true;
						} else {
							$keys = array_keys( $this->saved_packages, $data['package'] );
							if ( isset( $keys[ $instance ] ) ) { /* if the current instance of the package already exists */
								$go_to_next = true;
							} else {
								$id     = empty( $this->saved_packages ) ? 1 : max( array_keys( $this->saved_packages ) ) + 1;
								$folder = esc_html( trim( $data['package'] ) );
							}
						}
						break;
				}
			} else { /* add new package */
				$id     = empty( $this->saved_packages ) ? 1 : max( array_keys( $this->saved_packages ) ) + 1;
				$folder = esc_html( trim( $data['package'] ) );
			}

			if ( $go_to_next ) {
				return false;
			}

			$folder = preg_replace( '/\s+/', '_', $folder );

			if ( ! file_exists( "{$this->upload_dir}/{$folder}" ) ) {
				mkdir( "{$this->upload_dir}/{$folder}" );
			}

			/* add package to "saved" in order to detect duplicate packages */
			$this->saved_packages[ $id ] = $folder;

			/* forming of package data for recording in to a database */
			$this->packages[] = array(
				'id'        => $id,
				'folder'    => $folder,
				'name'      => ( isset( $data['name'] ) ? esc_html( trim( $data['name'] ) ) : $folder ),
				'disabled'  => ( isset( $data['disabled'] ) ? ! ! $data['disabled'] : true ),
				'settings'  => ( isset( $data['settings'] ) ? $this->check_settings( $data['settings'], $data['name'], $id ) : '' ),
			);
			return array( $id, $folder, $data );
		}

		/**
		 * Prepare package data before the inserting in to the database
		 *
		 * @since 1.6.9
		 * @param  array $images_data   The list of images in the package.
		 * @param  array $pack_data     Data of the package where the images are located.
		 */
		private function add_image_data( $images_data, $pack_data ) {
			$id     = $pack_data[0];
			$folder = $pack_data[1];
			$data   = $pack_data[2];
			$i      = 0;
			foreach ( $images_data as $image_data ) {
				/* switch to the next iteration if the data of the current image are wrong */
				if ( ! is_array( $image_data ) || 2 > count( $image_data ) ) {
					continue;
				}

				/* prepare image data */
				$name       = sanitize_file_name( $image_data[0] ); /* file name */
				$number     = abs( intval( $image_data[1] ) ); /* number, which is associated with the image */
				$image_path = empty( $data['package'] ) ? "{$this->packages_dir}/{$image_data[0]}" : "{$this->packages_dir}/{$data['package']}/{$image_data[0]}"; /* path to the image in the uzipped folder */
				$dest_path  = "{$this->upload_dir}/{$folder}/{$name}"; /* destination path */

				if (
					! file_exists( $image_path ) ||
					! $this->is_allowed( $image_path ) ||
					empty( $name ) ||
					! copy( $image_path, $dest_path )
				) {
					continue;
				}

				if ( isset( $this->saved_images[ $id ] ) ) {
					$keys     = array_keys( $this->saved_images[ $id ], $name );
					$image_id = in_array( $name, $this->saved_images[ $id ] ) ? $keys[0] : $this->get_image_next_id();
				} else {
					$image_id = empty( $this->saved_images ) ? 1 : $this->get_image_next_id();
				}

				/* add image to "saved" in order to detect duplicate images */
				$this->saved_images[ $id ][ $image_id ] = $name;

				/* add image options for recording in to a database */
				$this->images[] = array(
					'id'            => $image_id,
					'package_id'    => $id,
					'name'          => $name,
					'number'        => $number,
				);
				$i++;

				/* add data in to the database if there are collected 500 images */
				if ( 500 === $i ) {
					$this->insert_data();
					$i = 0;
				}
			}
		}

		/**
		 * Fetch the unioue folder name for the package
		 *
		 * @uses   to prevent the replacement of the images and to create new packages.
		 * @since  1.6.9
		 * @param  string $dir       Folder name.
		 * @return string $new_dir   New folder name.
		 */
		private function get_dir( $dir ) {
			$new_dir = "{$this->upload_dir}/{$dir}";
			$i = 0;
			while ( file_exists( $new_dir ) ) {
				$i ++;
				$new_dir = "{$this->upload_dir}/{$dir}_{$i}";
			}
			return $new_dir;
		}

		/**
		 * Prepare package settings
		 *
		 * @uses     if there is a package.json. file in the packages archive
		 * @since    1.6.9
		 * @param    array   $settings        Package setings which were soecified in the package.json file.
		 * @param    string  $package_name    The name of the package.
		 * @param    string  $package_id      The ID of the package.
		 * @param    boolean $serialize       If true - settings will be returned as a serialized string.
		 * @return   array/string             Prepared package settings.
		 */
		public function check_settings( $settings, $package_name = '', $package_id = '', $serialize = true ) {
			$color      = '(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])';
			$space      = '\s{0,1}';
			$rgb        = "rgb\({$space}{$color},{$space}{$color},{$space}{$color}{$space}\)";
			$hex        = '#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})';
			$reg_exp    = "/^({$rgb})|($hex)$/"; /* color value must be only in the RGB or HEX format */
			$wrong      = array();
			foreach ( $settings as $key => $value ) {
				$value = trim( $value );
				if ( ! empty( $value ) && ! preg_match( $reg_exp, $value ) ) {
					$wrong[]            = $value;
					$settings[ $key ]   = '';
				} else {
					$settings[ $key ]   = $value;
				}
			}
			if ( ! empty( $wrong ) ) {
				$message =
					sprintf(
						esc_html__( 'Some settings of the package %s were set incorrectly. They have been skipped.', 'captcha-bws' ),
						"<a href=\"admin.php?page=captcha-packages.php&cptch_action=edit&id={$package_id}\">\"{$package_name}\"</a>"
					) . '<br />' .
					esc_html__( 'Wrong data', 'captcha-bws' ) . ':&nbsp;' . implode( ',&nbsp;', $wrong );
				$this->notice = ( $this->notice ? $this->notice . '<br/>' : '' ) . $message;
			}

			return $serialize ? serialize( $settings ) : $settings;
		}

		/**
		 * Check file extension
		 *
		 * @param   string $file_name  path to file.
		 * @return  boolean            true if file format is allowed to use for CAPTCHA.
		 */
		private function is_allowed( $file_name ) {
			$allowed_formats = array(
				'gif'           => 'image/gif',
				'png'           => 'image/png',
				'jpg|jpeg|jpe'  => 'image/jpeg',
				'svg'           => 'font/svg',
			);
			$data = wp_check_filetype( $file_name, $allowed_formats );
			return ! ! $data['ext'];
		}

		/**
		 * Fetch an unique image ID
		 *
		 * @since 1.6.9
		 * @return int     an image ID
		 */
		private function get_image_next_id() {
			$next_id = 0;
			foreach ( $this->saved_images as $item ) {
				$max = max( array_keys( $item ) );
				if ( ! $next_id ) {
					$next_id = $max;
				} else {
					$next_id = $max < $next_id ? $next_id : $max;
				}
			}
			$next_id ++;
			return $next_id;
		}
	}
}
