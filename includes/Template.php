<?php

namespace KB_Mailer;

class Template {
	/**
	 * @var string
	 */
	private $html;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $args;

	public function __construct( $template, $args = array() ) {
		if ( file_exists( get_stylesheet_directory() . '/kb-mailer/templates/' . $template . '.php' ) ) {
			$this->path = get_stylesheet_directory() . '/kb-mailer/templates/' . $template . '.php';
		} else {
			$this->path = KBM_DIR . 'templates/' . $template . '.php';
		}
		$this->args = $args;
	}

	public function load() {
		if ( $this->path ) {
			$args = $this->args;
			ob_start();
			include $this->path;
			$this->html = ob_get_clean();
			return $this->html;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function get() {
		if ( empty( $this->html ) ) {
			$this->load();
		}
		return $this->html ?? '';
	}

	public function render() {
		echo wp_kses_post( self::get() );
	}
}
