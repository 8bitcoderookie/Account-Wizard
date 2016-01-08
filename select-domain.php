<?php
/**
* select-domain.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc reads all files in the `options` directory and offers user for selection
*/

// handle sumitted data

// check for errors

// create form

$html_title .= ' - '.$ACTION_SELECT_DOMAIN_DESC;
$html_body = '<h1>'.$html_title.'</h1>';
if (!isSecure()) {
  $html_body .= '<p class="warning">'.$WARNING_NO_HTTPS_NOTICE.'</p>';
}
$html_body .= '<form method="get" action="">'; // empty actions attribute will select the current file
$html_body .= '<label for="'.$FORM_NAME_OPTION.'">'.$FORM_LABEL_SELECT_DOMAIN.'</label>&nbsp;';
$html_body .= '<select name="'.$FORM_NAME_OPTION.'">';
$option_files = array_diff(scandir($SCRIPT_LOCAL_PATH.'/'.$DOMAIN_OPTIONS_SUBDIR), array('..', '.'));
foreach ($option_files as $file) {
  $filename_without_extension = pathinfo($file, PATHINFO_FILENAME);
  $html_body .= '<option value="'.$filename_without_extension.'">'.$filename_without_extension.'</option>';
}
$html_body .= '</select>';
$html_body .= '<input type="hidden" name="'.$FORM_NAME_ACTION.'" value="'.$ACTION_SUBMIT_DATA.'">';
$html_body .= '<p><input type="submit" value="'.$BUTTON_TEXT_NEXT.'"></p>';
$html_body .= '</form>';

echoHTML();

?>
