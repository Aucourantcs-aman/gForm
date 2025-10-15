<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

class RecaptchaFormPlugin extends Plugin
{
    private $formMessage = '';
    private $formMessageType = '';
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ];
    }
    public function onPluginsInitialized(): void
    {
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminMenu' => ['onAdminMenu', 0]
            ]);
        }
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }
    public function onPageInitialized(): void
    {
        $request = $this->grav['request'];

        // Check if the form was submitted
        if ($this->grav['uri']->post('submit') || !empty($_POST)) {
            $this->handleFormSubmission();
        }
    }

    public function onAdminMenu(): void
    {
        $this->grav['twig']->plugins_hooked_nav['Recaptcha Form'] = [
            'route' => '/plugins/recaptcha-form',
            'icon' => 'fa-forumbee'
        ];
    }
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
            // $this->grav['log']->info('Enabled fields: ' . json_encode($enabledFields));

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
            // Display form message if set
            if (!empty($this->formMessage)) {
                $formHtml .= '<div class="form-message ' . htmlspecialchars($this->formMessageType) . '" style="margin-bottom:10px;color:'
                    . ($this->formMessageType === 'success' ? 'green' : 'red') . ';">'
                    . htmlspecialchars($this->formMessage) . '</div>';
            }

            foreach ($enabledFields as $field => $enabled) {
                if (!$enabled)
                    continue;

                switch ($field) {
                    case 'subject':
                        $formHtml .= '<div class="form-group form-subject">';
                        $formHtml .= '<label for="subject" class="form-label">Subject:</label>';
                        $formHtml .= '<input type="text" id="subject" name="subject" class="form-input">';
                        $formHtml .= '</div>';
                        break;
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
            $recaptchaSiteKey = $validationKeys['google']['site_key'] ?? '';
            $this->grav['log']->info('$recaptchaSiteKey: ' . $recaptchaSiteKey);

            if (!empty($recaptchaSiteKey)) {
                $formHtml .= '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($recaptchaSiteKey) . '"></div>';
                $formHtml .= '<p style="font-size:12px;color:#999;">reCAPTCHA enabled</p>';
                $formHtml .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            }

            // Add Cloudflare Turnstile
            if (!empty($validationKeys['cloudflare']['site_key'])) {
                $formHtml .= '<div class="cf-turnstile" data-sitekey="' . htmlspecialchars($validationKeys['cloudflare']['site_key']) . '"></div>';
                $formHtml .= '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
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
    private function handleFormSubmission(): void
    {
        $postData = $_POST;
        $config = $this->config->get('plugins.recaptcha-form');
        $secretKey = $config['google_secret_key'] ?? '';

        $this->grav['log']->info('Using Secret Key: ' . $secretKey);

        // reCAPTCHA response token
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if (empty($recaptchaResponse)) {
            $this->grav['log']->warning('⚠️ Missing g-recaptcha-response in POST data.');
            return;
        }

        // Verify reCAPTCHA
        $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $remoteIp
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($verifyUrl, false, $context);
        $verification = json_decode($result, true);

        if (!empty($verification['success'])) {
            $this->grav['log']->info('✅ Google reCAPTCHA verified successfully.');
            $this->formMessage = 'Google reCAPTCHA verified successfully.!';
            $this->formMessageType = 'success';
        } else {
            $this->grav['log']->warning('❌ Google reCAPTCHA verification failed.');
            $this->formMessage = 'Google reCAPTCHA verification failed. Please try again.';
            $this->formMessageType = 'error';
            return; // stop further processing if failed
        }


        // Continue normal form processing
        foreach ($postData as $key => $value) {
            $this->grav['log']->info("Field '{$key}': " . (is_array($value) ? json_encode($value) : $value));
        }
    }

    private function verifyRecaptcha(string $responseToken, string $secretKey, bool $cloudflare = false): bool
    {
        $verifyUrl = $cloudflare
            ? 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            : 'https://www.google.com/recaptcha/api/siteverify';

        $data = ['secret' => $secretKey, 'response' => $responseToken];
        if (!$cloudflare)
            $data['remoteip'] = $_SERVER['REMOTE_ADDR'] ?? '';

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($verifyUrl, false, $context);
        $verification = json_decode($result, true);

        return !empty($verification['success']);
    }

    private function setFormMessage(bool $success, string $provider): void
    {
        if ($success) {
            $this->formMessage = "$provider verified successfully!";
            $this->formMessageType = 'success';
        } else {
            $this->formMessage = "$provider verification failed. Please try again.";
            $this->formMessageType = 'error';
        }
    }
}
