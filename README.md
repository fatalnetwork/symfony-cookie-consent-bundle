# Cookie Consent bundle for Symfony

Symfony bundle to append Cookie Consent to your website to comply to AVG/GDPR for cookies.

## Installation

### Step 1: Download using composer

In a Symfony application run this command to install and integrate Cookie Consent bundle in your application:

```bash
composer require connectholland/cookie-consent-bundle
```

### Step 2: Enable the bundle

When not using symfony flex, enable the bundle in the kernel manually:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FatalNetwork\CookieConsentBundle\FNCookieConsentBundle(),
        // ...
    );
}
```

### Step 3: Enable the routing

When not using symfony flex, enable the bundles routing manually:

```yaml
# app/config/routing.yml
fn_cookie_consent:
  resource: '@FNCookieConsentBundle/Resources/config/routing.yaml'
```

### Step 4: Configure to your needs

Configure your Cookie Consent with the following possible settings

```yaml
fn_cookie_consent:
  theme: 'light' # light, dark
  categories: # Below are the default supported categories
    - 'analytics'
    - 'tracking'
    - 'marketing'
    - 'social_media'
  use_logger: true # Logs user actions to database
  position: 'top' # top, bottom
  http_only: true # Sets HttpOnly on cookies
  form_action: $routeName # When set, xhr-Requests will only be sent to this route. Take care of having the route available.
  csrf_protection: true # The cookie consent form is csrf protected or not
```

## Usage

### Twig implementation

Load the cookie consent in Twig via render_esi ( to prevent caching ) at any place you like:

```twig
{{ render_esi(path('fn_cookie_consent.show')) }}
{{ render_esi(path('fn_cookie_consent.show_if_cookie_consent_not_set')) }}
```

If you want to load the cookie consent with a specific locale you can pass the locale as a parameter:

```twig
{{ render_esi(path('fn_cookie_consent.show', { 'locale' : 'en' })) }}
{{ render_esi(path('fn_cookie_consent.show_if_cookie_consent_not_set', { 'locale' : app.request.locale })) }}
```

### Cookies

When a user submits the form the preferences are saved as cookies. The cookies have a lifetime of 1 year. The following cookies are saved:

- **Cookie_Consent**: date of submit
- **Cookie_Consent_Key**: Generated key as identifier to the submitted Cookie Consent of the user
- **Cookie*Category*[CATEGORY]**: selected value of user (_true_ or _false_)

### Logging

AVG/GDPR requires all given cookie preferences of users to be explainable by the webmasters. For this we log all cookie preferences to the database. IP addresses are anonymized. This option can be disabled in the config.

![Database logging](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/log.png)

### Themes

![Dark Theme](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/dark_theme.png)
![Light Theme](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/light_theme.png)

### TwigExtension

The following TwigExtension functions are available:

**fncookieconsent_isCategoryAllowedByUser**
check if user has given it's permission for certain cookie categories

```twig
{% if fncookieconsent_isCategoryAllowedByUser('analytics') == true %}
    ...
{% endif %}
```

**fncookieconsent_isCookieConsentSavedByUser**
check if user has saved any cookie preferences

```twig
{% if fncookieconsent_isCookieConsentSavedByUser() == true %}
    ...
{% endif %}
```

## Customization

### Categories

You can add or remove any category by changing the config and making sure there are translations available for these categories.

### Translations

All texts can be altered via Symfony translations by overwriting the FNCookieConsentBundle translation files.

### Styling

FNCookieConsentBundle comes with a default styling. A sass file is available in Resources/assets/css/cookie_consent.scss and a build css file is available in Resources/public/css/cookie_consent.css. Colors can easily be adjusted by setting the variables available in the sass file.

To install these assets run:

```bash
bin/console assets:install
```

And include the styling in your template:

```twig
{% include "@FNCookieConsent/cookie_consent_styling.html.twig" %}
```

### Javascript

By loading Resources/public/js/cookie_consent.js the cookie consent will be submitted via ajax and the cookie consent will be shown on top of your website while pushing down the rest of the website.

### Events

When a form button is clicked, the event of cookie-consent-form-submit-successful is created. Use the following code to listen to the event and add your custom functionality.

```javascript
document.addEventListener(
  'cookie-consent-form-submit-successful',
  function (e) {
    // ... your functionality
    // ... e.detail is available to see which button is clicked.
  },
  false,
)
```

### Template Themes

You can override the templates by placing templates inside your project (except for Symfony 5 projects):

```twig
# app/Resources/FNCookieConsentBundle/views/cookie_consent.html.twig
{% extends '@!FNCookieConsent/cookie_consent.html.twig' %}

{% block title %}
    Your custom title
{% endblock %}
```

#### Template override for Symfony 5 projects

You can override the templates by placing templaces inside you project as below. Be careful, it is important to place templates at this location: "app/templates/bundles/FNCookieConsentBundle/" .

```twig
# app/templates/bundles/FNCookieConsentBundle/cookie_consent.html.twig
{% extends '@!FNCookieConsent/cookie_consent.html.twig' %}

{% block intro %}
    Your custom intro
{% endblock %}
```
