<?php

global $arttastic;

$fields = [
	[
		'id' => 'contact-info',
		'label' => 'Contact information',
		'description' => "Input your contact information.  This will be used throughout the site so you'll only have to change it in one place",
		'type' => 'section',
        'fields' => [
            [
                'id' => 'phone-number',
                'label' => 'Phone Number',
                'description' => "Please type your phone number in the field as you'd like it shown on the front end e.g. 01234 567 890 or +44 1234 567 890",
                'type' => 'text'
            ],
            [
                'id' => 'email',
                'label' => 'Email Address',
                'description' => "Please input the email address you'd like shown in the footer and on the contact page",
                'type' => 'email'
            ]
        ]
	],
    [
        'id' => 'hero-section',
        'label' => 'Hero Controls',
        'description' => 'This is all of the custom content sections for the homepage hero (hero refers to the bit of the page that you can see before you scroll down the page)',
        'type' => 'section',
        'fields' => [
            [
                'id' => 'hero-background-image',
                'label' => 'Background Image',
                'description' => "Head to the media library to select an image.  you can then come back here with the 'URL Copied to Clipboard' to paste it in this field.",
                'type' => 'text'
            ]
        ]
    ],
    [
        'id' => 'footer-section',
        'label' => 'Footer Controls',
        'description' => 'This is the section holding any extra footer controls',
        'type' => 'section',
        'fields' => [
            [
                'id' => 'footer-image',
                'label' => 'Footer Image',
                'description' => "Head to the media library to select an image.  You can then come back here with the 'URL Copied to Clipboard' to paste it in this field.",
                'type' => 'text'
            ]
        ]
    ]
];
$page_slug = $arttastic->get('slug') . '-settings';
$capability = 'manage_options';

$main_page = new ar_options_page(
    $arttastic->get('slug'),
    $page_slug,
    $capability,
    $fields
);

add_action( 'admin_menu', fn() => (
	add_menu_page(
		$arttastic->get('title') . ' Main Settings', /* Page Title */
		$arttastic->get('title'), /* Menu Title */
		$capability, /* Capability */
		$page_slug, /* Menu Slug */
		[$main_page, 'render_options_page'], /* Callback */
		'dashicons-admin-site', /* Icon */
		'2', /* Position */
	)
));