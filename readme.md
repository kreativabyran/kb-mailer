# KB Mailer

## Installation
Download zip-file and install as plugin.
## Usage

### Register email
First you have to register an email that you later can send. This is done with `kbm_register_email()`

| param              | type     | desc                                                                                                                                                                                                        |
|--------------------|----------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| $id                | `string` | Email ID. Used for sending.                                                                                                                                                                                 |
| $name              | `string` | Email name displayed in WP-admin. Also used for email subject.                                                                                                                                              |
| $content_variables | `array`  | _(Optional)_ Each array element needs a key, which is later used for replacing the variable.<br/>The array element value is a description which will be shown in the wp-admin page for building the email.  |

 Example:
 ```php
if ( function_exists( 'kbm_register_email' ) ) {
    kbm_register_email(
        'contact',
        'Contact request',
        array(
            'name'    => 'Name of person requesting contact',
            'message' => 'Message from person', 
        )
    );
}
 ```

### Editing email
Go to wp-admin > KB Mailer to see a list with all registered emails, there you can edit each part of the email, and use the registered content variables in the template.

### Send email
Registered emails are sent with `kbm_send_email()`

| param              | type     | desc                                                                                                                       |
|--------------------|----------|----------------------------------------------------------------------------------------------------------------------------|
| $id                | `string` | Email ID as registered in `kbm_register_email()`.                                                                          |
| $to                | `string` | Email receiver address.                                                                                                    |
| $content_variables | `array`  | _(Optional)_ Element key matches the one registered in `kbm_register_email()`.<br/>Value is what should be used the email. |
| $subject           | `string` | _(Optional)_ Email subject. Name as registered in `kbm_register_email()` will be used if this is not supplied.             |
Example:
```php
$to      = 'contact@example.com';
$name    = 'Oskar Modig';
$message = 'Hi! I would really like to get in touch with you.';
if ( function_exists( 'kbm_send_email' ) ) {
    kbm_send_email(
        'contact',
        $to,
        array(
            'name'    => $name,
            'message' => $message,
        )
    );
} else {
    // Failsafe if plugin is unavailable.
    wp_mail( $to, 'Contact request', "<h1>Contact request from $name</h1><p>$message</p>", array( 'Content-Type: text/html; charset=UTF-8' ) );
}
```

## Configuration
### Permissions
KB Mailer defaults to showing the email builder interface in wp-admin to user with the `manage_options` capability. The required capability can be changed with the `kb_mailer_admin_page_capability`-filter.
### Variable separators
The default variable separator is `%`. Meaning that `%name%` entered in the email content will be replaced with the value for that variable. The percentage sign before and/or after the variable id can be replaced with the following filters:
- `kb_mailer_content_variable_before`
- `kb_mailer_content_variable_after`