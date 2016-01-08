<?php
/**
* submit-data.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc reads option file and displays data form
*/

// handle sumitted data

$context_file = isset($_REQUEST[$FORM_NAME_OPTION]) ? htmlspecialchars($_REQUEST[$FORM_NAME_OPTION]).'.php' : '';

// check for errors

// create form

require_once($DOMAIN_OPTIONS_SUBDIR.'/'.$context_file);
$account_data_generator = new AccountDataGenerator;

$html_title .= ' - '.$ACTION_SUBMIT_DATA_DESC;
$html_body = '<h1>'.$html_title.' ('.pathinfo($context_file, PATHINFO_FILENAME).')</h1>';
if (!isSecure()) {
  $html_body .= '<p class="warning">'.$WARNING_NO_HTTPS_NOTICE.'</p>';
}
$html_body .= '<form method="post" action="">'; // empty actions attribute will select the current file
$html_body .= '<label for="'.$FORM_NAME_FIELD_SEPERATOR.'">'.$TABULAR_DATA_FIELD_SEPERATOR_LABEL.'</label>&nbsp;';
$html_body .= '<select name="'.$FORM_NAME_FIELD_SEPERATOR.'">';
foreach ($field_seperators as $option_index => $option_info) {
  $html_body .= '<option value="'.$option_index.'"><code>'.$option_info['text'].'</code></option>';
}
$html_body .= '</select>';
$html_body .= '<p><label for="'.$FORM_NAME_TABULAR_DATA.'">'.$TABULAR_DATA_FORMAT_NOTICE.'</label></p>';
$html_body .= '<p><code>'.implode(" | ", $account_data_generator->get_list_of_table_row_headers()).'</code></p>';
$html_body .= '<textarea rows="10" cols="100" name="'.$FORM_NAME_TABULAR_DATA.'">';
$html_body .= '</textarea>';
$html_body .= '<input type="hidden" name="'.$FORM_NAME_ACTION.'" value="'.$ACTION_CREATE_FILES.'">';
$html_body .= '<input type="hidden" name="'.$FORM_NAME_OPTION.'" value="'.$context_file.'">';
$html_body .= '<p><input type="submit" value="'.$BUTTON_TEXT_NEXT.'"></p>';
$html_body .= '</form>';

echoHTML();

?>
