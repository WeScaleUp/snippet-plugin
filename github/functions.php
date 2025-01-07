<?php

add_action( 'wp_dashboard_setup', function () {
	global $wp_meta_boxes;
	$wp_meta_boxes['dashboard'] = array();
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}, 1000 );





/**
 * Allow SVG uploads for administrator users.
 *
 * @param array $upload_mimes Allowed mime types.
 *
 * @return mixed
 */
add_filter(
	'upload_mimes',
	function ( $upload_mimes ) {
		// By default, only administrator users are allowed to add SVGs.
		// To enable more user types edit or comment the lines below but beware of
		// the security risks if you allow any user to upload SVG files.
		if ( ! current_user_can( 'administrator' ) ) {
			return $upload_mimes;
		}

		$upload_mimes['svg']  = 'image/svg+xml';
		$upload_mimes['svgz'] = 'image/svg+xml';

		return $upload_mimes;
	}
);

/**
 * Add SVG files mime check.
 *
 * @param array        $wp_check_filetype_and_ext Values for the extension, mime type, and corrected filename.
 * @param string       $file Full path to the file.
 * @param string       $filename The name of the file (may differ from $file due to $file being in a tmp directory).
 * @param string[]     $mimes Array of mime types keyed by their file extension regex.
 * @param string|false $real_mime The actual mime type or false if the type cannot be determined.
 */
add_filter(
	'wp_check_filetype_and_ext',
	function ( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

		if ( ! $wp_check_filetype_and_ext['type'] ) {

			$check_filetype  = wp_check_filetype( $filename, $mimes );
			$ext             = $check_filetype['ext'];
			$type            = $check_filetype['type'];
			$proper_filename = $filename;

			if ( $type && 0 === strpos( $type, 'image/' ) && 'svg' !== $ext ) {
				$ext  = false;
				$type = false;
			}

			$wp_check_filetype_and_ext = compact( 'ext', 'type', 'proper_filename' );
		}

		return $wp_check_filetype_and_ext;

	},
	10,
	5
);

add_filter(
	'admin_footer_text',
	function ( $footer_text ) {
		// Edit the line below to customize the footer text.
		$footer_text = 'Powered by <a href="https://www.wescaleup.nl/" target="_blank" rel="noopener">WeScaleUp®</a> | Hulp nodig? <a href="https://www.wescaleup.nl/contact" target="_blank" rel="noopener">Klik hier</a>';
		
		return $footer_text;
	}
);

function taxonomies_voor_paginas() {
 register_taxonomy_for_object_type( 'post_tag', 'page' );
 register_taxonomy_for_object_type( 'category', 'page' );
 }
add_action( 'init', 'taxonomies_voor_paginas' );


// Pas de themadetails en beschrijving aan voor het thema 'hello-elementor'
add_filter('wp_prepare_themes_for_js', 'customize_hello_elementor_theme');
function customize_hello_elementor_theme($prepared_themes) {
    // Controleer of het thema 'hello-elementor' aanwezig is
    if (isset($prepared_themes['hello-elementor'])) {
        // Pas de themadetails aan
        $prepared_themes['hello-elementor']['name'] = 'WeScaleUp®';
        $prepared_themes['hello-elementor']['author'] = 'WeScaleUp®';
        $prepared_themes['hello-elementor']['authorAndUri'] = '<a href="https://wescaleup.nl">WeScaleUp®</a>';
        $prepared_themes['hello-elementor']['screenshot'][0] = 'https://wescaleup.nl/wp-content/uploads/2024/12/WeScaleUp-Registratie-BOIP.jpg'; // URL naar een aangepaste screenshot

        // Verberg de beschrijving door deze leeg te maken
        $prepared_themes['hello-elementor']['description'] = ''; // Lege beschrijving

        // Verwijder de 'tags' volledig, inclusief het label
        unset($prepared_themes['hello-elementor']['tags']);
    }

    return $prepared_themes;
}

// Verander de URL van het logo op de login-pagina
add_filter('login_headerurl', function () {
    return home_url();
});

// Pas de titeltekst van het logo aan
add_filter('login_headertext', function () {
    return 'WeScaleUp';
});

// Verander 'Login' knop naar 'Inloggen'
add_filter('gettext', 'custom_login_button_text', 20, 3);
function custom_login_button_text($translated_text, $text, $domain) {
    if ($translated_text === 'Login') { // Standaard Engelse tekst
        $translated_text = 'Inloggen'; // Vervang met de gewenste tekst
    }
    return $translated_text;
}
