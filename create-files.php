<?php
/**
* submit-data.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc creates files and returns zip archive
*/

// handle sumitted data

$context_file = isset($_REQUEST[$FORM_NAME_OPTION]) ? htmlspecialchars($_REQUEST[$FORM_NAME_OPTION]) : '';
$data = isset($_REQUEST[$FORM_NAME_TABULAR_DATA]) ? $_REQUEST[$FORM_NAME_TABULAR_DATA] : '';
$field_seperator_array_id = isset($_REQUEST[$FORM_NAME_FIELD_SEPERATOR]) ? intval($_REQUEST[$FORM_NAME_FIELD_SEPERATOR]) : '';

$field_seperator = $field_seperators[$field_seperator_array_id]['char'];

require_once($DOMAIN_OPTIONS_SUBDIR.'/'.$context_file);
$account_data_generator = new AccountDataGenerator;
// $account_data_generator->get_list_of_table_row_headers());

ignore_user_abort(true); // so tempory files will be delete anyway

// normalize and parse submitted tabular data

$field_headers = $account_data_generator->get_list_of_table_row_headers();
$parsed_data = array();
$data = preg_replace('/\r\n|\r|\n+/', "\n", $data); // Windows uses \r\n newlines; *nix uses \n; Mac uses \r.
$data = trim(preg_replace('/\n+/', "\n", $data)); // get rid of emtpy lines
$lines = preg_split("/\n/", $data);
foreach ($lines as $line_number => $line) {
  $parsed_data[$line_number] = array();
  $rowData = preg_split("/".$field_seperator."/", $line);
  foreach ($field_headers as $field_number => $field_name) {
    $value = '';
    if (array_key_exists($field_number,$rowData)) {
      $value = $rowData[$field_number];
    }
    $parsed_data[$line_number][$field_name] = $value;
  }
}
$account_data_generator->set_data_table($parsed_data);

// create work dir and write target files

$work_folder_name = $TEMP_SUBDIR.'/'.time();
if (!mkdir($work_folder_name, 0777, true)) {
    exit('Could not create Folder '.$work_folder_name.'! Check your directory permissions of '.$TEMP_SUBDIR.'...');
}
foreach ($account_data_generator->get_list_of_targets() as $target) {
  $out_file_contents = $account_data_generator->get_file_contents_of_target($target);
  $out_filename = $target.$account_data_generator->get_file_extension_of_target($target);
  $out_charset = $account_data_generator->get_file_encoding_of_target($target);
  $bom = '';
  switch ($out_charset) {
    case 'UTF-8':
      $bom = "\xEF\xBB\xBF";
      break;
    case 'UTF-16':
      $bom = "\xFE\xFF";
      break;
  }
  $out_file_contents = iconv("UTF-8", $out_charset, $out_file_contents);
  file_put_contents($work_folder_name.'/'.$out_filename, $bom.$out_file_contents);
}

$account_data_generator->add_extra_files($work_folder_name);

// zip target files

$zip_file_path = $work_folder_name.'.zip';
require_once('HZip.php');
HZip::zipDir($work_folder_name, $zip_file_path);

// deliver ZIP file

$zip_file_name = basename($zip_file_path);
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=".$zip_file_name);
header("Content-Length: " . filesize($zip_file_path));
readfile($zip_file_path);

// cleanup; delete target files, work dir and zip file

array_map('unlink', glob("$work_folder_name/*.*"));
rmdir($work_folder_name);
unlink($zip_file_path);

// exit;

?>
