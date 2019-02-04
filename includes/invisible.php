<?php
if ( ! class_exists( 'Cptch_Invisible' ) ) {
	class Cptch_Invisible {
		protected $errors;

		private $data;
		private $use_openssl;

		public function __construct() {
			global $cptch_options;

			if ( empty( $cptch_options ) ) {
				cptch_settings();
			}

			$this->data = array(
				'time_limit'	=> $cptch_options['time_limit']*1000,
				'current_time'	=> time(),
				'url'			=> get_bloginfo( 'url' ),
				'const_key'		=> substr( wp_salt(), 0, 64 ), /* this key should be 64 simbols */
				'secret_key'	=> wp_salt( 'secure_auth' )
			);

			try {

				$this->use_openssl = version_compare( PHP_VERSION, '5.3.0' ) >= 0 && function_exists( 'openssl_encrypt' );

				if ( ! $this->use_openssl && ! function_exists( 'mcrypt_encrypt' ) ) {
					throw new Exception( __( "Can't handle encrypting/decrypting data.", 'captcha-bws' ), 'cant_crypt' );
				}

			} catch( Exception $e ) {
				$this->add_error( 'cptch_init_error', __( 'ERROR', 'captcha-bws' ) . ': ' . $e->getMessage() );
			}
		}

		public function get_content() {

			$data = array(
				'time'		=> $this->data['current_time'],
				'url'		=> $this->data['url'],
				'secret'	=> $this->data['secret_key']
			);

			$data	= json_encode( $data );
			$key	= $this->_get_one_time_key();

			try {
				if ( $this->use_openssl ) {
					if ( version_compare( PHP_VERSION, '5.3.3' ) >= 0 ) {
						$code = openssl_encrypt( $data, 'aes128', $this->data['const_key'], 0, $key );
					} else {
						$code = openssl_encrypt( $data, 'aes128', $this->data['const_key'] . $key, 0 );
					}
				} else {
					$code = mcrypt_encrypt( MCRYPT_CAST_256, substr( $this->data['const_key'], 0, 32 ), $data, 'nofb', $key );
				}

				$key	= base64_encode( $key );
				$code	= base64_encode( $code );

				return "<input type=\"hidden\" name=\"cptch_code\" value=\"{$code}\"><input type=\"hidden\" name=\"cptch_key\" value=\"{$key}\">" . sprintf( __( "Protected by %s", 'captcha-bws' ), 'BestWebSoft Captcha' );

			} catch( Exception $e ) {
				$this->add_error( $e->getCode(), __( 'ERROR', 'captcha-bws' ) . ': ' . $e->getMessage() );
				return false;
			}
		}

		public function check() {

			try {

				$code	= empty( $_REQUEST['cptch_code'] ) ? '' : base64_decode( $_REQUEST['cptch_code'] );
				$key	= empty( $_REQUEST['cptch_key'] ) ? '' : base64_decode( $_REQUEST['cptch_key'] );

				if ( empty( $code ) || empty( $key ) ) {
					throw new Exception( __( 'Empty captcha.', 'captcha-bws' ) );
				}

				if ( $this->use_openssl ) {
					if ( version_compare( PHP_VERSION, '5.3.3' ) >= 0 ) {
						$answer = openssl_decrypt( $code, 'aes128', $this->data['const_key'], 0, $key );
					} else {
						$answer = openssl_decrypt( $code, 'aes128', $this->data['const_key'] . $key );
					}
				} else {
					$answer = mcrypt_decrypt( MCRYPT_CAST_256, substr( $this->data['const_key'], 0, 32 ), $code, 'nofb', $key );
				}

				$answer = json_decode( $answer, true );

				if ( absint( $answer['time'] ) + $this->data['time_limit'] < $this->data['current_time'] ) {
					throw new Exception( __( "Captcha time limit exceeded.", 'captcha-bws' ) );
				}

				if ( 0 !== strcasecmp( $answer['url'], $this->data['url'] ) ||
					0 !== strcasecmp( $answer['secret'], $this->data['secret_key'] )
				)
					throw new Exception( __( "Wrong captcha.", 'captcha-bws' ) );

			} catch( Exception $e ) {
				$this->add_error( 'cptch_check_errors', __( 'ERROR', 'captcha-bws' ) . ': ' . $e->getMessage() );
			}
		}

		protected function add_error( $code, $message, $data = '' ) {
			if ( ! is_wp_error( $this->errors ) ) {
				$this->errors = new WP_Error();
			}

			$this->errors->add( $code, $message, $data );
		}

		private function _get_one_time_key() {
			$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+\/|?';
			$chras_length = strlen( $chars );
			$rand = '';
			for ( $i = 0; $i < 16; $i ++ )
				$rand .= $chars[ rand( 0, absint( $chras_length - 1 ) ) ];
			return $rand;
		}

		public function is_errors() {
			return is_wp_error( $this->errors );
		}

		public function get_errors() {
			return $this->errors;
		}
	}
}