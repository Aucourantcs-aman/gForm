<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'E:/xamp-htdocs/gravcms/gForm/user/plugins/recaptcha-form/blueprints.yaml',
    'modified' => 1760595143,
    'size' => 7367,
    'data' => [
        'name' => 'Recaptcha Form',
        'slug' => 'recaptcha-form',
        'type' => 'plugin',
        'version' => '0.1.0',
        'description' => 'Custom configurable form plugin with reCAPTCHA support.',
        'icon' => 'forumbee',
        'author' => [
            'name' => 'Developer',
            'email' => 'hello@aucourantcyberspace.com'
        ],
        'homepage' => 'https://github.com/developer/grav-plugin-recaptcha-form',
        'demo' => 'http://demo.yoursite.com',
        'keywords' => 'grav, plugin, recaptcha, form',
        'bugs' => 'https://github.com/developer/grav-plugin-recaptcha-form/issues',
        'docs' => 'https://github.com/developer/grav-plugin-recaptcha-form/blob/develop/README.md',
        'license' => 'MIT',
        'dependencies' => [
            0 => [
                'name' => 'grav',
                'version' => '>=1.6.0'
            ]
        ],
        'form' => [
            'validation' => 'loose',
            'fields' => [
                'tabs' => [
                    'type' => 'tabs',
                    'active' => 1,
                    'fields' => [
                        'settings' => [
                            'type' => 'tab',
                            'title' => 'Form Settings',
                            'fields' => [
                                'enabled' => [
                                    'type' => 'toggle',
                                    'label' => 'PLUGIN_ADMIN.PLUGIN_STATUS',
                                    'highlight' => 1,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'PLUGIN_ADMIN.ENABLED',
                                        0 => 'PLUGIN_ADMIN.DISABLED'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'subject_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Subject Field',
                                    'help' => 'Choose whether to include a Subject field in the form. REQUIRED',
                                    'highlight' => 0,
                                    'default' => 1,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool',
                                        'required' => true
                                    ]
                                ],
                                'name_field_type' => [
                                    'type' => 'select',
                                    'label' => 'Name Field Type',
                                    'validate' => [
                                        'required' => true
                                    ],
                                    'help' => 'Choose whether to show a single Full Name field or separate First and Last Name fields. REQUIRED',
                                    'options' => [
                                        'full_name' => 'Full Name',
                                        'fname_lname' => 'First Name & Last Name'
                                    ],
                                    'default' => 'full_name'
                                ],
                                'email_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Email Field',
                                    'help' => 'Choose whether to include an Email field in the form. REQUIRED',
                                    'highlight' => 0,
                                    'default' => 1,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool',
                                        'required' => true
                                    ]
                                ],
                                'message_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Message Field',
                                    'help' => 'Choose whether to include a Message textarea field in the form. REQUIRED',
                                    'highlight' => 0,
                                    'default' => 1,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool',
                                        'required' => true
                                    ]
                                ],
                                'address_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Address Field',
                                    'help' => 'Choose whether to include an Address field in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'phone_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Phone Number Field',
                                    'help' => 'Choose whether to include a Phone Number field in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'country_code_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Country Code Field',
                                    'help' => 'Choose whether to include a Country Code field in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'company_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Company Name Field',
                                    'help' => 'Choose whether to include a Company Name field in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'terms_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Terms & Conditions Checkbox',
                                    'help' => 'Choose whether to include a Terms & Conditions checkbox in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'attachment_toggle' => [
                                    'type' => 'toggle',
                                    'label' => 'Include Attachment Field',
                                    'help' => 'Choose whether to include a file Attachment field in the form.',
                                    'highlight' => 0,
                                    'default' => 0,
                                    'options' => [
                                        1 => 'Enabled',
                                        0 => 'Disabled'
                                    ],
                                    'validate' => [
                                        'type' => 'bool'
                                    ]
                                ],
                                'form_custom_css' => [
                                    'type' => 'textarea',
                                    'label' => 'Custom CSS',
                                    'help' => 'Add custom CSS styles for the form.',
                                    'default' => '',
                                    'rows' => 10
                                ]
                            ]
                        ],
                        'validation' => [
                            'type' => 'tab',
                            'title' => 'Validation Settings',
                            'fields' => [
                                'google_section' => [
                                    'type' => 'section',
                                    'title' => 'Google reCAPTCHA Keys',
                                    'underline' => true,
                                    'fields' => [
                                        'google_toggle' => [
                                            'type' => 'toggle',
                                            'label' => 'Enable Google reCAPTCHA',
                                            'highlight' => 1,
                                            'default' => 0,
                                            'options' => [
                                                1 => 'Enabled',
                                                0 => 'Disabled'
                                            ],
                                            'help' => 'Enable this to use Google reCAPTCHA validation.'
                                        ],
                                        'google_site_key' => [
                                            'type' => 'text',
                                            'label' => 'Google reCAPTCHA Site Key',
                                            'placeholder' => 'Enter your site key',
                                            'validate' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'google_secret_key' => [
                                            'type' => 'text',
                                            'label' => 'Google reCAPTCHA Secret Key',
                                            'placeholder' => 'Enter your secret key',
                                            'validate' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    '@display-if' => [
                                        'google_toggle' => 1
                                    ]
                                ],
                                'cloudflare_section' => [
                                    'type' => 'section',
                                    'title' => 'Cloudflare Turnstile Keys',
                                    'underline' => true,
                                    'fields' => [
                                        'cloudflare_toggle' => [
                                            'type' => 'toggle',
                                            'label' => 'Enable Cloudflare Turnstile',
                                            'highlight' => 1,
                                            'default' => 0,
                                            'options' => [
                                                1 => 'Enabled',
                                                0 => 'Disabled'
                                            ],
                                            'help' => 'Enable this to use Cloudflare Turnstile validation.'
                                        ],
                                        'cloudflare_site_key' => [
                                            'type' => 'text',
                                            'label' => 'Cloudflare Site Key',
                                            'placeholder' => 'Enter your site key',
                                            'validate' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'cloudflare_secret_key' => [
                                            'type' => 'text',
                                            'label' => 'Cloudflare Secret Key',
                                            'placeholder' => 'Enter your secret key',
                                            'validate' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    '@display-if' => [
                                        'cloudflare_toggle' => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
