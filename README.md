# Account-Wizard

Wizard helping to create consistent user accounts for multiple services

## requirements

Any Webserver with PHP 5.4 or above installed and HTTPS activated.

Warning: Any information (passwords included!) that is not transmitted over an encrypted channel (HTTPS) is transmitted in plan text!

## What it does

In the first step you select a institute or company from a drop down list, for which you like to create the accounts.

In the second step you can copy and paste (or drag and drop) tabular info (from MS Excel, OO Calc, etc.) to a text area.

After submitting the data you get zip compressed file, containing all files for uploading your users on different services.

## installing and configuring the wizard on your webserver

1. First do a `git clone https://github.com/8bitcoderookie/Account-Wizard.git` in a public accessable directory on your webserver. Ensure the `temp` folder has sufficent rights so the script can write to it.
2. Copy `_config.php` to `config.php` and change values to fit your environment.
3. Copy `options/example.php` to `options/schoolxy.php` and rewrite the (fully working) example file to fit your needs. You may, for example, rewrite the `get_random_password()` function to comply with your password policy.

## optional configuration

### HTML template file

Copy `templates/example.html` to `options/schoolxy.html` and change the HTML code to your style, preserving the placeholders. Change the corresponding filename in your `config.php` file.

### language file

Copy `lang/de.php` to `options/xy.php` and translate the strings to your language. Change the corresponding filename in your `config.php` file.
