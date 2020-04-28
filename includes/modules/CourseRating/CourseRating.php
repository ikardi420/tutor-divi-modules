<?php

/**
 * Tutor Course Author Module for Divi Builder
 * @since 1.0.0
 */

use TutorLMS\Divi\Helper;

class TutorCourseRating extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'tutor_course_rating';
	public $vb_support = 'on';

	// Module Credits (Appears at the bottom of the module settings modal)
	protected $module_credits = array(
		'author'     => 'Themeum',
		'author_uri' => 'https://themeum.com',
	);

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Module name & icon
		$this->name			= esc_html__('Tutor Course Rating', 'tutor-divi-modules');
		$this->icon_path	= plugin_dir_path( __FILE__ ) . 'icon.svg';

		// Toggle settings
		// Toggles are grouped into array of tab name > toggles > toggle definition
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__('Content', 'tutor-divi-modules'),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'stars' => array(
						'title'    => esc_html__('Stars', 'tutor-divi-modules'),
					),
					'count_text' => array(
						'title'    => esc_html__('Count Text', 'tutor-divi-modules'),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'count_text' => array(
					'css'          => array(
						'main' => '%%order_class%% .tutor-single-course-rating .tutor-single-rating-count',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'count_text',
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text_shadow'    => array(
				'default' => false,
			),
			'button'         => false,
		);
	}

	/**
	 * Module's specific fields
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_fields() {
		$fields = array(
			'course'       	=> Helper::get_field(
				array(
					'default'          => Helper::get_course_default(),
					'computed_affects' => array(
						'__rating',
					),
				)
			),
			'__rating'		=> array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'TutorCourseRating',
					'get_rating',
				),
				'computed_depends_on' => array(
					'course'
				),
				'computed_minimum'    => array(
					'course',
				),
			),
			'star_size' => array(
				'label'           => esc_html__( 'Size', 'tutor-divi-modules' ),
				'type'            => 'range',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'    => 'px',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'range_settings'  => array(
					'min'	=> 10,
					'max'	=> 100,
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'stars',
			),
			'star_color' => array(
				'label'           => esc_html__( 'Color', 'tutor-divi-modules' ),
				'type'            => 'color',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'stars',
			),
		);

		return $fields;
	}

	/**
	 * Get the tutor course author
	 *
	 * @return string
	 */
	public static function get_rating($args = []) {
		$course = Helper::get_course($args);
		ob_start();
		if ($course) {
			include_once dtlms_get_template('course/rating');
		}

		return ob_get_clean();
	}

	/**
	 * Render module output
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attrs       List of unprocessed attributes
	 * @param string $content     Content being processed
	 * @param string $render_slug Slug of module that is used for rendering output
	 *
	 * @return string module's rendered output
	 */
	public function render($attrs, $content = null, $render_slug) {

		// Process image size value into style
		$selector = '%%order_class%% .tutor-single-course-rating .tutor-star-rating-group';
		if ( '' !== $this->props['star_size'] ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $selector,
				'declaration' => sprintf(
					'font-size: %1$s;',
					esc_html( $this->props['star_size'] )
				),
			) );

		}

		if ( '' !== $this->props['star_color'] ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $selector,
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['star_color'] )
				),
			) );
		}

		$output = self::get_rating($this->props);

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ('' === $output) {
			return '';
		}

		return $this->_render_module_wrapper($output, $render_slug);
	}
}

new TutorCourseRating;
