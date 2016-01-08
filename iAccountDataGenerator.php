<?php
/**
* iAccountDataGenerator.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc interface for option files
*/

interface iAccountDataGenerator
{

  /**
    * @desc pass parsed table data to class instance
    * @param array $data_array - nested array of parsed data of the form
    *              Array (
    *                     [0] => Array (
    *                             [row_header1] => data1
    *                             [row_header2] => data2
    *                             ...
    *                         )
    *                     [1] => Array (
    *                             [row_header1] => data1
    *                             [row_header2] => data2
    *                         )
    *                     ...
    *              )
    *        strings 'row_headerx' are the headers got from get_list_of_table_row_headers()
    *        strings 'datax' are the strings submitted with the form
  */
  public function set_data_table($data_array);


  /**
    * @desc get list of strings containing all expected field names for data upload
    * @return array - array of strings
  */
  public function get_list_of_table_row_headers();

  /**
    * @desc get list of file targets used for file creation
    * @return array - array of strings
  */
  public function get_list_of_targets();

  /**
    * @desc get file extension for specific target file
    * @param string $target - string identifiing the target as from get_list_of_targets()
    * @return string - file extension for target file
  */
  public function get_file_extension_of_target($target);

  /**
    * @desc get file encoding for specific target file
    * @param string $target - string identifiing the target as from get_list_of_targets()
    * @return string - representing the string $out_charset in iconv() [http://php.net/manual/en/function.iconv.php]
  */
  public function get_file_encoding_of_target($target);

  /**
    * @desc get list of strings containing all expected field names for data upload
    * @param string $target - string identifiing the target
    * @return string - text to write to target file
  */
  public function get_file_contents_of_target($target);

  /**
    * @desc you can add extra files to temp folder before zip creation, like readme.txt, etc.
    * @param string $relative_path - realtive path to temp directory
  */
  public function add_extra_files($relative_path);

}
?>
