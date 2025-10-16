<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'E:/xamp-htdocs/gravcms/gForm/user/config/plugins/recaptcha-form.yaml',
    'modified' => 1760600396,
    'size' => 3272,
    'data' => [
        'enabled' => true,
        'text_var' => 'Custom Text added by the **Recaptcha Form** plugin (disable plugin to remove)',
        'subject_toggle' => false,
        'name_field_type' => 'full_name',
        'email_toggle' => true,
        'message_toggle' => true,
        'address_toggle' => true,
        'phone_toggle' => true,
        'country_code_toggle' => true,
        'company_toggle' => true,
        'terms_toggle' => false,
        'attachment_toggle' => false,
        'form_custom_css' => '/* ===========================
   ReCaptcha Form Styling
=========================== */

/* Form container */
#recaptcha-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 25px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Form groups */
#recaptcha-form .form-group {
    flex: 1 1 45%; /* default two per row */
    display: flex;
    flex-direction: column;
    margin-bottom: 0; /* spacing handled by gap */
}

/* Make message full width */
#recaptcha-form .form-message {
    flex: 1 1 100%;
}

/* Labels */
#recaptcha-form .form-label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    font-size: 14px;
}

/* Inputs and textarea */
#recaptcha-form .form-input {
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

/* Focus state for inputs */
#recaptcha-form .form-input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    outline: none;
}

/* Checkbox styling */
#recaptcha-form .form-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 8px;
}

/* Submit button */
#recaptcha-form .form-button {
    background-color: #4a90e2;
    color: white;
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s, transform 0.2s;
    flex: 1 1 100%;
}

/* Submit hover */
#recaptcha-form .form-button:hover {
    background-color: #357ABD;
    transform: translateY(-2px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #recaptcha-form {
        padding: 20px;
    }

    #recaptcha-form .form-input {
        font-size: 13px;
        padding: 9px 10px;
    }

    #recaptcha-form .form-button {
        width: 100%;
        font-size: 15px;
        padding: 12px;
    }

    /* Single column on small screens */
    #recaptcha-form .form-group {
        flex: 1 1 100%;
    }
}

/* Checkbox + label inline */
#recaptcha-form .form-terms {
    flex-direction: row;
    align-items: center;
}

/* Textarea height */
#recaptcha-form textarea.form-input {
    min-height: 100px;
    resize: vertical;
}
#country_code{
  padding-top: 0px !important;
  padding-bottom: 0px !important;
}
.form-message.success { color: green; }
.form-message.error { color: red; }',
        'google_toggle' => '0',
        'google_site_key' => '6LdDjugrAAAAAG8pD5Hw9ebxuKuODHHW5d3hA9-Z',
        'google_secret_key' => '6LdDjugrAAAAAEWjjveiJ1y8nMp4H9ueETxUA8fo',
        'cloudflare_toggle' => '0',
        'cloudflare_site_key' => '0x4AAAAAAB6tiMMZuWWpc7Rl',
        'cloudflare_secret_key' => '0x4AAAAAAB6tiLh3EwKOagofGmDrpZWTLZ8'
    ]
];
