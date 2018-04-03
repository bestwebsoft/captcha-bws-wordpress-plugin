=== Captcha by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: captcha, capctha, security, antispam, captcha bws, protect forms, wordpress, secure wordpress, popular captcha, prevent spam, wordpress captcha, simple capctha
Requires at least: 3.9
Tested up to: 4.9.4
Stable tag: 5.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

#1 super security anti-spam captcha plugin for WordPress forms.

== Description ==

Captcha plugin is the best security solution that protects your WordPress website forms from spam entries. It can be used for login, registration, password recovery, comments form and much more.

It is easy to use and manage, a simple and effective plugin which will always guard your website forms.

Stop spam now!

= Free Features =

* Add captcha to:
    * Login form
    * Registration form
    * Reset password form
    * Comments form
    * Custom form
* Add custom code via plugin settings page
* Compatible with latest WordPress version
* Incredibly simple settings for fast setup without modifying code
* Detailed step-by-step documentations and videos
* Multi-lingual and RTL ready

> **Pro Features**
>
> All features from Free version included plus:
>
> * Choose Captcha type:
>   * Invisible (Default)
>   * Simple math actions such as addition, subtraction, and multiplication
>   * Character Recognition
> * Add captcha to:
>   * [Contact Form](https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=77588d399fe8cb2d33ec1be26e404896)
>   * [Subscriber](https://bestwebsoft.com/products/wordpress/plugins/subscriber/?k=0473d534b7affdaf5bf4a6c74c0600ef)
>   * Contact Form 7 (since v 3.4)
> * Compatible with BuddyPress:
>   * Registration form
>   * Comments form
>   * Create a Group form
> * Compatible with WooCommerce:
>   * Login form
>   * Register form
>   * Lost password form
>   * Checkout billing form
> * Enhance captcha protection with:
>   * Letters
>   * Numbers
>   * Images
> * Hide captcha for:
>   * Registered users in comments form
>   * Whitelisted IP addresses
> * Set captcha submission time limit
> * Refresh captcha option
> * Image packages with hand-drawn digits
> * Edit captcha title and notifications
> * Merge IP addresses from [Limit Attempts](https://bestwebsoft.com/products/wordpress/plugins/limit-attempts/?k=a9ab60b2d4016ae9c809733d84012988) plugin with Captcha whitelist
> * Configure all subsites on the network
> * Captcha submission time limit for separate forms
> * Add and enable unlimited number of image packages at the same time
> * Get answer to your support question within one business day ([Support Policy](https://bestwebsoft.com/support-policy/))
>
> [Upgrade to Pro Now](https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=2d2d85a3c277bf3489697c9a9ff2d352)

If you have a feature suggestion or idea you'd like to see in the plugin, we'd love to hear about it! [Suggest a Feature](https://support.bestwebsoft.com/hc/en-us/requests/new)

= Documentation & Videos =

* [[Doc] How to Use](https://docs.google.com/document/d/11_TUSAjMjG7hLa53lmyTZ1xox03hNlEA4tRmllFep3I/)
* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)
* [[Doc] Purchase](https://docs.google.com/document/d/1EUdBVvnm7IHZ6y0DNyldZypUQKpB8UVPToSc_LdOYQI/)
* [[Video] Purchase, Installation, Configuration Tutorial](https://www.youtube.com/watch?v=r0Noz2bYAq8)
* [[Video] Installation Instruction](https://www.youtube.com/watch?v=qsfLTcSo5Ok)

= Documentation & Videos =

* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)

= Help & Support =

Visit our Help Center if you have any questions, our friendly Support Team is happy to help - <https://support.bestwebsoft.com/>

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

Some of these translations are not complete. We are constantly adding new features which should be translated. If you would like to create your own language pack or update the existing one, you can send [the text of PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [BestWebSoft](https://support.bestwebsoft.com/hc/en-us/requests/new) and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO [files Poedit](https://www.poedit.net/download.php).

= Recommended Plugins =

* [Limit Attempts](https://bestwebsoft.com/products/wordpress/plugins/limit-attempts/?k=c6b924d096b75a288daf0e49a58f93c2) - Protect WordPress website against brute force attacks. Limit rate of login attempts.
* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=0864088de1701a5e104ffb77c6d7011c) - Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.

== Installation ==

1. Upload the `captcha-bws` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "Captcha".

[View a Step-by-step Instruction on Captcha Installation](https://docs.google.com/document/d/1DN2yYCvDyK2LqmbWw6xmUNLbb0awOVDZ_dOgIXod-Jw/).

== Frequently Asked Questions ==

= How to add Captcha plugin to the Wordpress login page (form)? =

Follow the next steps in order to add Captcha to your Wordpress login page (form):
1. Open your Wordpress admin Dashboard.
2. Navigate to Captcha settings page.
3. Find "Enable Captcha for" for the "Login form".
4. Save changes.

= Any captcha answer results an error =

Captcha will only be displayed if you are using standard registration, login, comments form pages. In case of using custom forms and pages it will be necessary to make changes in them so that captcha could be displayed and work correctly:

= Add Captcha plugin to a custom form on my Wordpress website =

Follow the instructions below in order to add Captcha plugin to your custom PHP or HTML form:
1. Install the Captcha plugin and activate it.
2. (Optional) If you would like to use your own settings for the custom forms you have (for example, for your contact and sign up forms), please follow the steps below:
- Go to the plugin settings page;
- Open Custom code tab;
- Mark "Activate" checkbox in the "Editing bws-custom-code.php" section;
- Add the following code:

`function add_my_forms( $forms ) {
    $forms['my_contact_form'] = "Form Display Name";
    return $forms;
}
add_filter( 'cptch_add_form', 'add_my_forms' );`

Please don't use the next form slugs since they are predefined by plugin settings: general, wp_login, wp_register, wp_lost_password, wp_comments, bws_contact, bws_subscriber, buddypress_register, buddypress_comments, buddypress_group, cf7_contact, woocommerce_login, woocommerce_register, woocommerce_lost_password, woocommerce_checkout.
- Save file changes;
- Go to the "Settings" tab on the plugin settings page (Admin Dashboard -> Captcha); If everything is OK, you will see your form in 'Enable Captcha for' => 'External plugins' ( with labels which you specified in the "cptch_add_form_tab" hook call function ).
- Enable it and configure form options as you need;
- Click "Save changes";

If you don`t want to use your own settings for CAPTCHA displaying in your custom form, it will use the settings from "General" block on the plugin settings.

3. Open the file with the form (where you would like to add CAPTCHA);
4. Find a place to insert the code for the CAPTCHA output;
5. If you completed the instructions in point 2, then you should add:

`<?php echo apply_filters( 'cptch_display', '', 'my_contact_form' ); ?>`

In this example, the second parameter is a slug for your custom form.

Otherwise, insert the following lines:

`<?php echo apply_filters( 'cptch_display', '' ); ?>`

6. After that, you should add the following lines to the function of the entered data checking.
If you completed the instructions in point 2, then you should add:

`<?php $error = apply_filters( 'cptch_verify', true, 'string', 'my_contact_form' );
if ( true === $error ) { /* the CAPTCHA answer is right */
    /* do necessary action */
} else { /* the CAPTCHA answer is wrong or there are some other errors */
    echo $error; /* display the error message or do other necessary actions in case when the CAPTCHA test was failed */
} ?>`

In this example, the third parameter is a slug for your custom form.

Otherwise, insert the following lines:

`<?php $error = apply_filters( 'cptch_verify', true );
if ( true === $error ) { /* the CAPTCHA answer is right */
    /* do necessary action */
} else { /* the CAPTCHA answer is wrong or there are some other errors */
    echo $error; /* display the error message or do other necessary actions in case when the CAPTCHA test was failed */
} ?>`

If there is a variable in the check function responsible for the errors output, you can concatenate variable $error to this variable. If the 'cptch_verify' filter hook returns 'true', it means that you have entered the CAPTCHA answer properly. In all other cases, the function will return the string with the error message.

= Why is the CAPTCHA missing in the comments form? =

Plugin displays captcha for those comments forms which were written in the same way as comments forms for the standard WordPress themes. Unfortunately, the plugin is incompatible with comments forms generated by using SAAS (eg: Disqus or JetPack comments forms). If you don't use SAAS comments forms, please follow the next steps:
1. Using FTP, please go to {wp_root_folder}/wp-content/themes/{your_theme}.
2. Find and open "comments.php" file. It is possible that the file that is used to display the comment form in your theme called differently or comment form output functionality is inserted directly in the other templates themes (eg "single.php" or "page.php"). In this case, you need to open the corresponding file.
3. Make sure that the file contains one of the next hooks:

`do_action ( 'comment_form_logged_in_after' )
do_action ( 'comment_form_after_fields' )
do_action ( 'comment_form' )`

If you didn't find one of these hooks, then put the string `<?php do_action( 'comment_form', $post->ID ); ?>` in the comment form.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<https://support.bestwebsoft.com>). If no, please provide the following data along with your problem's description:
- The link to the page where the problem occurs
- The name of the plugin and its version. If you are using a pro version - your order number.
- The version of your WordPress installation
- Copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/)

== Screenshots ==

1. Captcha Settings page.

== Changelog ==

= V5.0.1 - 03.04.2018 =
* Pro: Bug related to the inability to save settings after upgrading to Pro version has been fixed

= V5.0.0 - 14.09.2017 =
* NEW : Plugin has been released.

== Upgrade Notice ==

= V5.0.1 =
* Bugs fixed.

= V5.0.0 =
* Plugin release.