<?php

global $arttastic;

$fields = [
    [
        'id' => 'theme-updates',
        'label' => 'Theme Updates',
        'description' => "This is the DANGER ZONE.  All of these settings are here to keep the theme running perfectly.  You shouldn't need to change any of these settings.  If you do accidentally change anything,  contact me -> support@amias.dev",
        'type' => 'section',
        'fields' => [
            [
                'id' => 'licence-key',
                'label' => 'Licence Key',
                'description' => "This is the key that enables the theme to get updates.",
                'type' => 'password'
            ],
            [
                'id' => 'licence-key-validity',
                'label' => 'Licence Key Validity',
                'description' => 'This is the message' . $key_validity,
                'type' => 'message'
            ]
        ]
    ]
];
$page_slug = $arttastic->get('slug') . '-danger-zone';
$capability = 'manage_options';

$danger_zone = new ar_options_page(
    $arttastic->get('slug'),
    $page_slug,
    $capability,
    $fields
);

add_action( 'admin_menu', fn() => (
	add_submenu_page(
        $arttastic->get('slug') . '-settings',
		$arttastic->get('title') . ' Danger Zone', /* Page Title */
		'Danger Zone', /* Menu Title */
		$capability, /* Capability */
		$page_slug, /* Menu Slug */
		[$danger_zone, 'render_options_page'], /* Callback */
		'99', /* Position */
	)
));