<?php
/**
* example.php
*
* PHP Version 5.4
*
* @copyright 2015 Michael Rundel
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link https://github.com/8bitcoderookie/Account-Wizard
* @desc This file serves as a template for your own projects
*/

require_once('iAccountDataGenerator.php');

class AccountDataGenerator implements iAccountDataGenerator {

  private $EMAIL_DOMAIN = '@school.at';

  private $FIELDNAME_GIVEN_NAME = 'vorname';
  private $FIELDNAME_SURNAME    = 'nachname';
  private $FIELDNAME_GENDER     = 'geschlecht';
  private $FIELDNAME_STATUS     = 'status'; // N...Neu, R...Repetent
  private $FIELDNAME_ID         = 'schuelerID';
  private $FIELDNAME_CLASS      = 'klasse';
  private $FIELDNAME_SUBCLASS1  = 'zweig1';
  private $FIELDNAME_SUBCLASS2  = 'zweig2';
  private $FIELDNAME_SUBCLASS3  = 'zweig3';
  private $FIELDNAME_SUBCLASS4  = 'zweig4';
  private $FIELDNAME_SUBCLASS5  = 'zweig5';
  private $FIELDNAME_SUBCLASS6  = 'zweig6';
  private $FIELDNAMES;

  private $TARGET_OFFICE_365 = 'office365';
  private $TARGET_MOODLE     = 'moodle';
  private $TARGET_WIN_DOMAIN = 'winsrv';
  private $TARGETS;

  private $user_passwords;
  private $user_emails;
  private $user_usernames;

  private $data_table;

  function __construct() {
    // the order of fieldnames given in this array is the same
    // order of form submitted data table
    $this->FIELDNAMES = array(
      $this->FIELDNAME_CLASS,
      $this->FIELDNAME_SURNAME,
      $this->FIELDNAME_GIVEN_NAME,
      $this->FIELDNAME_STATUS,
      $this->FIELDNAME_ID,
      $this->FIELDNAME_GENDER,
      $this->FIELDNAME_SUBCLASS1,
      $this->FIELDNAME_SUBCLASS2,
      $this->FIELDNAME_SUBCLASS3,
      $this->FIELDNAME_SUBCLASS4,
      $this->FIELDNAME_SUBCLASS5,
      $this->FIELDNAME_SUBCLASS6
    );

    $this->TARGETS = array(
      $this->TARGET_OFFICE_365 => array(
        'extension' => '.csv',
        'encoding' => 'UTF-8',
        'headers' => true,
        'seperator' => ','
      ),
      $this->TARGET_MOODLE => array(
        'extension' => '.csv',
        'encoding' => 'UTF-8',
        'headers' => true,
        'seperator' => ','
      ),
      $this->TARGET_WIN_DOMAIN => array(
        'extension' => '.csv',
        'encoding' => 'Windows-1252',
        'headers' => false,
        'seperator' => ';'
      )
    );
  }

  private function normalize_ascii_name($name) {
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = strtolower($name);
    $name = preg_replace("@[^A-Za-z\-]+@i", '', $name);
    return $name;
  }

  private function get_random_password($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $characters_length - 1)];
    }
    return $password;
  }

  // calculates the the beginning of the current school year
  // and returns the year part only in 4 digit format
  private function get_school_year() {
  	$currentYear = date("Y");
  	$weekday = date("w", mktime(0, 0, 0, 9, 1, $currentYear)); // 0 (for Sunday) through 6 (for Saturday)
  	if ($weekday == 0) {
  		$weekday = 7;
  	}
  	if ($weekday == 1) {
  		$daySchoolBegins = 1;
  	}
  	else {
  		$daySchoolBegins = 9-$weekday;
  	}
  	$timestampSchoolBegins = mktime(0, 0, 0, 9, $daySchoolBegins, $currentYear);
  	$timestamp = time();
  	if ($timestamp < $timestampSchoolBegins) {
  		return ($currentYear-1);
  	}
  	else {
  		return $currentYear;
  	}
  }

  private function create_all_passwords() {
    $this->user_passwords = array();
    foreach ($this->data_table as $line_number => $data_row) {
      $this->user_passwords[$line_number] = $this->get_random_password();
    }
  }

  private function create_all_emails() {
    $this->user_emails = array();
    foreach ($this->data_table as $line_number => $data_row) {
      $surename = $data_row[$this->FIELDNAME_SURNAME];
      $givenname = $data_row[$this->FIELDNAME_GIVEN_NAME];
      $email = $this->normalize_ascii_name($givenname).'.'.$this->normalize_ascii_name($surename).$this->EMAIL_DOMAIN;
      $this->user_emails[$line_number] = $email;
    }
  }

  private function create_all_usernames() {
    $this->user_usernames = array();
    foreach ($this->data_table as $line_number => $data_row) {
      $surename = $data_row[$this->FIELDNAME_SURNAME];
      $givenname = $data_row[$this->FIELDNAME_GIVEN_NAME];
      // windows domain login names are limited to 20 characters
      // $username = substr(normalize_ascii_name($givenname).'.'.normalize_ascii_name($surename), 0, 20);
      $username = mb_substr($this->normalize_ascii_name($givenname).'.'.$this->normalize_ascii_name($surename), 0, 20);
      $this->user_usernames[$line_number] = $username;
    }
  }

  private function get_file_contents_moodle() {
    $header = array(
      'username',
      'lastname',
      'firstname',
      'email',
      'idnumber',
      'city',
      'country',
      'lang',
      'auth',
      'timezone',
      'description',
      'mnethostid',
      'mailformat',
      'maildisplay',
      'autosubscribe',
      'cohort1',
      'cohort2',
      'cohort3',
      'cohort4',
      'cohort5',
      'cohort6'
    );
    $out = '';
    $sep = $this->TARGETS[$this->TARGET_OFFICE_365]['seperator'];
    $out .= implode($sep, $header)."\n";
    foreach ($this->data_table as $line_number => $data_row) {
      $description = 'Schüler';
      if ( mb_strtolower($data_row[$this->FIELDNAME_GENDER]) == 'w') {
        $description = 'Schülerin';
      }
      $cohort2 = $this->get_school_year().'-'.mb_strtoupper($data_row[$this->FIELDNAME_CLASS]);
      $cohort3 = '';
      if ( !empty($data_row[$this->FIELDNAME_SUBCLASS1]) ) {
        $cohort3 = $cohort2.'-'.mb_strtoupper($data_row[$this->FIELDNAME_SUBCLASS1]);
      }
      $cohort4 = '';
      if ( !empty($data_row[$this->FIELDNAME_SUBCLASS2]) ) {
        $cohort4 = $cohort2.'-'.mb_strtoupper($data_row[$this->FIELDNAME_SUBCLASS2]);
      }
      $cohort5 = '';
      if ( !empty($data_row[$this->FIELDNAME_SUBCLASS3]) ) {
        $cohort5 = $cohort2.'-'.mb_strtoupper($data_row[$this->FIELDNAME_SUBCLASS3]);
      }
      $cohort6 = '';
      if ( !empty($data_row[$this->FIELDNAME_SUBCLASS4]) ) {
        $cohort6 = $cohort2.'-'.mb_strtoupper($data_row[$this->FIELDNAME_SUBCLASS4]);
      }
      $data = array(
        $this->user_usernames[$line_number], // username
        $data_row[$this->FIELDNAME_SURNAME], // lastname
        $data_row[$this->FIELDNAME_GIVEN_NAME], // firstname
        $this->user_emails[$line_number], // email
        $data_row[$this->FIELDNAME_ID], // idnumber
        'Vienna', // city
        'AT', // country
        'de', // lang
        'ldap', // auth
        'Europe/Vienna', // timezone
        $description, // description
        '3', // mnethostid
        '1', // mailformat
        '0', // maildisplay
        '0', // autosubscribe
        'SchuelerInnen', // cohort1
        $cohort2, // cohort2
        $cohort3, // cohort3
        $cohort4, // cohort4
        $cohort5, // cohort5
        $cohort6 // cohort6
      );
      $out .= implode($sep, $data)."\n";
    }
    return $out;
  }

  private function get_file_contents_office365() {
    $header = array(
      'Benutzername',
      'Vorname',
      'Nachname',
      'Anzeigename',
      'Position',
      'Abteilung',
      'Büronummer',
      'Telefon (geschäftlich)',
      'Mobiltelefon',
      'Faxnummer',
      'Adresse',
      'Ort',
      'Bundesland/Kanton',
      'Postleitzahl',
      'Land oder Region'
    );
    $out = '';
    $sep = $this->TARGETS[$this->TARGET_OFFICE_365]['seperator'];
    $out .= implode($sep, $header)."\n";
    foreach ($this->data_table as $line_number => $data_row) {
      $position = 'Schüler';
      if ( mb_strtolower($data_row[$this->FIELDNAME_GENDER]) == 'w') {
        $position = 'Schülerin';
      }
      $data = array(
        $this->user_emails[$line_number], // Benutzername
        $data_row[$this->FIELDNAME_GIVEN_NAME], // Vorname
        $data_row[$this->FIELDNAME_SURNAME], // Nachname
        $data_row[$this->FIELDNAME_GIVEN_NAME].' '.$data_row[$this->FIELDNAME_SURNAME], // Anzeigename
        $position, // Position
        '', // Abteilung
        '', // Büronummer
        '', // Telefon (geschäftlich)
        '', // Mobiltelefon
        '', // Faxnummer
        '', // Adresse
        'Wien', // Ort
        'Wien', // Bundesland/Kanton
        '', // Postleitzahl
        'Österreich' // Land oder Region
      );
      $out .= implode($sep, $data)."\n";
    }
    return $out;
  }

  private function get_file_contents_windows_domain() {
    $out = '';
    $sep = $this->TARGETS[$this->TARGET_WIN_DOMAIN]['seperator'];
    foreach ($this->data_table as $line_number => $data_row) {
      $data = array(
        $data_row[$this->FIELDNAME_ID], // SchülerID
        $data_row[$this->FIELDNAME_CLASS], // Klasse
        $data_row[$this->FIELDNAME_SURNAME], // Nachname
        $data_row[$this->FIELDNAME_GIVEN_NAME], // Vorname
        $this->user_emails[$line_number], // E-Mail
        $this->user_usernames[$line_number], // Benutzername
        $this->user_passwords[$line_number] // Kennwort
      );
      $out .= implode($sep, $data)."\n";
    }
    return $out;
  }

  // definition of inferface iAccountDataGenerator functions
  // read ./iAccountDataGenerator.php for more information

  public function set_data_table($data_array) {
    $this->data_table = $data_array;
    $this->create_all_passwords();
    $this->create_all_emails();
    $this->create_all_usernames();
  }

  public function get_list_of_table_row_headers() {
    return $this->FIELDNAMES;
  }

  public function get_list_of_targets() {
    return array_keys($this->TARGETS);
  }

  public function get_file_extension_of_target($target) {
    return $this->TARGETS[$target]['extension'];
  }

  public function get_file_encoding_of_target($target) {
    return $this->TARGETS[$target]['encoding'];
  }

  public function get_file_contents_of_target($target) {
    switch ($target) {
      case $this->TARGET_OFFICE_365:
        return  $this->get_file_contents_office365();
      case $this->TARGET_MOODLE:
        return $this->get_file_contents_moodle();
      case $this->TARGET_WIN_DOMAIN:
        return $this->get_file_contents_windows_domain();
      default:
        return 'unknown target "'.$target.'"';
    }
  }


  public function add_extra_files($relative_path) {
    $readme = <<< END_OF_STRING
# Windows Server

Copy file [winsrv.csv](winsrv.csv) to your windows server and use your script
create accounts in your active directory

# Office 365:

Go to ([https://portal.office.com/admin/default.aspx#ActiveUsersPage](https://portal.office.com/admin/default.aspx#ActiveUsersPage))
and upload file [office365.csv](office365.csv)

# Moodle 2.9

Go to  [http://www.myserver.ac.at/moodle/admin/tool/uploaduser/index.php](http://www.brg4.ac.at/moodle/admin/tool/uploaduser/index.php)
and upload [moodle.csv](moodle.csv).
END_OF_STRING;
    file_put_contents($relative_path.'/readme.md.txt', $readme);
  }
}
?>
