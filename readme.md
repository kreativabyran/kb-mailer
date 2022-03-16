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
kbm_register_email(
    'contact',
    'Contact request'
    array(
        'name'    => 'Name of person requesting contact',
        'message' => 'Message from person', 
    )
);
 ```

### Editing email
Go to wp-admin > KB Mailer to see a list with all registered emails, there you can edit each part of the email, and use the registered content variables in the template.

### Send email
Registered emails are sent with `kbm_send_email()`

| param              | type     | desc                                                                                                                        |
|--------------------|----------|-----------------------------------------------------------------------------------------------------------------------------|
| $id                | `string` | Email ID as registered in `kbm_register_email()`.                                                                           |
| $to                | `string` | Email receiver address.                                                                                                     |
| $content_variables | `array`  | _(Optional)_ Element key matches the one registered in `kbm_register_email()`.<br/>Value is what should be used the email.  |
Example:
```php
kbm_send_email(
    'contact',
    'contact@example.com',
    array(
        'name'    => 'Oskar Modig',
        'message' => 'Hi! I would really like to get in touch with you.',
    )
);
```