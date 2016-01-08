<?php
/**
* index.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc This file handles all the steps of the wizard
*/

if (!file_exists('config.php')) {
    exit('Please copy `_config.php` to `config.php` and change values to fit your environment, first');
}
require_once('config.php');

require_once($LOCALISATAION_SUBDIR.'/'.$FILE_LOCALIZED_STRINGS);

$SCRIPT_LOCAL_PATH = realpath(dirname(__FILE__));

$HTML_TEMPLATE_TITLE = '<!-- title -->';
$HTML_TEMPLATE_HEAD = '<!-- head -->';
$HTML_TEMPLATE_BODY = '<!-- body -->';

$FORM_NAME_OPTION = 'context';
$FORM_NAME_TABULAR_DATA = 'data';
$FORM_NAME_ACTION = 'action';
$FORM_NAME_FIELD_SEPERATOR = 'field-seperator';

// these names are used as HTML attributes and as file names
$ACTION_SELECT_DOMAIN = 'select-domain';
$ACTION_SUBMIT_DATA = 'submit-data';
$ACTION_CREATE_FILES = 'create-files';

$html_title = $PROJECT_NAME;
$html_header = '';
$html_body = '';

$field_seperators = array(
  array('text' => '\\t ('.$TABULAR_DATA_FIELD_SEPERATOR_TABULATOR.')', 'char' => '\t'),
  array('text' => ', ('.$TABULAR_DATA_FIELD_SEPERATOR_COMMA.')', 'char' => ','),
  array('text' => '; ('.$TABULAR_DATA_FIELD_SEPERATOR_SEMICOLON.')', 'char' => ';')
);


// handle sumitted data

$action = isset($_REQUEST[$FORM_NAME_ACTION]) ? htmlspecialchars($_REQUEST[$FORM_NAME_ACTION]) : '';

if (empty($action)) {
  require_once($ACTION_SELECT_DOMAIN.'.php');
}
else {
  require_once($action.'.php');
}

function echoHTML() {
  global $HTML_TEMPLATE_SUBDIR, $FILE_HTML_TEMPLATE;
  global $HTML_TEMPLATE_TITLE, $HTML_TEMPLATE_HEAD, $HTML_TEMPLATE_BODY;
  global $html_title, $html_header, $html_body;
  $htmlpage = '';
  $htmlpage = file_get_contents($HTML_TEMPLATE_SUBDIR.'/'.$FILE_HTML_TEMPLATE);
  $htmlpage = str_replace($HTML_TEMPLATE_TITLE, $html_title, $htmlpage);
  $htmlpage = str_replace($HTML_TEMPLATE_HEAD, $html_header, $htmlpage);
  $htmlpage = str_replace($HTML_TEMPLATE_BODY, $html_body, $htmlpage);
  header('Content-Type: text/html; charset=utf-8');
  echo($htmlpage);
}

// taken from http://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps#2886224
function isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}

?>
