<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

class RecaptchaFormPlugin extends Plugin
{
    private $formMessage = '';
    private $formMessageType = '';
    private $formMessages = [];
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
            // $this->grav['log']->info('Validation keys: ' . json_encode($validationKeys));

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
            // Display all form messages
            foreach ($this->formMessages as $msg) {
                $formHtml .= '<div class="form-message ' . htmlspecialchars($msg['type']) . '" style="margin-bottom:10px;color:'
                    . ($msg['type'] === 'success' ? 'green' : 'red') . ';">'
                    . htmlspecialchars($msg['text']) . '</div>';
            }

            foreach ($enabledFields as $field => $enabled) {
                if (!$enabled)
                    continue;

                switch ($field) {
                    case 'subject':
                        $formHtml .= '<div class="form-group form-subject">';
                        $formHtml .= '<label for="subject" class="form-label">Subject:</label>';
                        $formHtml .= '<input type="text" id="subject" name="subject" class="form-input" required>';
                        $formHtml .= '</div>';
                        break;
                    case 'name':
                        if (($enabledFields['name_field_type'] ?? 'full_name') === 'full_name') {
                            $formHtml .= '<div class="form-group form-name">';
                            $formHtml .= '<label for="name" class="form-label">Name:</label>';
                            $formHtml .= '<input type="text" id="name" name="name" class="form-input" required>';
                            $formHtml .= '</div>';
                        } else {
                            $formHtml .= '<div class="form-group form-first-name">';
                            $formHtml .= '<label for="first_name" class="form-label">First Name:</label>';
                            $formHtml .= '<input type="text" id="first_name" name="first_name" class="form-input" required>';
                            $formHtml .= '</div>';

                            $formHtml .= '<div class="form-group form-last-name">';
                            $formHtml .= '<label for="last_name" class="form-label">Last Name:</label>';
                            $formHtml .= '<input type="text" id="last_name" name="last_name" class="form-input" required>';
                            $formHtml .= '</div>';
                        }
                        break;

                    case 'email':
                        $formHtml .= '<div class="form-group form-email">';
                        $formHtml .= '<label for="email" class="form-label">Email:</label>';
                        $formHtml .= '<input type="email" id="email" name="email" class="form-input" required>';
                        $formHtml .= '</div>';
                        break;

                    case 'message':
                        $formHtml .= '<div class="form-group form-message">';
                        $formHtml .= '<label for="message" class="form-label">Message:</label>';
                        $formHtml .= '<textarea id="message" name="message" class="form-input" required></textarea>';
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
            // $this->grav['log']->info('$recaptchaSiteKey: ' . $recaptchaSiteKey);

            if (!empty($recaptchaSiteKey)) {
                $formHtml .= '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($recaptchaSiteKey) . '"></div>';
                $formHtml .= '<p style="font-size:12px;color:#999;">reCAPTCHA enabled</p>';
                $formHtml .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                // Client-side validation
                $formHtml .= '<script>
        document.getElementById("recaptcha-form").addEventListener("submit", function(e) {
            if (typeof grecaptcha !== "undefined" && grecaptcha.getResponse() === "") {
                alert("Please complete the Google reCAPTCHA.");
                e.preventDefault();
                return false;
            }
        });
    </script>';
            }
            // Inject Cloudflare Turnstile
            if (!empty($validationKeys['cloudflare']['site_key'])) {
                $formHtml .= '<div class="cf-turnstile" data-sitekey="' . htmlspecialchars($validationKeys['cloudflare']['site_key']) . '"></div>';
                $formHtml .= '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
            }

            // Submit button
            $formHtml .= '<div class="form-group form-submit">';
            $formHtml .= '<button type="submit" id="submit" class="form-button">Submit</button>';
            $formHtml .= '</div>';

            // ✅ Placeholder for AJAX success/error message
            $formHtml .= '<div id="form-status-message" style="margin-top:10px;display:none;padding:10px;border-radius:5px;font-weight:bold;"></div>';

            $formHtml .= '</form>';

            // ✅ Add AJAX submission script
            $formHtml .= '<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("recaptcha-form");
    const submitBtn = document.getElementById("submit");
    const statusMsg = document.getElementById("form-status-message");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        // Validate reCAPTCHA client-side
        if (typeof grecaptcha !== "undefined" && grecaptcha.getResponse() === "") {
            alert("Please complete the Google reCAPTCHA.");
            return false;
        }

        // Disable button and change text
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = "Submitting...";

        // Prepare form data
        const formData = new FormData(form);

        fetch(window.location.href, {
            method: "POST",
            body: formData
        })
        .then(response => {
            // Expect HTML page response since PHP reloads page
            // So, treat any successful fetch as success for UX
            if (response.ok) {
                showMessage("Your request has been submitted successfully.", true);
                form.reset();
                if (typeof grecaptcha !== "undefined") {
                    grecaptcha.reset();
                }
            } else {
                showMessage("Error while submitting the form.", false);
            }
        })
        .catch(error => {
            console.error("AJAX Error:", error);
            showMessage("Error while submitting the form.", false);
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });

        // Helper to display message
        function showMessage(message, success) {
            statusMsg.style.display = "block";
            statusMsg.style.backgroundColor = success ? "#d4edda" : "#f8d7da";
            statusMsg.style.color = success ? "#155724" : "#721c24";
            statusMsg.style.border = success ? "1px solid #c3e6cb" : "1px solid #f5c6cb";
            statusMsg.textContent = message;

            // Hide after few seconds
            setTimeout(() => {
                statusMsg.style.display = "none";
            }, 5000);
        }
    });
});
</script>';

            $page->content($page->content() . $formHtml);

        }
    }
    private function handleFormSubmission(): void
    {
        $postData = $_POST;
        $config = $this->config->get('plugins.recaptcha-form');

        // Google reCAPTCHA
        if (!empty($config['google_secret_key'])) {
            $response = $_POST['g-recaptcha-response'] ?? '';
            if ($response) {
                $success = $this->verifyRecaptcha($response, $config['google_secret_key']);
                $this->addFormMessage($success, 'Google reCAPTCHA');
            }
        }

        // Cloudflare Turnstile
        if (!empty($config['cloudflare_secret_key'])) {
            $response = $_POST['cf-turnstile-response'] ?? '';
            if ($response) {
                $success = $this->verifyRecaptcha($response, $config['cloudflare_secret_key'], true);
                $this->addFormMessage($success, 'Cloudflare Turnstile');
            }
        }

        // 1. Get form data
        $postData = $_POST;
        // Check if a file was uploaded
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];

            // Log basic info about the uploaded file
            $this->grav['log']->info('Attachment detected: ' . $file['name']);
            $this->grav['log']->info('Attachment type: ' . $file['type']);
            $this->grav['log']->info('Attachment size: ' . $file['size'] . ' bytes');
        } else {
            $this->grav['log']->info('No attachment uploaded or upload error.');
        }
        // $this->grav['log']->info('=== RecaptchaForm Submission Received ===');
        foreach ($postData as $key => $value) {
            $displayValue = is_array($value) ? json_encode($value) : $value;
            // $this->grav['log']->info("Form Field '{$key}': {$displayValue}");
        }

        // 3. Build email subject and body
        $subject = $postData['subject'] ?? 'New Contact Form Submission';
        $body = '<h2>Contact Form Submission</h2><ul>';
        foreach ($postData as $key => $value) {
            $displayValue = is_array($value) ? json_encode($value) : $value;
            $body .= '<li><strong>' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . ':</strong> '
                . htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $body .= '</ul>';

        // $this->grav['log']->info('=== Email Body ===');
        // $this->grav['log']->info($body);

        // 4. Get email config
        $emailConfig = $this->config->get('plugins.email');
        $to = $emailConfig['to'] ?? 'aman.aucourantcs@gmail.com';
        $from = $emailConfig['from'] ?? 'aman.aucourantcs@gmail.com';


        // $this->grav['log']->info("Preparing to send email: from='{$from}' to='{$to}'");

        if (!$to || !$from) {
            $this->grav['log']->error('Email sending failed: missing "to" or "from" address.');
            return;
        }

        try {
            // 5. Send email via Grav Email plugin
            if ($this->grav->offsetExists('Email')) {
                // $this->grav['log']->info('Email plugin found. Preparing message object.');

                /** @var \Grav\Plugin\Email\Email $emailService */
                $emailService = $this->grav['Email'];

                // Create a Message object
                $message = $emailService->message($subject, $body, 'text/html');
                // $this->grav['log']->info('Message object created.');

                // Set from and to
                $message->setFrom($from);
                // $this->grav['log']->info("Message From set: {$from}");

                $message->setTo($to);
                // $this->grav['log']->info("Message To set: {$to}");

                // Log message content
                // $this->grav['log']->info("Message subject: {$subject}");
                // $this->grav['log']->info("Message body: {$body}");

                // Send email
                $sent = $emailService->send($message);

                if ($sent) {
                    // $this->grav['log']->info('RecaptchaForm email sent successfully using Email plugin.');
                } else {
                    $this->grav['log']->error('Email sending failed: Email plugin send() returned false.');
                    $this->grav['log']->error('Email plugin config: ' . json_encode($this->config->get('plugins.email')));
                }
            } else {
                $this->grav['log']->warning('Email plugin not found. Falling back to PHP mail().');

                // Fallback to native PHP mail()
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "From: {$from}\r\n";

                // $this->grav['log']->info("PHP mail headers: {$headers}");

                $sent = mail($to, $subject, $body, $headers);

                if ($sent) {
                    // $this->grav['log']->info('RecaptchaForm email sent successfully via PHP mail().');
                } else {
                    $this->grav['log']->error('Email sending failed: PHP mail() returned false.');
                }
            }
        } catch (\Exception $e) {
            $this->grav['log']->error('Email sending failed with exception: ' . $e->getMessage());
            $this->grav['log']->error('Exception trace: ' . $e->getTraceAsString());
        }

        // Log submitted fields if any success
        // if (!empty($this->formMessages)) {
        //     foreach ($postData as $key => $value) {
        //         // $this->grav['log']->info("Field '{$key}': " . (is_array($value) ? json_encode($value) : $value));
        //     }
        // }
    }
    private function verifyRecaptcha(string $response, string $secret, bool $cloudflare = false): bool
    {
        $url = $cloudflare
            ? 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            : 'https://www.google.com/recaptcha/api/siteverify';

        $data = ['secret' => $secret, 'response' => $response];
        if (!$cloudflare) {
            $data['remoteip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $verify = json_decode($result, true);

        return !empty($verify['success']);
    }
    private function addFormMessage(bool $success, string $type): void
    {
        $this->formMessages[] = [
            'type' => $success ? 'success' : 'error',
            'text' => $success
                ? "$type verified successfully!"
                : "$type verification failed. Please try again."
        ];
    }
}
