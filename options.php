<?php

/**
 * Class arttastic_options_page
 *
 * Configure the plugin settings page.
 */
class ar_options_page {

	/**
	 * Capability required by the user to access the My Plugin menu entry.
	 *
	 * @var string $capability
	 */
    private $slug;
    private $capability;

	/**
	 * Array of fields that should be displayed in the settings page.
	 *
	 * @var array $fields
	 */
	private $fields;

	/**
	 * The Plugin Settings constructor.
	 */
	function __construct( $slug, $page_slug, $capability, $fields ) {
		add_action( 'admin_init', [$this, 'settings_init'] );

        $this->slug = $slug;
        $this->page_slug = $page_slug;
        $this->capability = $capability;
        $this->fields = $fields;
	}

	/**
	 * Register the settings and all fields.
	 */
	function settings_init() : void {

		// Register a new setting this page.
		register_setting( $this->page_slug, 'ar_options' );

		// Register a new section.
		add_settings_section(
			$this->slug . '-settings-section',
			__( '', $this->page_slug ),
			[$this, 'render_section'],
			$this->page_slug
		);


		/* Register All The Fields. */
		foreach( $this->fields as $field ) {
			// Register a new field in the main section.
            if ( $field['type'] === 'section' ) {

                add_settings_section(
                    $field['id'], // ID
                    '',  // Title
                    [$this, 'render_field'], // Callback
                    $this->page_slug,   // Page
                    [
                        'after_section' => '</div>',
                        'label_for' => $field['id'],
                        'class' => 'ar-options-section',
                        'field' => $field
                    ]
                );

                foreach ( $field['fields'] as $sub_field ) {

                    // Register a new field in the main section.
                    add_settings_field(
                        $sub_field['id'], /* ID for the field. Only used internally. To set the HTML ID attribute, use $args['label_for']. */
                        __( $sub_field['label'], 'site-settings' ), /* Label for the field. */
                        [$this, 'render_field'], /* The name of the callback function. */
                        $this->page_slug, /* The menu page on which to display this field. */
                        $field['id'], /* The section of the settings page in which to show the box. */
                        [
                            'label_for' => $sub_field['id'], /* The ID of the field. */
                            'class' => 'ar-options-field', /* The class of the field. */
                            'field' => $sub_field, /* Custom data for the field. */
                        ]
                    );
                }

            } else {
                add_settings_field(
                    $field['id'], /* ID for the field. Only used internally. To set the HTML ID attribute, use $args['label_for']. */
                    __( $field['label'], $this->page_slug ), /* Label for the field. */
                    [$this, 'render_field'], /* The name of the callback function. */
                    $this->page_slug, /* The menu page on which to display this field. */
                    $this->slug . '-settings-section', /* The section of the settings page in which to show the box. */
                    [
                        'label_for' => $field['id'], /* The ID of the field. */
                        'class' => 'wporg_row', /* The class of the field. */
                        'field' => $field, /* Custom data for the field. */
                    ]
                );
            }
		}
	}

	/**
	 * Render the settings page.
	 */
	function render_options_page() : void {

		// check user capabilities
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', $this->page_slug ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'wporg_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				/* output security fields for the registered setting "wporg" */
				settings_fields( $this->page_slug );
				/* output setting sections and their fields */
				/* (sections are registered for "wporg", each field is registered to a specific section) */
				do_settings_sections( $this->page_slug );
				/* output save settings button */
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Args to configure the field.
	 */
	function render_field( array $args ) : void {

		$field = $args['field'];

		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'ar_options' );

		switch ( $field['type'] ) {

            case "message": {
                ?>
                <p class="description"><?php echo esc_attr( $field['description'] ); ?></p>
                <?php
                break;
            }

            case "section": {
                ?>
                <div class="ar-options-section" id="<?php echo esc_attr( $field['id'] ); ?>">
                    <h3><?php echo esc_attr( $field['label'] ); ?></h3>
                    <?php
                    if ( esc_attr( $field['description'] ) != '' && esc_attr( $field['description'] ) != null ) {
                        ?>
                        <p class="description">
                            <?php esc_html_e( $field['description'], 'site-settings' ); ?>
                        </p>
                        <?php
                    }
                break;
            }

			case "text": {
				?>
				<input
					type="text"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "checkbox": {
				?>
				<input
					type="checkbox"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="1"
					<?php echo isset( $options[ $field['id'] ] ) ? ( checked( $options[ $field['id'] ], 1, false ) ) : ( '' ); ?>
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "textarea": {
				?>
				<textarea
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
				><?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?></textarea>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "select": {
				?>
				<select
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
				>
					<?php foreach( $field['options'] as $key => $option ) { ?>
						<option value="<?php echo $key; ?>" 
							<?php echo isset( $options[ $field['id'] ] ) ? ( selected( $options[ $field['id'] ], $key, false ) ) : ( '' ); ?>
						>
							<?php echo $option; ?>
						</option>
					<?php } ?>
				</select>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "password": {
				?>
				<input
					type="password"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "wysiwyg": {
				wp_editor(
					isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : '',
					$field['id'],
					array(
						'textarea_name' => 'ar_options[' . $field['id'] . ']',
						'textarea_rows' => 5,
					)
				);
				break;
			}

			case "email": {
				?>
				<input
					type="email"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "url": {
				?>
				<input
					type="url"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "color": {
				?>
				<input
					type="color"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

			case "date": {
				?>
				<input
					type="date"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="ar_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], $this->page_slug ); ?>
				</p>
				<?php
				break;
			}

		}
	}


	/**
	 * Render a section on a page, with an ID and a text label.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     An array of parameters for the section.
	 *
	 *     @type string $id The ID of the section.
	 * }
	 */
	function render_section( array $args ) : void {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings', $this->page_slug ); ?></p>
		<?php
	}

}

foreach ( glob( __DIR__ . '/default-pages/*.php' ) as $page ) {

    include $page;
}

foreach ( glob( __DIR__ . '/pages/*.php' ) as $page ) {

    include $page;
}