<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

class RecaptchaFormPlugin extends Plugin
{
    /**
     * Get subscribed events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        // Only load admin menu in admin
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminMenu' => ['onAdminMenu', 0]
            ]);
        }
    }

    /**
     * Add plugin to admin menu
     */
    public function onAdminMenu(): void
    {
        $this->grav['twig']->plugins_hooked_nav['Recaptcha Form'] = [
            'route' => '/plugins/recaptcha-form',
            'icon' => 'fa-forumbee'
        ];
    }

    /**
     * Pass form variables to Twig for frontend pages
     */
    public function onTwigSiteVariables(): void
    {
        $page = $this->grav['page'];

        // Check if current page has recaptchaForm: true
        if ($page->header()->recaptchaForm ?? false) {

            // Get plugin settings
            $config = $this->config->get('plugins.recaptcha-form');

            // Collect only enabled fields
            $enabledFields = [];
            foreach ($config as $key => $value) {
                if (strpos($key, '_toggle') !== false && $value) {
                    $fieldName = str_replace('_toggle', '', $key);
                    $enabledFields[$fieldName] = true;
                }
            }
            $this->grav['log']->info('Enabled fields: ' . json_encode($enabledFields));

            // Get validation keys if the validation is enabled
            $validationKeys = [];

            if (!empty($enabledFields['google'])) {
                $validationKeys['google'] = [
                    'site_key' => $config['google_site_key'] ?? '',
                    'secret_key' => $config['google_secret_key'] ?? ''
                ];
            }

            if (!empty($enabledFields['cloudflare'])) {
                $validationKeys['cloudflare'] = [
                    'site_key' => $config['cloudflare_site_key'] ?? '',
                    'secret_key' => $config['cloudflare_secret_key'] ?? ''
                ];
            }

            // Log validation keys
            $this->grav['log']->info('Validation keys: ' . json_encode($validationKeys));

            // Make sure the name field is included if name_field_type is set
            if (!empty($config['name_field_type'])) {
                $enabledFields = array_merge(['name' => true, 'name_field_type' => $config['name_field_type']], $enabledFields);
            }

            // Get custom CSS from blueprint
            $formCustomCss = $config['form_custom_css'] ?? '';

            // Generate HTML dynamically
            $formHtml = '';

            // Inject custom CSS
            if (!empty($formCustomCss)) {
                $formHtml .= '<style type="text/css">' . $formCustomCss . '</style>';
            }

            // Start form tag
            $formHtml .= '<form method="post" action="" id="recaptcha-form" class="recaptcha-form">';

            foreach ($enabledFields as $field => $enabled) {
                if (!$enabled)
                    continue;

                switch ($field) {
                    case 'name':
                        if (($enabledFields['name_field_type'] ?? 'full_name') === 'full_name') {
                            $formHtml .= '<div class="form-group form-name">';
                            $formHtml .= '<label for="name" class="form-label">Name:</label>';
                            $formHtml .= '<input type="text" id="name" name="name" class="form-input">';
                            $formHtml .= '</div>';
                        } else {
                            $formHtml .= '<div class="form-group form-first-name">';
                            $formHtml .= '<label for="first_name" class="form-label">First Name:</label>';
                            $formHtml .= '<input type="text" id="first_name" name="first_name" class="form-input">';
                            $formHtml .= '</div>';

                            $formHtml .= '<div class="form-group form-last-name">';
                            $formHtml .= '<label for="last_name" class="form-label">Last Name:</label>';
                            $formHtml .= '<input type="text" id="last_name" name="last_name" class="form-input">';
                            $formHtml .= '</div>';
                        }
                        break;

                    case 'email':
                        $formHtml .= '<div class="form-group form-email">';
                        $formHtml .= '<label for="email" class="form-label">Email:</label>';
                        $formHtml .= '<input type="email" id="email" name="email" class="form-input">';
                        $formHtml .= '</div>';
                        break;

                    case 'message':
                        $formHtml .= '<div class="form-group form-message">';
                        $formHtml .= '<label for="message" class="form-label">Message:</label>';
                        $formHtml .= '<textarea id="message" name="message" class="form-input"></textarea>';
                        $formHtml .= '</div>';
                        break;

                    case 'phone':
                        $formHtml .= '<div class="form-group form-phone">';
                        $formHtml .= '<label for="phone" class="form-label">Phone:</label>';
                        $formHtml .= '<input type="text" id="phone" name="phone" class="form-input">';
                        $formHtml .= '</div>';
                        break;

                    case 'address':
                        $formHtml .= '<div class="form-group form-address">';
                        $formHtml .= '<label for="address" class="form-label">Address:</label>';
                        $formHtml .= '<input type="text" id="address" name="address" class="form-input">';
                        $formHtml .= '</div>';
                        break;

                    case 'country_code':
                        $formHtml .= '<div class="form-group form-country-code">';
                        $formHtml .= '<label for="country_code" class="form-label">Country Code:</label>';
                        $formHtml .= '<select id="country_code" name="country_code" class="form-input">';

                        $countryCodes = [
                            "+1" => "United States (+1)",
                            "+7" => "Russia (+7)",
                            "+20" => "Egypt (+20)",
                            "+27" => "South Africa (+27)",
                            "+30" => "Greece (+30)",
                            "+31" => "Netherlands (+31)",
                            "+32" => "Belgium (+32)",
                            "+33" => "France (+33)",
                            "+34" => "Spain (+34)",
                            "+36" => "Hungary (+36)",
                            "+39" => "Italy (+39)",
                            "+40" => "Romania (+40)",
                            "+41" => "Switzerland (+41)",
                            "+43" => "Austria (+43)",
                            "+44" => "United Kingdom (+44)",
                            "+45" => "Denmark (+45)",
                            "+46" => "Sweden (+46)",
                            "+47" => "Norway (+47)",
                            "+48" => "Poland (+48)",
                            "+49" => "Germany (+49)",
                            "+51" => "Peru (+51)",
                            "+52" => "Mexico (+52)",
                            "+53" => "Cuba (+53)",
                            "+54" => "Argentina (+54)",
                            "+55" => "Brazil (+55)",
                            "+56" => "Chile (+56)",
                            "+57" => "Colombia (+57)",
                            "+58" => "Venezuela (+58)",
                            "+60" => "Malaysia (+60)",
                            "+61" => "Australia (+61)",
                            "+62" => "Indonesia (+62)",
                            "+63" => "Philippines (+63)",
                            "+64" => "New Zealand (+64)",
                            "+65" => "Singapore (+65)",
                            "+66" => "Thailand (+66)",
                            "+81" => "Japan (+81)",
                            "+82" => "South Korea (+82)",
                            "+84" => "Vietnam (+84)",
                            "+86" => "China (+86)",
                            "+90" => "Turkey (+90)",
                            "+91" => "India (+91)",
                            "+92" => "Pakistan (+92)",
                            "+93" => "Afghanistan (+93)",
                            "+94" => "Sri Lanka (+94)",
                            "+98" => "Iran (+98)"
                        ];

                        foreach ($countryCodes as $code => $label) {
                            $formHtml .= '<option value="' . $code . '">' . $label . '</option>';
                        }

                        $formHtml .= '</select>';
                        $formHtml .= '</div>';
                        break;


                    case 'company':
                        $formHtml .= '<div class="form-group form-company">';
                        $formHtml .= '<label for="company" class="form-label">Company:</label>';
                        $formHtml .= '<input type="text" id="company" name="company" class="form-input">';
                        $formHtml .= '</div>';
                        break;

                    case 'terms':
                        $formHtml .= '<div class="form-group form-terms">';
                        $formHtml .= '<input type="checkbox" id="terms" name="terms" class="form-checkbox">';
                        $formHtml .= '<label for="terms" class="form-label">Accept Terms</label>';
                        $formHtml .= '</div>';
                        break;

                    case 'attachment':
                        $formHtml .= '<div class="form-group form-attachment">';
                        $formHtml .= '<label for="attachment" class="form-label">Attachment:</label>';
                        $formHtml .= '<input type="file" id="attachment" name="attachment" class="form-input">';
                        $formHtml .= '</div>';
                        break;
                }
            }

            // Submit button
            $formHtml .= '<div class="form-group form-submit">';
            $formHtml .= '<button type="submit" id="submit" class="form-button">Submit</button>';
            $formHtml .= '</div>';

            $formHtml .= '</form>';

            // Inject into current page content
            $page->content($page->content() . $formHtml);

            $this->grav['log']->info('Recaptcha form injected into page content.');
        }
    }

}
