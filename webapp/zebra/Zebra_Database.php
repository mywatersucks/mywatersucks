<?php

/**
 *  An advanced, compact, lightweight, object-oriented MySQL database wrapper built upon PHP's MySQL extension. It
 *  provides methods for interacting with MySQL databases that are more intuitive and fun to use than PHP's default
 *  ones.
 *
 *  Provides a comprehensive debugging interface with detailed information about the executed queries: execution time,
 *  returned/affected rows, excerpts of the found rows, error messages, etc. It also automatically EXPLAIN's each SELECT
 *  query (so you don't miss those keys again!).
 *
 *  It encourages developers to write maintainable code and provides a better default security layer by encouraging the
 *  use of prepared statements, where parameters are automatically escaped automatically.
 *
 *  The code is heavily commented and generates no warnings/errors/notices when PHP's error reporting level is set to
 *  E_ALL.
 *
 *  Visit {@link http://stefangabos.ro/php-libraries/zebra-database/} for more information.
 *
 *  For more resources visit {@link http://stefangabos.ro/}
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @version    2.6 (last revision: September 03, 2011)
 *  @copyright  (c) 2006 - 2011 Stefan Gabos
 *  @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 *  @package    Zebra_Database
 */

class Zebra_Database
{

    /**
     *  After an INSERT, UPDATE or DELETE query, read this property to get the number of affected rows.
     *
     *  <i>This is a read-only property!</i>
     *
     *  <code>
     *  // after an "action" query...
     *  echo $db->affected_rows;
     *  </code>
     *
     *  @var integer
     */
    var $affected_rows;

    /**
     *  Path (with trailing slash) where to store cached queries results.
     *
     *  <i>This folder must be relative to your working path and not the class' path!</i>
     *
     *  @var string
     */
    var $cache_path;

    /**
     *  Array with cached results.
     *
     *  We will use this for fetching and seek
     *
     *  @access private
     */
    var $cached_results;

    /**
     *  Sets how many of the records returned by a SELECT query should be shown in the debug console.
     *
     *  <code>
     *  // show more records
     *  $db->console_show_records(50);
     *  </code>
     *
     *  <i>Be aware that having this property set to a high number (thousands or more) and having a query that returns
     *  that many rows, can cause your script to crash due to memory limitations. In this case you should either lower
     *  the value of this property or try and set PHP's memory limit higher using:</i>
     *
     *  <code>
     *  // set PHP's memory limit to 20 MB
     *  ini_set('memory_limit','20M');
     *  </code>
     *
     *  Default is 20.
     *
     *  @since  1.0.9
     *
     *  @var integer
     */
    var $console_show_records;

    /**
     *  Currently selected MySQL database
     *
     *  @access private
     */
    var $database;

    /**
     *  Setting this property to TRUE, will instruct the class to generate debug information for each query it executes.
     *
     *  Debug information can later be reviewed by calling the {@link show_debug_console()} method.
     *
     *  <b>Don't forget to set this to FALSE when going live. Generating the debug information consumes a lot of
     *  resources and is meant to be used in the development process only!</b>.
     *
     *  Note that not calling <i>show_debug_console()</i> when {@link debug} is set to TRUE, will not disable debug
     *  information: debug information will still be generated only it will not be shown!
     *
     *  The propper solution is to always use show_debug_console() at the end of your scripts and simply change the state
     *  of <i>$debug</i> as <i>show_debug_console()</i> will not display anything if <i>$debug</i> is set to FALSE.
     *
     *  <code>
     *  $db->debug = false;
     *  </code>
     *
     *  Default is TRUE.
     *
     *  @var boolean
     */
    var $debug;

    /**
     *  All debug information is stored in this array.
     *
     *  @access private
     */
    var $debug_info;

    /**
     *  An array of IP addresses for which to show the debug console (when calling {@link show_debug_console()} and
     *  {@link debug} is TRUE).
     *
     *  Leaving this an empty array, will display the debug console for everybody.
     *
     *  <code>
     *  // show the debug console only to specific IPs
     *  $db->debugger_ip = array('192.168.0.12', '192.168.0.13');
     *  </code>
     *
     *  Default is an empty array.
     *
     *  @since  1.0.6
     *
     *  @var array
     */
    var $debugger_ip;

    /**
     *  By default, if {@link set_charset()} is not called, a warning message will be displayed in the debug console.
     *
     *  The ensure that data is both properly saved and retrieved from the database you should call this method, first
     *  thing after connecting to the database.
     *
     *  If you don't want to call the method and don't want to see the warning either, set this property to FALSE.
     *
     *  Default is TRUE.
     *
     *  @var boolean
     */
    var $disable_warnings;

    /**
     *  After a SELECT query done through either {@link select()} or {@link query()} methods, and having set the
     *  <i>$calc_rows</i> argument to TRUE, this property would contain the number of records that would have been
     *  returned if there was no LIMIT applied to the query.
     *
     *  If <i>$calc_rows</i> is FALSE, or is TRUE but there is no LIMIT applied to the query, this property's value will
     *  be equal to {@link returned_rows}.
     *
     *  <i>This is a read-only property!</i>
     *
     *  @var integer
     */
    var $found_rows;

    /**
     *  By setting this property to TRUE, the execution of the script will be halted upon an unsuccessful query and
     *  the debug console will be displayed, <i>if</i> {@link debug} is TRUE and the viewer's IP address is in the
     *  {@link debugger_ip} array (or <i>$debugger_ip</i> is an empty array).
     *
     *  <code>
     *  // don't stop execution on critical errors (if possible)
     *  $db->halt_on_errors = false;
     *  </code>
     *
     *  Default is TRUE.
     *
     *  @since  1.0.5
     *
     *  @var  boolean
     */
    var $halt_on_errors;

    /**
     *  The language to be used in the debug console.
     *
     *  The name of the PHP file to use from the <i>/languages</i> folder, without extension (i.e. "german" for the
     *  german language not "german.php").
     *
     *  <i>Language file must exist in the "languages" folder!</i>
     *
     *  <code>
     *  // set a different language for the debug console
     *  $db->language = 'french';
     *  </code>
     *
     *  Default is "english".
     *
     *  @var string
     */
    var $language;

    /**
     *  MySQL link identifier.
     *
     *  @access private
     */
    var $link_identifier;

    /**
     *  Path (with trailing slash) where to store the log file.
     *
     *  Data is written to the log file when calling the {@link write_log()} method.
     *
     *  <i>At the given path the script will attempt to create a file named "log.txt". Remember to grant the appropriate
     *  right to the script!</i>
     *
     *  <b>IF YOU'RE LOGGING, MAKE SURE YOU HAVE A CRON JOB OR ANYTHING THAT DELETES THE LOG FILE FROM TIME TO TIME!</b>
     *
     *  @var string
     */
    var $log_path;

    /**
     *  Time (in seconds) after which a query will be considered as running for too long.
     *
     *  If a query's execution time exceeds this number, a notification email will be automatically sent to the address
     *  defined by {@link notification_address}, having {@link notifier_domain} in subject.
     *
     *  <code>
     *  // consider queries running for more than 5 seconds as slow and send email
     *  $db->max_query_time = 5;
     *  </code>
     *
     *  Default is 10.
     *
     *  @var integer
     */
    var $max_query_time;

    /**
     *  By setting this property to TRUE, a minimized version of the debug console will be shown by default.
     *
     *  Clicking on it, will show the full debug console.
     *
     *  Default is TRUE
     *
     *  @since  1.0.4
     *
     *  @var boolean
     */
    var $minimize_console;

    /**
     *  Email address to which notification emails to be sent when a query's execution time exceeds the number of
     *  seconds set by {@link max_query_time}.
     *
     *  If a query's execution time exceeds the number of seconds set by {@link max_query_time}, a notification email
     *  will be automatically sent to the address defined by {@link notification_address}, having {@link notifier_domain}
     *  in subject.
     *
     *  <code>
     *  // the email address where to send an email when there are slow queries
     *  $db->notifier_address = 'youremail@yourdomain.com';
     *  </code>
     *
     *  @var string
     */
    var $notification_address;

    /**
     *  Domain name to be used in the subject of notification emails sent when a query's execution time exceeds the number
     *  of seconds set by {@link max_query_time}.
     *
     *  If a query's execution time exceeds the number of seconds set by {@link max_query_time}, a notification email
     *  will be automatically sent to the address defined by {@link notification_address}, having {@link notifier_domain}
     *  in subject.
     *
     *  <code>
     *  // set a domain name so that you'll know where the email comes from
     *  $db->notifier_domain = 'yourdomain.com';
     *  </code>
     *
     *  @var string
     */
    var $notifier_domain;

    /**
     *  After a SELECT query, read this property to get the number of returned rows.
     *
     *  <i>This is a read-only property!</i>
     *
     *  See {@link found_rows} also.
     *
     *  <code>
     *  // after a select query...
     *  echo $db->returned_rows;
     *  </code>
     *
     *  @since  1.0.4
     *
     *  @var integer
     */
    var $returned_rows;

    /**
     *  Tells whether a transaction is in progress or not.
     *
     *  Possible values are
     *  -   0, no transaction is in progress
     *  -   1, a transaction is in progress
     *  -   2, a transaction is in progress but an error occurred with one of the queries
     *  -   3, transaction is run in test mode and it will be rolled back upon completion
     *
     *  @access private
     */
    var $transaction_status;

    /**
     *  Array of warnings, generated by the script, to be shown to the user in the debug console
     *
     *  @access private
     */
    var $warnings;

    /**
     *  Constructor of the class
     *
     *  Initializes the class' properties
     */
    function Zebra_Database()
    {

        // get path of class and replace (on a windows machine) \ with /
        // this path is to be used for all includes as it is an absolute path
        $this->path = preg_replace('/\\\/', '/', dirname(__FILE__));

        // sets default values for the class' properties
        // public properties
        $this->cache_path = $this->path . '/cache/';

        $this->console_show_records = 20;

        $this->halt_on_errors = $this->minimize_console = true;

        $this->language('english');

        $this->max_query_time = 10;

        $this->log_path = $this->notification_address = $this->notifier_domain = '';

        $this->total_execution_time = $this->transaction_status = 0;

        // private properties
        $this->cached_results = $this->debug_info = $this->debugger_ip = array();

        $this->debug = $this->database = $this->link_identifier = false;

        // set default warnings:
        $this->warnings = array(
            'charset'       =>  true,   // set_charset not called
        );

    }

    /**
     *  Closes the MySQL connection
     *
     *  @since  1.1.0
     *
     *  @return boolean                                 Returns TRUE on success or FALSE on failure.
     */
    function close()
    {

        // close the last one open
        @mysql_close($this->link_identifier);

    }

    /**
     *  Opens a connection to a MySQL Server and selects a database.
     *
     *  <i>Since the library is using "lazy connection" (it is not actually connecting to the database until the first
     *  query is executed) there's no link identifier available when calling this method!</i>
     *
     *  <i>If you need the link identifier use the {@link get_link()} method!</i>
     *
     *  <code>
     *  // create the database object
     *  $db = new Zebra_Database();
     *
     *  // notice that we're not doing any error checking. errors will be shown in the debug console
     *  $db->connect('host', 'username', 'password', 'database');
     *
     *  //  code goes here
     *
     *  // show the debug console (if enabled)
     *  $db->show_debug_console();
     *  </code>
     *
     *  @param  string  $host       The address of the MySQL server to connect to (i.e. localhost).
     *
     *  @param  string  $user       The user name used for authentication when connecting to the MySQL server.
     *
     *  @param  string  $password   The password used for authentication when connecting to the MySQL server.
     *
     *  @param  string  $database   The database to be selected after the connection is established.
     *
     *  @param  boolean $is_new     (Optional) By default, if a second call is made to mysql_connect() with the same
     *                              host, user and password, no new link will be established, but instead, the link
     *                              identifier of the already opened link will be returned.
     *
     *                              Therefore, you <b>MUST</b> set this to TRUE whenever you instantiate this class
     *                              other than for the first time, inside the same script, for accessing a different
     *                              database than the previous one, but on the same host and with the same user name and
     *                              password.
     *
     *                              Default is FALSE.
     *
     *  @return void
     */
    function connect($host, $user, $password, $database, $is_new = false)
    {

        // we are using lazy-connection
        // that is, we are not going to actually connect to the database until we execute the first query
        // the actual connection is done by the _connected method
        $this->credentials = array(
            'host'      =>  $host,
            'user'      =>  $user,
            'password'  =>  $password,
            'database'  =>  $database,
            'is_new'    =>  $is_new,
        );

    }

    /**
     *  Counts the values in a column of a table.
     *
     *  <code>
     *  // count male users
     *  $male = $db->dcount('id', 'users', 'gender = "M"');
     *
     *  // when working with variables you should use the following syntax
     *  // this way variables will be mysql_real_escape_string-ed first
     *  $users = $db->dcount('id', 'users', 'gender = ?', array($gender));
     *  </code>
     *
     *  @param  string  $column         Name of the column in which to do the counting.
     *
     *  @param  string  $table          Name of the table containing the column.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @return mixed                   Returns the number of counted records, or FALSE if no records matching the given
     *                                  criteria (if any) were found. It also returns FALSE if there are no records in
     *                                  the table or on error.
     *
     *                                  <i>This method may return boolean FALSE, but may also return a non-Boolean value
     *                                  which evaluates to FALSE, such as 0. Use the === operator for testing the return
     *                                  value of this method.</i>
     */
    function dcount($column, $table, $where = '', $replacements = '', $cache = false, $highlight = false)
    {

        // run the query
        $this->query('

            SELECT
                COUNT(' . $column . ') AS counted
            FROM
                ' . $table .
            ($where != '' ? ' WHERE ' . $where : '')

        , $replacements, $cache, $highlight);

        // if query was executed successfully and one or more records were returned
        if ($this->last_result && $this->returned_rows > 0) {

            // fetch the result
            $row = $this->fetch_assoc();

            // return the result
            return $row['counted'];

        }

        // if error or no records
        return false;

    }

    /**
     *  Deletes rows from a table.
     *
     *  <code>
     *  // delete male users
     *  $db->delete('users', 'gender = "M"');
     *
     *  // when working with variables you should use the following syntax
     *  // this way variables will be mysql_real_escape_string-ed first
     *  $db->delete('users', 'gender = ?', array($gender));
     *  </code>
     *
     *  @param  string  $table          Table from which to delete.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$table</i> and <i>$where</i>. Each item will be automatically
     *                                  {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @since  1.0.9
     *
     *  @return boolean                 Returns TRUE on success, or FALSE on error
     */
    function delete($table, $where = '', $replacements = '', $highlight = false)
    {

        // run the query
        $this->query('

            DELETE FROM
                ' . $table .
            ($where != '' ? ' WHERE ' . $where : '')

        , $replacements, false, $highlight);

        // if query was successful
        if ($this->last_result) return true;

        // if query was unsuccessful
        return false;

    }

    /**
     *  Returns one or more columns from ONE row of a table.
     *
     *  <code>
     *  // get name, surname and age of all male users
     *  $result = $db->dlookup('name, surname, age', 'users', 'gender = "M"');
     *
     *  // when working with variables you should use the following syntax
     *  // this way variables will be mysql_real_escape_string-ed first
     *  $result = $db->dlookup('name, surname, age', 'users', 'gender = ?', array($gender));
     *  </code>
     *
     *  @param  string  $column         One or more columns to return data from.
     *
     *                                  <i>If only one column is specified, the returned result will be the specified
     *                                  column's value, whereas if more columns are specified, the returned result will
     *                                  be an associative array!</i>
     *
     *                                  <i>You may use "*" (without the quotes) to return all the columns from the
     *                                  row.</i>
     *
     *  @param  string  $table          Name of the in which to search.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @return mixed                   Found value/values, or FALSE if no records matching the given criteria (if any)
     *                                  were found. It also returns FALSE if there are no records in the table or on error.
     */
    function dlookup($column, $table, $where = '', $replacements = '', $cache = false, $highlight = false)
    {

        // run the query
        $this->query('

            SELECT
                ' . $column . '
            FROM
                ' . $table .
            ($where != '' ? ' WHERE ' . $where : '') . '
            LIMIT 1

        ', $replacements, $cache, $highlight);

        // if query was executed successfully and one or more records were returned
        if ($this->last_result && $this->returned_rows > 0) {

            // fetch the result
            $row = $this->fetch_assoc();

            // if all columns were requested
            if (trim($column) == '*') {

                // return all the columns in the row, as an array
                return $row;

            // if not all columns were requested
            } else {

                // convert requested columns to an array
                $columns_list = explode(',', $column);

                // trim any white spaces and remove, if found, the escape characters used for columns having reserved
                // names (like `order` or `status`)
                array_walk($columns_list, create_function('&$value', '$value = str_replace("`", "", trim($value));'));

                // if the value of only one column was requested
                if (count($columns_list) == 1) {

                    // the prepared column name to return
                    $column = array_pop($columns_list);

                    // if requested column exists in the row
                    if (isset($row[$column])) {

                        // return the value of the requested columns
                        return $row[$column];

                    }

                // if the values of more than one columns were requested
                } else {

                    $return = array();

                    // iterate through the requested columns
                    foreach ($columns_list as $column) {

                        // if requested column exists in the row
                        if (isset($row[$column])) {

                            // put it in the return result
                            $return[$column] = $row[$column];

                        }

                    }

                    // return requested fields
                    return $return;

                }

            }

        }

        // if error or no records
        return false;

    }

    /**
     *  Looks up the maximum value in a column of a table.
     *
     *  <code>
     *  // get the maximum age of male users
     *  $result = $db->dmax('age', 'users', 'gender = "M"');
     *
     *  // when working with variables you should use the following syntax
     *  // this way variables will be mysql_real_escape_string-ed first
     *  $result = $db->dmax('age', 'users', 'gender = ?', array($gender));
     *  </code>
     *
     *  @param  string  $column         Name of the column in which to search.
     *
     *  @param  string  $table          Name of table in which to search.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @return mixed                   The maximum value in the column, or FALSE if no records matching the given criteria
     *                                  (if any) were found. It also returns FALSE if there are no records in the table
     *                                  or on error.
     *
     *                                  <i>This method may return boolean FALSE, but may also return a non-Boolean value
     *                                  which evaluates to FALSE, such as 0. Use the === operator for testing the return
     *                                  value of this method.</i>
     */
    function dmax($column, $table, $where = '', $replacements = '', $cache = false, $highlight = false)
    {

        // run the query
         $this->query('

            SELECT
                MAX(' . $column . ') AS maximum
            FROM
                ' . $table .
            ($where != '' ? ' WHERE ' . $where : '')

        , $replacements, $cache, $highlight);

        // if query was executed successfully and one or more records were returned
        if ($this->last_result && $this->returned_rows > 0) {

            // fetch the result
            $row = $this->fetch_assoc();

            // return the result
            return $row['maximum'];

        }

        // if error or no records
        return false;

    }

    /**
     *  Sums the values in a column of a table.
     *
     *  Example:
     *
     *  <code>
     *  // get the total logins of all male users
     *  $result = $db->dsum('login_count', 'users', 'gender = "M"');
     *
     *  // when working with variables you should use the following syntax
     *  // this way variables will be mysql_real_escape_string-ed first
     *  $result = $db->dsum('login_count', 'users', 'gender = ?', array($gender));
     *  </code>
     *
     *  @param  string  $column         Name of the column in which to sum values.
     *
     *  @param  string  $table          Name of the table in which to search.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @return mixed                   Returns the sum, or FALSE if no records matching the given criteria (if any) were
     *                                  found. It also returns FALSE if there are no records in the table or on error.
     *
     *                                  <i>This method may return boolean FALSE, but may also return a non-Boolean value
     *                                  which evaluates to FALSE, such as 0. Use the === operator for testing the return
     *                                  value of this method.</i>
     */
    function dsum($column, $table, $where = '', $replacements = '', $cache = false, $highlight = false)
    {

        // run the query
        $this->query('

            SELECT
                SUM(' . $column . ') AS total
            FROM
                '. $table .
            ($where != '' ? ' WHERE ' . $where : '')

        , $replacements, $cache, $highlight);

        // if query was executed successfully and one or more records were returned
        if ($this->last_result && $this->found_rows > 0) {

            // fetch the result
            $row = $this->fetch_assoc();

            // return the result
            return $row['total'];

        }

        // if error or no records
        return false;

    }

    /**
     *  Escapes special characters in a string for use in an SQL statement.
     *
     *  <i>This method also encloses given string in single quotes!</i>
     *
     *  <i>Works even if {@link http://www.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc magic_quotes}
     *  is ON.</i>
     *
     *  <code>
     *  // use the method in a query
     *  // THIS IS NOT THE RECOMMENDED METHOD!
     *  $db->query('
     *      SELECT
     *          *
     *      FROM
     *          users
     *      WHERE
     *          gender = "' . $db->escape($gender) . '"
     *  ');
     *
     *  // the recommended method
     *  // (variable are automatically escaped this way)
     *  $db->query('
     *      SELECT
     *          *
     *      FROM
     *          users
     *      WHERE
     *          gender = ?
     *  ', array($gender));
     *  </code>
     *
     *  @param  string  $string     String that is to be escaped.
     *
     *  @return string              Returns the escaped string.
     */
    function escape($string)
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if "magic quotes" are on
            if (get_magic_quotes_gpc()) {

                // strip slashes
                $string = stripslashes($string);

            }

            // escape the string
            $result = mysql_real_escape_string($string, $this->link_identifier);

            // return escaped string
            return $result;

        }

        // upon error, we don't have to report anything as _connected() method already did
        // just return FALSE
        return false;

    }

    /**
     *  Returns an associative array that corresponds to the fetched row and moves the internal data pointer ahead. The
     *  data is taken from the resource created by the previous query or from the resource given as argument.
     *
     *  <code>
     *  // create the database object
     *  // run a query
     *  $db->query('SELECT * FROM table WHERE criteria = ?', array($criteria));
     *
     *  // iterate through the found records
     *  while ($row = $db->fetch_assoc()) {
     *
     *      // code goes here
     *
     *  }
     *  </code>
     *
     *  @param  resource    $resource   (Optional) Resource to fetch.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @return mixed                   Returns an associative array that corresponds to the fetched row and moves the
     *                                  internal data pointer ahead, or FALSE if there are no more rows.
     */
    function fetch_assoc($resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and a query was run before
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            // if $resource is a valid resource
            if (is_resource($resource)) {

                // fetch and return next row from the result set
                return mysql_fetch_assoc($resource);

            // if $resource is a pointer to an array taken from cache
            } elseif (is_integer($resource) && isset($this->cached_results[$resource])) {

                // get the current entry from the array and advance the pointer
                $result = each($this->cached_results[$resource]);

                // return as an associative array
                return @$result[1];

            // if $resource is invalid
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as either the _connected() method already did
        // or did so the checking for valid resource
        return false;

    }

    /**
     *  Returns an associative array containing all the rows from the resource created by the previous query or from the
     *  resource given as argument and moves the internal pointer to the end.
     *
     *  <code>
     *  // run a query
     *  $db->query('SELECT * FROM table WHERE criteria = ?', array($criteria));
     *
     *  // fetch all the rows as an associative array
     *  $records = $db->fetch_assoc_all();
     *  </code>
     *
     *  @param  string      $index      (Optional) A column name from the records, containing unique values.
     *
     *                                  If specified, each entry in the returned array will have its index equal to the
     *                                  the value of the specified column for each particular row.
     *
     *                                  <i>If not specified, returned array will have numerical indexes, starting from 0.</i>
     *
     *  @param  resource    $resource   (Optional) Resource to fetch.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @since  1.1.2
     *
     *  @return mixed                   Returns an associative array containing all the rows from the resource created
     *                                  by the previous query or from the resource given as argument and moves the
     *                                  internal pointer to the end. Returns FALSE on error.
     */
    function fetch_assoc_all($index = '', $resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and a query was run before
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            if (

                // if $resource is a valid resource OR
                is_resource($resource) ||

                // $resource is a pointer to an array taken from cache
                (is_integer($resource) && isset($this->cached_results[$resource]))

            ) {

                // this is the array that will contain the results
                $result = array();

                // move the pointer to the start of $resource
                // if there are any rows available (notice the @)
                if (@$this->seek(0, $resource)) {

                    // iterate through the records
                    while ($row = $this->fetch_assoc($resource)) {

                        // if $index was specified and exists in the returned row
                        if (trim($index) != '' && isset($row[$index])) {

                            // add data to the result
                            $result[$row[$index]] = $row;

                        // if $index was not specified or does not exists in the returned row
                        } else {

                            // add data to the result
                            $result[] = $row;

                        }

                    }

                }

                // return the results
                return $result;

            // if $resource is invalid
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as either the _connected() method already did
        // or did so the checking for valid resource
        return false;

    }

    /**
     *  Returns an object with properties that correspond to the fetched row and moves the internal data pointer ahead.
     *  The data is taken from the resource created by the previous query or from the resource given as argument.
     *
     *  <code>
     *  // run a query
     *  $db->query('SELECT * FROM table WHERE criteria = ?', array($criteria));
     *
     *  // iterate through the found records
     *  while ($row = $db->fetch_object()) {
     *
     *      // code goes here
     *
     *  }
     *  </code>
     *
     *  @param  resource    $resource   (Optional) Resource to fetch.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @since  1.0.8
     *
     *  @return mixed                   Returns an object with properties that correspond to the fetched row and moves
     *                                  the internal data pointer ahead, or FALSE if there are no more rows.
     */
    function fetch_obj($resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and a query was run before
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            // if $resource is a valid resource
            if (is_resource($resource)) {

                // fetch and return next row from the result set
                return mysql_fetch_object($resource);

            // if $resource is a pointer to an array taken from cache
            } elseif (is_integer($resource) && isset($this->cached_results[$resource])) {

                // get the current entry from the array and advance the pointer
                $result = each($this->cached_results[$resource]);

                // if we're not past the end of the array
                if ($result !== false) {

                    // create a new generic object -> similar with $obj = new stdClass() but i like this one better ;)
                    $obj = (object) NULL;

                    // populate the object's properties
                    foreach ($result[1] as $key=>$value) {

                        $obj->$key = $value;

                    }

                // if we're past the end of the array
                } else {

                    // make sure we return FALSE
                    $obj = false;

                }

                // return as object
                return $obj;

            // if $resource is invalid
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as either the _connected() method already did
        // or did so the checking for valid resource
        return false;

    }

    /**
     *  Returns an associative array containing all the rows (as objects) from the resource created by the previous query
     *  or from the resource given as argument and moves the internal pointer to the end.
     *
     *  <code>
     *  // run a query
     *  $db->query('SELECT * FROM table WHERE criteria = ?', array($criteria));
     *
     *  // fetch all the rows as an associative array
     *  $records = $db->fetch_obj_all();
     *  </code>
     *
     *  @param  string      $index      (Optional) A column name from the records, containing unique values.
     *
     *                                  If specified, each entry in the returned array will have its index equal to the
     *                                  the value of the specified column for each particular row.
     *
     *                                  <i>If not specified, returned array will have numerical indexes, starting from 0.</i>
     *
     *  @param  resource    $resource   (Optional) Resource to fetch.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @since  1.1.2
     *
     *  @return mixed                   Returns an associative array containing all the rows (as objects) from the resource
     *                                  created by the previous query or from the resource given as argument and moves
     *                                  the internal pointer to the end. Returns FALSE on error.
     */
    function fetch_obj_all($index = '', $resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and a query was run before
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            if (

                // if $resource is a valid resource OR
                is_resource($resource) ||

                // $resource is a pointer to an array taken from cache
                (is_integer($resource) && isset($this->cached_results[$resource]))

            ) {

                // this is the array that will contain the results
                $result = array();

                // move the pointer to the start of $resource
                // if there are any rows available (notice the @)
                if (@$this->seek(0, $resource)) {

                    // iterate through the resource data
                    while ($row = $this->fetch_obj($resource)) {

                        // if $index was specified and exists in the returned row
                        if (trim($index) != '' && isset($row[$index])) {

                            // add data to the result
                            $result[$row[$index]] = $row;

                        // if $index was not specified or does not exists in the returned row
                        } else {

                            // add data to the result
                            $result[] = $row;

                        }

                    }

                }

                // return the results
                return $result;

            // if $resource is invalid
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as either the _connected() method already did
        // or did so the checking for valid resource
        return false;

    }

    /**
     *  Returns an array of associative arrays with information about the columns in the MySQL result associated with
     *  the specified result identifier.
     *
     *  Each entry will have the column's name as index and, associtated, an array with the following keys:
     *
     *  - name
     *  - table
     *  - def
     *  - max_length
     *  - not_null
     *  - primary_key
     *  - multiple_key
     *  - unique_key
     *  - numeric
     *  - blob
     *  - type
     *  - unsigned
     *  - zerofill
     *
     *  <code>
     *  // run a query
     *  $db->query('SELECT * FROM table');
     *
     *  // print information about the columns
     *  print_r('<pre>');
     *  print_r($db->get_columns());
     *  </code>
     *
     *  @param  resource    $resource   (Optional) Resource to fetch columns information from.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @since  2.0
     *
     *  @return mixed                   Returns an associative array with information about the columns in the MySQL
     *                                  result associated with the specified result identifier or FALSE on error.
     */
    function get_columns($resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and a query was run before
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            // if $resource is a valid resource
            if (is_resource($resource)) {

                $result = array();

                // get the number of columns in the resource
                $columns = mysql_num_fields($resource);

                // iterate through the columns in the resource set
                for ($i = 0; $i < $columns; $i++) {

                    // fetch column information
                    $column_info = mysql_fetch_field($resource, $i);

                    // add information to the array of results
                    // converting it first to an associative array
                    $result[$column_info->name] = get_object_vars($column_info);

                }

                // return information
                return $result;

            // if $resource is a pointer to an array taken from cache
            } elseif (is_integer($resource) && isset($this->cached_results[$resource])) {

                // return information that was stored in the cached file
                return $this->column_info;

            // if $resource is invalid
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as either the _connected() method already did
        // or did so the checking for valid resource
        return false;

    }

    /**
     *  Returns the MySQL link identifier associated with the current connection to the MySQL server.
     *
     *  Why a separate method? Because the library uses "lazy connection" (it is not actually connecting to the database
     *  until the first query is executed) there's no link identifier available when calling the {@link connect()} method.
     *
     *  <code>
     *  // create the database object
     *  $db = new Zebra_Database();
     *
     *  // nothing is returned by this method!
     *  $db->connect('host', 'username', 'password', 'database');
     *
     *  // get the link identifier
     *  $link = $db->get_link();
     *  </code>
     *
     *  @since 2.5
     *
     *  @return identifier  Returns the MySQL link identifier associated with the current connection to the MySQL server.
     */
    function get_link()
    {

        // if an active connection exists
        // return the MySQL link identifier associated with the current connection to the MySQL server
        if ($this->_connected()) return $this->link_identifier;

        // if script gets this far, return false as something must've been wrong
        return false;

    }

    /**
     *  Returns information about the columns of a given table, as an associative array.
     *
     *  <code>
     *  // get column information for a table named "table_name"
     *  $db->get_columns('table_name');
     *  </code>
     *
     *  @param  string  $table  Name of table to return column information for.
     *
     *  @since  2.6
     *
     *  @return array           Returns information about the columns of a given table, as an associative array.
     */
    function get_table_columns($table)
    {

        // run the query
        $this->query('

            SHOW COLUMNS FROM ' . $this->escape($table) . '

        ');

        // fetch and return data
        return $this->fetch_assoc_all('Field');

    }

    /**
     *  Returns an associative array with a lot of useful information on all or specific tables only.
     *
     *  <code>
     *  // return status information on tables having their name starting with "users"
     *  $tables = get_table_status('users%');
     *  </code>
     *
     *  @param  string  $pattern    (Optional) Instructs the method to return information only on tables whose name matches
     *                              the given pattern.
     *
     *                              Can be a table name or a pattern with "%" as wildcard.
     *
     *  @since  1.1.2
     *
     *  @return array               Returns an associative array with a lot of useful information on all or specific
     *                              tables only.
     */
    function get_table_status($pattern = '')
    {

        // run the query
        $this->query('
            SHOW
            TABLE
            STATUS
            ' . (trim($pattern) != '' ? 'LIKE ?' : '') . '
        ', array($pattern));

        // fetch and return data
        return $this->fetch_assoc_all('Name');

    }

    /**
     *  Returns an array with all the tables in the current database.
     *
     *  <code>
     *  // get all tables from database
     *  $tables = get_tables();
     *  </code>
     *
     *  @since  1.1.2
     *
     *  @return array   An array with all the tables in the current database.
     */
    function get_tables()
    {

        // fetch all the tables in the database
        $result = $this->fetch_assoc_all($this->query('
            SHOW TABLES
        '));

        $tables = array();

        // as the results returned by default are quite odd
        // translate them to a more usable array
        foreach ($result as $tableName) {

            $tables[] = array_pop($tableName);

        }

        // return the array with the table names
        return $tables;

    }

    /**
     *  Stops the execution of the script at the line where this method is called and, if {@link debug} is set to TRUE and
     *  the viewer's IP address is in the {@link debugger_ip} array (or <i>debugger_ip</i> is an empty array), shows the
     *  debug console.
     *
     *  @since  1.0.7
     *
     *  @return void
     */
    function halt()
    {

        // show the debug console
        $this->show_debug_console();

        // stop further execution of the script
        die();

    }

    /**
     *  Works similarly to PHP's implode() function, with the difference that the "glue" is always the comma and that
     *  this method {@link escape()}'s arguments.
     *
     *  <i>Useful for escaping an array's values used in SQL statements with the "IN" keyword.</i>
     *
     *  <code>
     *  $array = array(1,2,3,4);
     *
     *  //  INCORRECT
     *
     *  //  this would not work as the WHERE clause in the SQL statement would become
     *  //  WHERE column IN ('1,2,3,4')
     *  $db->query('
     *      SELECT
     *          column
     *      FROM
     *          table
     *      WHERE
     *          column IN (?)
     *  ', array($array));
     *
     *  //  CORRECT
     *
     *  //  this would work as the WHERE clause in the SQL statement would become
     *  //  WHERE column IN ('1','2','3','4') which is what we actually need
     *  $db->query('
     *      SELECT
     *          column
     *      FROM
     *          table
     *      WHERE
     *          column IN (' . $db->implode($array) . ')
     *  ');
     *  </code>
     *
     *
     *  @param  array   $pieces     An array with items to be "glued" together
     *
     *  @since  2.0
     *
     *  @return string              Returns the string representation of all the array elements in the same order,
     *                              escaped and with commas between each element.
     */
    function implode($pieces)
    {

        $result = '';

        // iterate through the array's items
        foreach ($pieces as $piece) {

            // glue items together
            $result .= ($result != '' ? ',' : '') . '\'' . $this->escape($piece) . '\'';

        }

        return $result;

    }

    /**
     *  Shorthand for INSERT queries.
     *
     *  When using this method, column names will be enclosed in grave accents " ` " (thus, allowing seamless usage of
     *  reserved words as column names) and values will be automatically escaped.
     *
     *  <code>
     *  $db->insert(
     *      'table',
     *      array(
     *          'column1'   =>  'value1',
     *          'column2'   =>  'value2',
     *  ));
     *  </code>
     *
     *  @param  string  $table          Table in which to insert.
     *
     *  @param  array   $columns        An associative array where the array's keys represent the columns names and the
     *                                  array's values represent the values to be inserted in each respective column.
     *
     *                                  Column names will be enclosed in grave accents " ` " (thus, allowing seamless
     *                                  usage of reserved words as column names) and values will be automatically
     *                                  {@link escape()}d.
     *
     *  @param  boolean $ignore         (Optional) By default, trying to insert a record that would cause a duplicate
     *                                  entry for a primary key would result in an error. If you want these errors to be
     *                                  skipped set this argument to TRUE.
     *
     *                                  For more information see {@link http://dev.mysql.com/doc/refman/5.5/en/insert.html MySQL's INSERT IGNORE syntax}.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @since  1.0.9
     *
     *  @return boolean                 Returns TRUE on success of FALSE on error.
     */
    function insert($table, $columns, $ignore = false, $highlight = false)
    {

        // enclose the column names in grave accents
        $cols = '`' . implode('`,`', array_keys($columns)) . '`';

        // parameter markers for escaping values later on
        $values = rtrim(str_repeat('?,', count($columns)), ',');

        // run the query
        $this->query('

            INSERT' . ($ignore ? ' IGNORE' : '') . ' INTO
                ' . $table . '
                (' . $cols . ')
            VALUES
                (' . $values . ')'

        , array_values($columns), false, $highlight);

        // return true if query was executed successfully
        if ($this->last_result) return true;

        return false;

    }

    /**
     *  Shorthand inserting multiple rows in a single query.
     *
     *  When using this method, column names will be enclosed in grave accents " ` " (thus, allowing seamless usage of
     *  reserved words as column names) and values will be automatically escaped.
     *
     *  <code>
     *  $db->insert_bulk(
     *      'table',
     *      array('column1', 'column2'),
     *      array(
     *          array('value1', 'value2'),
     *          array('value3', 'value4'),
     *          array('value5', 'value6'),
     *          array('value7', 'value8'),
     *          array('value9', 'value10')
     *      )
     *  ));
     *  </code>
     *
     *  @param  string  $table          Table in which to insert.
     *
     *  @param  array   $columns        An array with columns to insert values into.
     *
     *                                  Column names will be enclosed in grave accents " ` " (thus, allowing seamless
     *                                  usage of reserved words as column names).
     *
     *  @param  arrays  $data           An array of an unlimited number of arrays containing values to be inserted.
     *
     *                                  Values will be automatically {@link escape()}d.
     *
     *  @param  boolean $ignore         (Optional) By default, trying to insert a record that would cause a duplicate
     *                                  entry for a primary key would result in an error. If you want these errors to be
     *                                  skipped set this argument to TRUE.
     *
     *                                  For more information see {@link http://dev.mysql.com/doc/refman/5.5/en/insert.html MySQL's INSERT IGNORE syntax}.
     *
     *                                  Default is FALSE.
     *
     *  @since  2.1
     *
     *  @return boolean                 Returns TRUE on success of FALSE on error.
     */
    function insert_bulk($table, $columns, $data, $ignore = false)
    {

        // if $data is not an array of arrays
        if (!is_array(array_pop(array_values($data)))) {

            // save debug information
            $this->_log('errors', array(

                'message'   =>  $this->language['data_not_an_array'],

            ));

        // if arguments are ok
        } else {

            // start preparing the INSERT statement
            $sql = '
                INSERT' . ($ignore ? ' IGNORE' : '') . ' INTO
                    ' . $table . '
                    (' . '`' . implode('`,`', $columns) . '`' . ')
                VALUES
            ';

            // iterate through the arrays
            foreach ($data as $values) {

                // escape values
                $sql .= '(' . $this->implode($values) . '),';

            }

            // run the query
            $this->query(rtrim($sql, ','));

            // return true if query was executed successfully
            if ($this->last_result) return true;

        }

        // if script gets this far, return false as something must've been wrong
        return false;

    }

    /**
     *  Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
     *
     *  @since  1.0.4
     *
     *  @return mixed   The ID generated for an AUTO_INCREMENT column by the previous INSERT query on success,
     *                  '0' if the previous query does not generate an AUTO_INCREMENT value, or FALSE if there was
     *                  no MySQL connection.
     */
    function insert_id()
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if a query was run before
            if (isset($this->last_result)) {

                // return the AUTO_INCREMENT value
                return mysql_insert_id($this->link_identifier);

            // if no query was run before
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // upon error, we don't have to report anything as _connected() method already did
        // just return FALSE
        return false;

    }

    /**
     *  When using this method, if a row is inserted that would cause a duplicate value in a UNIQUE index or PRIMARY KEY,
     *  an UPDATE of the old row is performed.
     *
     *  Read more at {@link http://dev.mysql.com/doc/refman/5.0/en/insert-on-duplicate.html}.
     *
     *  When using this method, column names will be enclosed in grave accents " ` " (thus, allowing seamless usage of
     *  reserved words as column names) and values will be automatically escaped.
     *
     *  <code>
     *  // presuming article_id is a UNIQUE index or PRIMARY KEY, the statement below will insert a new row for given
     *  // $article_id and set the "votes" to 0. But, if $article_id is already in the database, increment the votes'
     *  // numbers.
     *  $db->insert_update(
     *      'table',
     *      array(
     *          'article_id'    =>  $article_id,
     *          'votes'         =>  0,
     *      ),
     *      array(
     *          'votes'         =>  'INC(1)',
     *      )
     *  );
     *  </code>
     *
     *  @param  string  $table          Table in which to insert/update.
     *
     *  @param  array   $columns        An associative array where the array's keys represent the columns names and the
     *                                  array's values represent the values to be inserted in each respective column.
     *
     *                                  Column names will be enclosed in grave accents " ` " (thus, allowing seamless
     *                                  usage of reserved words as column names) and values will be automatically
     *                                  {@link escape()}d.
     *
     *  @param  array   $update         (Optional) An associative array where the array's keys represent the columns names
     *                                  and the array's values represent the values to be inserted in each respective
     *                                  column.
     *
     *                                  This array represents the columns/values to be updated if the inserted row would
     *                                  cause a duplicate value in a UNIQUE index or PRIMARY KEY.
     *
     *                                  If an empty array is given, the values in <i>$columns</i> will be used.
     *
     *                                  Column names will be enclosed in grave accents " ` " (thus, allowing seamless
     *                                  usage of reserved words as column names) and values will be automatically
     *                                  {@link escape()}d.
     *
     *                                  A special value may also be used for when a column's value needs to be
     *                                  incremented or decremented. In this case, use <i>INC(value)</i> where <i>value</i>
     *                                  is the value to increase the column's value with. Use <i>INC(-value)</i> to decrease
     *                                  the column's value. See {@link update()} for an example.
     *
     *                                  Default is an empty array.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @since  2.1
     *
     *  @return boolean                 Returns TRUE on success of FALSE on error.
     */
    function insert_update($table, $columns, $update = array(), $highlight = false)
    {

        // if $update is not given as an array, make it an empty array
        if (!is_array($update)) $update = array();

        // enclose the column names in grave accents
        $cols = '`' . implode('`,`', array_keys($columns)) . '`';

        // parameter markers for escaping values later on
        $values = rtrim(str_repeat('?,', count($columns)), ',');

        // if no $update specified
        if (empty($update)) {

            // use the columns specified in $columns
            $update_cols = '`' . implode('` = ?,`', array_keys($columns)) . '` = ?';

            // use the same column for update as for insert
            $update = $columns;

        // if $update is specified
        } else {

            $update_cols = '';

            // start creating the SQL string and enclose field names in `
            foreach ($update as $column_name => $value) {

                // if special INC() keyword is used
                if (preg_match('/INC\((\-{1})?(.*?)\)/i', $value, $matches) > 0) {

                    $update_cols .= ($cols != '' ? ', ' : '') . '`' . $column_name . '` = `' . $column_name . '` ' . ($matches[1] == '-' ? '-' : '+') . ' ?';

                    $update[$column_name] = $matches[2];

                // the usual way
                } else {

                    $update_cols .= ($update_cols != '' ? ', ' : '') . '`' . $column_name . '` = ?';

                }

            }

        }

        // run the query
        $this->query('

            INSERT INTO
                ' . $table . '
                (' . $cols . ')
            VALUES
                (' . $values . ')
            ON DUPLICATE KEY UPDATE
                ' . $update_cols

        , array_merge(array_values($columns), array_values($update)), false, $highlight);

        // return true if query was executed successfully
        if ($this->last_result) return true;

        return false;

    }

    /**
     *  Sets the language to be used for messages in the debug console.
     *
     *  <code>
     *  // show messages in the debug console in German
     *  $db->language('german');
     *  </code>
     *
     *  @param  string  $language   The name of the PHP language file from the "languages" subdirectory.
     *
     *                              Must be specified without the extension!
     *                              (i.e. "german" for the german language not "german.php")
     *
     *                              Default is "english".
     *
     *  @since  1.0.6
     *
     *  @return void
     */
    function language($language)
    {

        // include the language file
        require_once $this->path . '/languages/' . $language . '.php';

    }

    /**
     *  Optimizes all tables that have overhead (unused, lost space)
     *
     *  <code>
     *  // optimize all tables in the database
     *  $db->optimize();
     *  </code>
     *
     *  @since  1.1.2
     *
     *  @return void
     */
    function optimize()
    {

        // fetch information on all the tables in the database
        $tables = $this->get_table_status();

        // iterate through the database's tables
        foreach ($tables as $table) {

            // if a table has overhead (unused, lost space)
            if ($table['Data_free'] > 0) {

                // optimize the table
                $this->query('OPTIMIZE TABLE `' . $table['Name'] . '`');

            }

        }

    }

    /**
     *  Parses a MySQL dump file (like an export from phpMyAdmin).
     *
     *  <i>If you must parse a very large file and your script crashed due to timeout or because of memory limitations,
     *  try the following:</i>
     *
     *  <code>
     *  // prevent script timeout
     *  set_time_limit(0);
     *
     *  // allow for more memory to be used by the script
     *  ini_set('memory_limit','128M');
     *  </code>
     *
     *  @param  string  $path   Path to the file to be parsed.
     *
     *  @return boolean         Returns TRUE on success or FALSE on failure.
     */
    function parse_file($path)
    {

        // if an active connection exists
        if ($this->_connected()) {

            // read file into an array
            $file_content = file($path);

            // if file was successfully opened
            if ($file_content) {

                $query = '';

                // iterates through every line of the file
                foreach ($file_content as $sql_line) {

                    // trims whitespace from both beginning and end of line
                    $tsql = trim($sql_line);

                    // if line content is not empty and is the line does not represent a comment
                    if ($tsql != '' && substr($tsql, 0, 2) != '--' && substr($tsql, 0, 1) != '#') {

                        // add to query string
                        $query .= $sql_line;

                        // if line ends with ';'
                        if (preg_match('/;\s*$/', $sql_line)) {

                            // run the query
                            $this->query($query);

                            // empties the query string
                            $query = '';

                        }

                    }

                }

                return true;

            // if file could not be opened
            } else {

                // save debug info
                $this->_log('errors', array(

                    'message'   =>  $this->language['file_could_not_be_opened'],

                ));

            }

        }

        // we don't have to report any error as _connected() method already did or checking for file returned FALSE
        return false;

    }

    /**
     *  Runs a MySQL query.
     *
     *  After a SELECT query you can get the number of returned rows by reading the {@link returned_rows} property.
     *
     *  After an UPDATE, INSERT or DELETE query you can get the number of affected rows by reading the
     *  {@link affected_rows} property.
     *
     *  <b>Note that you don't need to return the result of this method in a variable for using it later with
     *  a fetch method, like {@link fetch_assoc()} or {@link fetch_obj()} as all these methods, if called without the
     *  resource arguments, work on the LAST returned result resource!</b>
     *
     *  <code>
     *  // run a query
     *  $db->query('
     *      SELECT
     *          *
     *      FROM
     *          users
     *      WHERE
     *          gender = ?
     *  ', array($gender));
     *  </code>
     *
     *  @param  string  $sql            MySQL statement to execute.
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$sql</i>. Each item will be automatically {@link escape()}-ed and
     *                                  will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $calc_rows      (Optional) If query is a SELECT query, this argument is set to TRUE, and there is
     *                                  a LIMIT applied to the query, the value of the {@link found_rows} property (after
     *                                  the query was run) will represent the number of records that would have been
     *                                  returned if there was no LIMIT applied to the query.
     *
     *                                  This is very useful for creating pagination or computing averages. Also, note
     *                                  that this information will be available without running an extra query. Here's
     *                                  how {@link http://dev.mysql.com/doc/refman/5.0/en/information-functions.html#function_found-rows}
     *
     *                                  Default is FALSE.
     *
     *  @return mixed                   On success, returns a resource or an array (if results are taken from the cache)
     *                                  or FALSE on error.
     *
     *                                  <i>If query results are taken from cache, the returned result will be a pointer to
     *                                  the actual results of the query!</i>
     */
    function query($sql, $replacements = '', $cache = false, $highlight= false, $calc_rows = false)
    {

        // if an active connection exists
        if ($this->_connected()) {

            // remove spaces used for indentation (if any)
            $sql = preg_replace(array("/^\s+/m", "/\r\n/"), array('', ' '), $sql);

            unset($this->affected_rows);

            // if $replacements is specified but it's not an array
            if ($replacements != '' && !is_array($replacements)) {

                // save debug information
                $this->_log('unsuccessful-queries',  array(

                    'query' =>  $sql,
                    'error' =>  $this->language['warning_replacements_not_array']

                ));

            // if $replacements is specified and is an array
            } elseif ($replacements != '' && is_array($replacements)) {

                // found how many items to replace are there in the query string
                preg_match_all('/\?/', $sql, $matches, PREG_OFFSET_CAPTURE);

                // if the number of items to replace is different than the number of items specified in $replacements
                if (!empty($matches[0]) && count($matches[0]) != count($replacements)) {

                    // save debug information
                    $this->_log('unsuccessful-queries', array(

                        'query' => $sql,
                        'error' => $this->language['warning_replacements_wrong_number']

                    ));

                // if the number of items to replace is the same as the number of items specified in $replacements
                } else {

                    // make preparations for the replacement
                    $pattern1 = array();

                    $pattern2 = array();

                    // prepare parameter markers for replacement
                    foreach ($matches[0] as $match) {

                        $pattern1[] = '/\\' . $match[0] . '/';

                    }

                    foreach ($replacements as $key => $replacement) {

                        // generate a string
                        $randomstr = md5(microtime()) . $key;

                        // prepare the replacements for the parameter markers
                        $replacements1[] = $randomstr;

                        // mysql_real_escape_string the items in replacements
                        // also, replace anything that looks like $45 to \$45 or else the next preg_replace-s will treat
                        // it as references
                        $replacements2[$key] = '\'' . preg_replace('/\$([0-9]*)/', '\\\$$1', $this->escape($replacement)) . '\'';

                        // and also, prepare the new pattern to be replaced afterwards
                        $pattern2[$key] = '/' . $randomstr . '/';

                    }

                    // replace each question mark with something new
                    // (we do this intermediary step so that we can actually have question marks in the replacements)
                    $sql = preg_replace($pattern1, $replacements1, $sql, 1);

                    // perform the actual replacement
                    $sql = preg_replace($pattern2, $replacements2, $sql, 1);

                }

            }

            // $calc_rows is TRUE, we have a SELECT query and the SQL_CALC_FOUND_ROWS string is not in it
            // (we do this trick to get the numbers of records that would've been returned if there was no LIMIT applied)
            if ($calc_rows && strtolower(substr(ltrim($sql), 0, 6)) == 'select' && strpos($sql, 'SQL_CALC_FOUND_ROWS') === false) {

                // add the 'SQL_CALC_FOUND_ROWS' parameter to the query
                $sql = preg_replace('/SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $sql, 1);

            }

            unset($this->last_result);

            // starts a timer
            list($usec, $sec) = explode(' ', microtime());

            $start_timer = (float)$usec + (float)$sec;

            $refreshed_cache = 'nocache';

            // if we need to look for a cached version of the query's results
            if ($cache !== false) {

                // by default, we assume that the cache exists and is not expired
                $refreshed_cache = false;

                // if cache folder exists and is writable
                if (file_exists($this->cache_path) && is_dir($this->cache_path) && is_writable($this->cache_path)) {

                    // the cache file's name
                    $file_name = $this->cache_path . md5($sql);

                    // if a cached version of this query's result already exists and it is not expired
                    if (file_exists($file_name) && filemtime($file_name) + $cache > mktime()) {

                        // if cache file is valid
                        if ($this->cached_results[] = @unserialize(file_get_contents($file_name))) {

                            // assign to the last_result property the pointer to the position where the array was added
                            $this->last_result = count($this->cached_results) - 1;

                            // reset the pointer of the array
                            reset($this->cached_results[$this->last_result]);

                        }

                    }

                // if folder doesn't exist
                } else {

                    // save debug information
                    $this->_log('errors', array(

                        'message'   =>  $this->language['cache_path_not_writable'],

                    ), false);

                }

            }

            // if query was not read from the cache
            if (!isset($this->last_result)) {

                // run the query
                $this->last_result = @mysql_query($sql, $this->link_identifier);

                // if no test transaction, query was unsuccessful and a transaction is in progress
                if ($this->transaction_status !== 3 && !$this->last_result && $this->transaction_status !== 0) {

                    // set transaction_status to 2 so that the transaction_commit know that it has to rollback
                    $this->transaction_status = 2;

                }

            }

            // stops timer
            list($usec, $sec) = explode(' ', microtime());

            $stop_timer = (float)$usec + (float)$sec;

            // add the execution time to the total execution time
            // (we will use this in the debug console)
            $this->total_execution_time += $stop_timer - $start_timer;

            // if execution time exceeds max_query_time
            if ($stop_timer - $start_timer > $this->max_query_time) {

                // then send a notification mail
                @mail(
                    $this->notification_address,
                    sprintf($this->language['email_subject'], $this->notifier_domain),
                    sprintf($this->language['email_content'], $this->max_query_time, $stop_timer - $start_timer, $sql),
                    'From: ' . $this->notifier_domain
                );

            }

            // if the query was successfully executed
            if ($this->last_result !== false) {

                // if query's result was not read from cache (meaning $this->last_result is a result resource or boolean
                // TRUE - as queries like UPDATE, DELETE, DROP return boolean TRUE on success rather than a result resource)
                if (is_resource($this->last_result) || $this->last_result === true) {

                    // by default, consider this not to be a SELECT query
                    $is_select = false;

                    // if returned resource is a valid resource
                    if (is_resource($this->last_result)) {

                        // consider query to be a SELECT query
                        $is_select = true;

                    }

                    // reset these values for each query
                    $this->returned_rows = $this->found_rows = 0;

                    // if query was a SELECT query
                    if ($is_select) {

                        // the returned_rows property holds the number of records returned by a SELECT query
                        $this->returned_rows = $this->found_rows = @mysql_num_rows($this->last_result);

                        // if we need the number of rows that would have been returned if there was no LIMIT
                        if ($calc_rows) {

                            // get the number of records that would've been returned if there was no LIMIT
                            $found_rows = mysql_fetch_assoc(mysql_query('SELECT FOUND_ROWS()', $this->link_identifier));

                            $this->found_rows = $found_rows['FOUND_ROWS()'];

                        }

                    // if query was an action query
                    } else {

                        // the affected_rows property holds the number of affected rows by action queries
                        // (DELETE, INSERT, UPDATE)
                        $this->affected_rows = @mysql_affected_rows($this->link_identifier);

                    }

                    // if query's results need to be cached
                    if ($is_select && $cache !== false) {

                        // flag that we have refreshed the cache
                        $refreshed_cache = true;

                        $cache_data = array();

                        // iterate though the query's records
                        while ($row = mysql_fetch_assoc($this->last_result)) {

                            // and save the results in a temporary variable
                            $cache_data[] = $row;

                        }

                        // if there were any records fetched
                        if (!empty($cache_data)) {

                            // resets the internal pointer of the result resource
                            $this->seek(0, $this->last_result);

                        }

                        // if cached folder was found and is writable
                        if (isset($file_name)) {

                            // deletes (if exists) the previous cache file
                            @unlink($file_name);

                            // creates the new cache file
                            $handle = fopen($file_name, 'wb');

                            // save also the found_rows, returned_rows and columns information
                            array_push($cache_data, array(

                                'returned_rows' =>  $this->returned_rows,
                                'found_rows'    =>  $this->found_rows,
                                'column_info'   =>  $this->get_columns(),

                            ));

                            // saves the query's result in it
                            fwrite($handle, serialize($cache_data));

                            // and close the file
                            fclose($handle);

                        }

                    }

                // if query was read from cache
                } else {

                    // if read from cache this must be a SELECT query
                    $is_select = true;

                    // the last entry in the cache file contains the returned_rows, found_rows and column_info properties
                    // we need to take them off the array
                    $counts = array_pop($this->cached_results[$this->last_result]);

                    // set extract these properties from the values in the cached file
                    $this->returned_rows    = $counts['returned_rows'];
                    $this->found_rows       = $counts['found_rows'];
                    $this->column_info      = $counts['column_info'];

                }

                // if debugging is on
                if ($this->debug) {

                    $warning = '';

                    $result = array();

                    // if rows were returned
                    if ($is_select) {

                        $row_counter = 0;

                        // put the first rows, as defined by console_show_records, in an array to show them in the
                        // debug console
                        // if query was not read from cache
                        if (is_resource($this->last_result)) {

                            // iterate through the records until we displayed enough records
                            while ($row_counter++ < $this->console_show_records && $row = mysql_fetch_assoc($this->last_result)) {

                                $result[] = $row;

                            }

                            // reset the pointer in the result afterwards
                            // we have to mute error reporting because if the result set is empty (mysql_num_rows() == 0),
                            // a seek to 0 will fail with a E_WARNING!
                            @mysql_data_seek($this->last_result, 0);

                        // if query was read from the cache
                        // put the first rows, as defined by console_show_records, in an array to show them in the
                        // debug console
                        } else {

                            $result = array_slice($this->cached_results[$this->last_result], 0, $this->console_show_records);

                        }

                        // if there were queries run already
                        if (isset($this->debug_info['successful-queries'])) {

                            $keys = array();

                            // iterate through the run queries
                            // to find out if this query was already run
                            foreach ($this->debug_info['successful-queries'] as $key=>$query_data) {

                                // if this query was run before
                                if (

                                    isset($query_data['records']) &&

                                    !empty($query_data['records']) &&

                                    $query_data['records'] == $result

                                ) {

                                    // save the pointer to the query in an array
                                    $keys[] = $key;

                                }

                            }

                            // if the query was run before
                            if (!empty($keys)) {

                                // issue a warning for all the queries that were found to be the same as the current one
                                // iterate through the queries that are the same
                                foreach ($keys as $key) {

                                    // we create the variable as we will also use it later when adding the
                                    // debug information for this query
                                    $warning = sprintf($this->language['optimization_needed'], count($keys));

                                    // add the warning to the query's debug information
                                    $this->debug_info['successful-queries'][$key]['warning'] = $warning;

                                }

                            }

                        }

                        // if it's a SELECT query and query is not read from cache...
                        if ($is_select && is_resource($this->last_result)) {

                            // ask the MySQL to EXPLAIN the query
                            $explain_resource = mysql_query('EXPLAIN EXTENDED ' . $sql);

                            // if query returned a result
                            // (as some queries cannot be EXPLAIN-ed like SHOW TABLE, DESCRIBE, etc)
                            if ($explain_resource) {

                                // put all the records returned by the explain query in an array
                                while ($row = mysql_fetch_assoc($explain_resource)) {

                                    $explain[] = $row;

                                }

                            }

                        }

                    }

                    // save debug information
                    $this->_log('successful-queries', array(

                        'query'         =>  $sql,
                        'records'       =>  $result,
                        'returned_rows' =>  $this->returned_rows,
                        'explain'       =>  (isset($explain) ? $explain : ''),
                        'affected_rows' =>  (isset($this->affected_rows) ? $this->affected_rows : false),
                        'execution_time'=>  $stop_timer - $start_timer,
                        'warning'       =>  $warning,
                        'highlight'     =>  $highlight,
                        'from_cache'    =>  $refreshed_cache,
                        'transaction'   =>  ($this->transaction_status !== 0 ? true : false),

                    ), false);

                }

                // return result resource
                return $this->last_result;

            }

            // in case of error
            // save debug information
            $this->_log('unsuccessful-queries', array(

                'query'     =>  $sql,
                'error'     =>  mysql_error($this->link_identifier)

            ));

        }

        // we don't have to report any error as _connected() method already did or any of the previous checks
        return false;

    }

    /**
     *  Moves the internal row pointer of the MySQL result associated with the specified result identifier to the
     *  specified row number.
     *
     *  The next call to a fetch method, like {@link fetch_assoc()} or {@link fetch_obj()}, would return that row.
     *
     *  @param  integer     $row        The row you want to move the pointer to.
     *
     *                                  <i>$row</i> starts at 0.
     *
     *                                  <i>$row</i> should be a value in the range from 0 to {@link returned_rows}
     *
     *  @param  resource    $resource   (Optional) Resource to fetch.
     *
     *                                  <i>If not specified, the resource returned by the last run query is used.</i>
     *
     *  @since  1.1.0
     *
     *  @return boolean                 Returns TRUE on success or FALSE on failure.
     */
    function seek($row, $resource = '')
    {

        // if an active connection exists
        if ($this->_connected()) {

            // if no resource was specified, and there was a previous call to the "query" method
            if ($resource == '' && isset($this->last_result)) {

                // assign the last resource
                $resource = & $this->last_result;

            }

            // check if given resource is valid
            if (is_resource($resource)) {

                // return the fetched row
                // we have to mute error reporting because if the result set is empty (mysql_num_rows() == 0),
                // a seek to 0 will fail with a E_WARNING!
                if (@mysql_data_seek($resource, $row)) {

                    return true;

                // if error reporting was not supressed with @
                } elseif (error_reporting() != 0) {

                    // save debug information
                    $this->_log('errors', array(

                        'message'   =>  $this->language['could_not_seek'],

                    ));

                }

            // if $resource is actually a pointer to an array taken from cache
            } elseif (is_integer($resource) && isset($this->cached_results[$resource])) {

                // move the pointer to the start of the array
                reset($this->cached_results[$resource]);

                // if the pointer needs to be moved to the very first records then we don't need to do anything
                // as by resetting the array we already have that
                if ($row == 0) {

                    // simply return true
                    return true;

                // if $row > 0
                } elseif ($row > 0) {

                    // get the current info from the array and advance the pointer
                    while (list($key, $value) = each($this->cached_results[$resource])) {

                        // we check it like this because elseways we'll have the pointer moved one entry too far
                        if ($key == $row - 1) {

                            return true;

                        }

                    }

                    // save debug information
                    $this->_log('errors', array(

                        'message'   =>  $this->language['could_not_seek'],

                    ));

                }

            // if not a valid resource
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['not_a_valid_resource'],

                ));

            }

        }

        // we don't have to report any error as _connected() method already did or checking for valid resource failed
        return false;

    }

    /**
     *  Shorthand for simple SELECT queries.
     *
     *  For complex queries (using UNION, JOIN, etc) use the {@link query()} method.
     *
     *  When using this method, column names will be enclosed in grave accents " ` " (thus, allowing seamless usage of
     *  reserved words as column names) and values will be automatically escaped.
     *
     *  <code>
     *  $db->select(
     *      'column1, column2',
     *      'table',
     *      'criteria = ?',
     *      array($criteria)
     *  );
     *  </code>
     *
     *  @param  string  $columns        Any string representing valid column names as used in a SELECT statement.
     *
     *  @param  string  $table          Table in which to search.
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $limit          (Optional) A MySQL LIMIT clause (without the LIMIT keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  string  $order          (Optional) A MySQL ORDER BY clause (without the ORDER BY keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  mixed   $cache          (Optional) Instructs the script on whether it should cache the query's results
     *                                  or not. Can be either FALSE - meaning no caching - or an integer representing the
     *                                  number of seconds after which the cached results are considered to be expired
     *                                  and the query will be executed again.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @param  boolean $calc_rows      (Optional) If query is a SELECT query, this argument is set to TRUE, and there is
     *                                  a LIMIT applied to the query, the value of the {@link found_rows} property (after
     *                                  the query was run) will represent the number of records that would have been
     *                                  returned if there was no LIMIT applied to the query.
     *
     *                                  This is very useful for creating pagination or computing averages. Also, note
     *                                  that this information will be available without running an extra query. Here's
     *                                  how {@link http://dev.mysql.com/doc/refman/5.0/en/information-functions.html#function_found-rows}
     *
     *                                  Default is FALSE.
     *
     *  @since  2.0
     *
     *  @return mixed                   On success, returns a resource or an array (if results are taken from the cache)
     *                                  or FALSE on error.
     *
     *                                  <i>If query results are taken from cache, the returned result will be a pointer to
     *                                  the actual results of the query!</i>
     */
    function select($columns, $table, $where = '', $limit = '', $order = '', $replacements = '', $cache = false, $highlight = false, $calc_rows = false)
    {

        // run the query
        return $this->query('

            SELECT
                ' . $columns . '
            FROM
                ' . $table .

            ($where != '' ? ' WHERE ' . $where : '') .

            ($order != '' ? ' ORDER BY ' . $order : '') .

            ($limit != '' ? ' LIMIT ' . $limit : '')

        , $replacements, $cache, $highlight, $calc_rows);

    }

    /**
     *  Sets MySQL character set and collation.
     *
     *  The ensure that data is both properly saved and retrieved from the database you should call this method, first
     *  thing after connecting to the database.
     *
     *  If this method is not called, a warning message will be displayed in the debug console.
     *
     *  Warnings can be disabled by setting the {@link disable_warnings} property.
ÿ   PК
     *  @param  string  $charset    (Optional) The character set to be used by the database.
     *
     *                              Default is 'utf8'.
     *
     *                              For a list of possible values see:
     *                              {@link http://dev.mysql.com/doc/refman/5.1/en/charset-charsets.html}
     *
     *  @param  string  $collation  (Optional) The collation to be used by the database.
     *
     *                              Default is 'utf8_general_ci'.
     *
     *                              For a list of possible values see:
     *                              {@link http://dev.mysql.com/doc/refman/5.1/en/charset-charsets.html}
     *
     *  @since  2.0
     *
     *  @return void
     */
    function set_charset($charset = 'utf8', $collation = 'utf8_general_ci')
    {

        // do not show the warning that this method has not been called
        unset($this->warnings['charset']);

        // set MySQL character set
		$this->query('SET NAMES "' . $this->escape($charset) . '" COLLATE "' . $this->escape($collation) . '"');

    }

    /**
     *  Shows the debug console, <i>if</i> {@link debug} is TRUE and the viewer's IP address is in the
     *  {@link debugger_ip} array (or <i>$debugger_ip</i> is an empty array).
     *
     *  <i>This method must be called after all the queries in a script, preferably before </body>!</i>
     *
     *  <b>You should ALWAYS have this method called at the end of your scripts and control whether the debug console
     *  will show or not with the {@link debug} property.</b>
     *
     *  @param  boolean $return         (Optional) If set to TRUE, the output will be returned instead of being printed
     *                                  to the screen.
     *
     *                                  Default is FALSE.
     *
     *  @return void
     */
    function show_debug_console($return = false)
    {

        // if
        if (

            // debug is enabled AND
            $this->debug &&

            // debugger_ip is an array AND
            is_array($this->debugger_ip) &&

                (

                    // debugger_ip is an empty array OR
                    empty($this->debugger_ip) ||

                    // the viewer's IP is the allowed array
                    in_array($_SERVER['REMOTE_ADDR'], $this->debugger_ip)

                )

        ) {

            // if there are any warning messages iterate through them
            foreach (array_keys($this->warnings) as $warning) {

                // add them to the debug console
                $this->_log('warnings', array(

                    'message'   =>  $this->language['warning_' . $warning],

                ), false);

            }

            // blocks to be shown in the debug console
            $blocks = array(
                'errors'                =>  array(
                                                'counter'       =>  0,
                                                'identifier'    =>  'e',
                                                'generated'     =>  '',
                                            ),
                'successful-queries'    =>  array(
                                                'counter'       =>  0,
                                                'identifier'    =>  'sq',
                                                'generated'     =>  '',
                                            ),
                'unsuccessful-queries'  =>  array(
                                                'counter'       =>  0,
                                                'identifier'    =>  'uq',
                                                'generated'     =>  '',
                                            ),
                'warnings'              =>  array(
                                                'counter'       =>  0,
                                                'identifier'    =>  'w',
                                                'generated'     =>  '',
                                            ),
                'globals'               =>  array(
                                                'generated'         =>  '',
                                            ),
            );

            // there are no warnings
            $warnings = false;

            // prepare output for each block
            foreach (array_keys($blocks) as $block) {

                $output = '';

                // if there is any information for the current block
                if (isset($this->debug_info[$block])) {

                    // iterate through the error message
                    foreach ($this->debug_info[$block] as $debug_info) {

                        // increment the messages counter
                        $counter = ++$blocks[$block]['counter'];

                        $identifier = $blocks[$block]['identifier'];

                        // if block is about queries
                        if ($block == 'successful-queries' || $block == 'unsuccessful-queries') {

                            // symbols in MySQL query
                            $symbols = array(
                                '=',
                                '>',
                                '<',
                                '*',
                                '+',
                                '-',
                                ',',
                                '.',
                                '(',
                                ')',
                            );

                            // escape special characters and prepare them to be used to regular expressions
                            array_walk($symbols, create_function('&$value', '$value="/(" . quotemeta($value) . ")/";'));

                            // strings in MySQL queries
                            $strings = array(
                                "/\'([^\']*)\'/",
                                "/\"([^\"]*)\"/",
                            );

                            // keywords in MySQL queries
                            $keywords = array(
                                'ADD',
                                'ALTER',
                                'ANALYZE',
                                'BETWEEN',
                                'CHANGE',
                                'COMMIT',
                                'CREATE',
                                'DELETE',
                                'DROP',
                                'EXPLAIN',
                                'FROM',
                                'GROUP BY',
                                'HAVING',
                                'INNER JOIN',
                                'INSERT INTO',
                                'LEFT JOIN',
                                'LIMIT',
                                'ON DUPLICATE KEY',
                                'OPTIMIZE',
                                'ORDER BY',
                                'RENAME',
                                'REPAIR',
                                'REPLACE INTO',
                                'RIGHT JOIN',
                                'ROLLBACK',
                                'SELECT',
                                'SET',
                                'SHOW',
                                'START TRANSACTION',
                                'STATUS',
                                'TABLE',
                                'TABLES',
                                'TRUNCATE',
                                'UPDATE',
                                'UNION',
                                'VALUES',
                                'WHERE'
                            );

                            // escape special characters and prepare them to be used to regular expressions
                            array_walk($keywords, create_function('&$value', '$value="/(\b" . quotemeta($value) . "\b)/i";'));

                            // more keywords (these are the keywords that we don't put a line break after in the debug console
                            // when showing queries formatted and highlighted)
                            $keywords2 = array(
                                'AGAINST',
                                'ALL',
                                'AND',
                                'AS',
                                'ASC',
                                'AUTO INCREMENT',
                                'AVG',
                                'BINARY',
                                'BOOLEAN',
                                'BOTH',
                                'CASE',
                                'COLLATE',
                                'COUNT',
                                'DESC',
                                'DOUBLE',
                                'ELSE',
                                'END',
                                'ENUM',
                                'FIND_IN_SET',
                                'IN',
                                'INT',
                                'IS',
                                'KEY',
                                'LIKE',
                                'MATCH',
                                'MAX',
                                'MIN',
                                'MODE',
                                'NAMES',
                                'NOT',
                                'NULL',
                                'ON',
                                'OR',
                                'SQL_CALC_FOUND_ROWS',
                                'SUM',
                                'TEXT',
                                'THEN',
                                'TO',
                                'VARCHAR',
                                'WHEN',
                                'XOR',
                            );

                            // escape special characters and prepare them to be used to regular expressions
                            array_walk($keywords2, create_function('&$value', '$value="/(\b" . quotemeta($value) . "\b)/i";'));

                            $query_strings = array();

                            // if there are any strings in the query, store the offset where they start and the actual string
                            // in the $matches var
                            if (preg_match_all(

                                '/(\'|\"|\`)([^\1\\\]*?(?:\\\.[^\1\\\]*?)*)\\1/',

                                $debug_info['query'],

                                $matches,

                                PREG_OFFSET_CAPTURE

                            ) > 0) {

                                // reverse the order in which strings will be replaced so that we replace strings starting with
                                // the last one or else we scramble up the offsets...
                                $matches[2] = array_reverse($matches[2], true);

                                // iterate through the strings
                                foreach ($matches[2] as $match) {

                                    // save the strings
                                    $query_strings['/' . md5($match[0]) . '/'] = preg_replace('/\$([0-9]*)/', '\\\$$1', $match[0]);

                                    // replace strings with their md5 hashed equivalent
                                    // (we do this because we don't have to highlight anything in strings)
                                    $debug_info['query'] = substr_replace(

                                        $debug_info['query'],

                                        md5($match[0]),

                                        $match[1],

                                        strlen($match[0])

                                    );

                                }

                            }

                            // highlight symbols
                            $debug_info['query'] =

                                preg_replace($symbols, htmlentities('<span class="symbol">$1</span>'), $debug_info['query']);

                            // highlight strings
                            $replacement = htmlentities('<span class="string">\'$1\'</span>');

                            $debug_info['query'] = preg_replace($strings, $replacement, $debug_info['query']);

                            // highlight keywords
                            $debug_info['query'] =

                                preg_replace(

                                    $keywords,

                                    htmlentities('<br><span class="keyword">$1</span><br><span class="indent"></span>'),

                                    $debug_info['query']

                                );

                            // highlight more keywords
                            $debug_info['query'] =

                                preg_replace($keywords2, htmlentities('<span class="keyword">$1</span>'), $debug_info['query']);

                            // convert strings back to their original values
                            $debug_info['query'] = preg_replace(array_keys($query_strings), $query_strings, $debug_info['query']);

                        }

                        // all blocks are enclosed in tables
                        $output .= '
                            <table cellspacing="0" cellpadding="0" border="1" class="zdc-entry' .

                                // apply a class for even rows
                                ($counter % 2 == 0 ? ' even' : '') .

                                // should this query be highlighted
                                (isset($debug_info['highlight']) && $debug_info['highlight'] == 1 ? ' zdc-highlight' : '') .

                            '">
                                <tr>
                                    <td class="zdc-counter" valign="top">' . str_pad($counter, 3, '0', STR_PAD_LEFT) . '</td>
                                    <td class="zdc-data">
                        ';

                        // are there any error messages issued by the script?
                        if (isset($debug_info['message']) && trim($debug_info['message']) != '') {

                            $output .= '
                                <div class="zdc-box zdc-error">
                                    ' . $debug_info['message'] . '
                                </div>
                            ';
                        }


                        // are there any error messages issued by MySQL?
                        if (isset($debug_info['error']) && trim($debug_info['error']) != '') {

                            $output .= '
                                <div class="zdc-box zdc-error">
                                    ' . $debug_info['error'] . '
                                </div>
                            ';

                        }

                        // are there any warning messages issued by the script?
                        if (isset($debug_info['warning']) && trim($debug_info['warning']) != '') {

                            $output .= '
                                <div class="zdc-box zdc-error">' .
                                    $debug_info['warning'] . '
                                </div>
                            ';

                            // set a flag so that we show in the minimized debug console that there are warnings
                            $warnings = true;

                        }

                        // is there a query to be displayed?
                        if (isset($debug_info['query']) ) {

                            $output .= '
                                <div class="zdc-box' . (isset($debug_info['transaction']) && $debug_info['transaction'] ? ' zdc-transaction' : '') . '">' .
                                    preg_replace('/^\<br\>/', '', html_entity_decode($debug_info['query'])) . '
                                </div>
                            ';

                        }

                        // start generating the actions box
                        $output .= '
                            <div class="zdc-box zdc-actions">
                                <ul>
                        ';

                        // actions specific to successful queries
                        if ($block == 'successful-queries') {

                            // info about whether the query results were taken from cache or not
                            if ($debug_info['from_cache'] != 'nocache') {
                                $output .= '
                                    <li class="zdc-cache">
                                        <strong>' . $this->language['from_cache'] . '</strong>
                                    </li>
                                ';
                            }

                            // info about execution time
                            $output .= '
                                <li class="zdc-time">' .
                                    $this->language['execution_time'] . ': ' .
                                    $this->_fix_pow($debug_info['execution_time']) . ' ' .
                                    $this->language['miliseconds'] . ' (<strong>' .
                                    number_format(
                                        $debug_info['execution_time'] * 100 / $this->total_execution_time,
                                        2, '.', ',') . '</strong>%)
                                </li>
                            ';

                            // if not an action query
                            if ($debug_info['affected_rows'] === false) {

                                // button for reviewing returned rows
                                $output .= '
                                    <li class="zdc-records">
                                        <a href="javascript:zdc_toggle(\'zdc-records-sq' . $counter . '\')">' .
                                            $this->language['returned_rows'] . ': <strong>' . $debug_info['returned_rows'] . '</strong>
                                        </a>
                                    </li>
                                ';

                            // if action query
                            } else {

                                // info about affected rows
                                $output .= '
                                    <li class="zdc-affected">' .
                                        $this->language['affected_rows'] . ': <strong>' . $debug_info['affected_rows'] . '</strong>
                                    </li>
                                ';

                            }

                            // if EXPLAIN is available (only for SELECT queries)
                            if (is_array($debug_info['explain'])) {

                                // button for reviewing EXPLAIN results
                                $output .= '
                                    <li class="zdc-explain">
                                        <a href="javascript:zdc_toggle(\'zdc-explain-sq' . $counter . '\')">' .
                                            $this->language['explain'] . '
                                        </a>
                                    </li>
                                ';

                            }

                        }

                        // if backtrace information is available
                        if (isset($debug_info['backtrace'])) {

                            $output .= '
                                <li class="zdc-backtrace">
                                    <a href="javascript:zdc_toggle(\'zdc-backtrace-' . $identifier . $counter . '\')">' .
                                        $this->language['backtrace'] . '
                                    </a>
                                </li>
                            ';

                        }

                        // common actions (to top, close all)
                        $output .= '
                            <li class="zdc-top">
                                <a href="' . preg_replace('/\#zdc\-top$/i', '', $_SERVER['REQUEST_URI']) . '#zdc-top">' .
                                    $this->language['to_top'] . '
                                </a>
                            </li>
                            <li class="zdc-close">
                                <a href="javascript:zdc_closeAll(\'\')">' .
                                    $this->language['close_all'] . '
                                </a>
                            </li>
                        ';

                        // wrap up actions bar
                        $output .= '
                                </ul>
                                <div class="clear"></div>
                            </div>
                        ';

                        // data tables (backtrace, returned rows, explain)
                        // let's see what tables do we need to display
                        $tables = array();

                        // if query did return records
                        if (!empty($debug_info['records'])) {
                            $tables[] = 'records';
                        }

                        // if explain is available
                        if (isset($debug_info['explain']) && is_array($debug_info['explain'])) {
                            $tables[] = 'explain';
                        }

                        // if backtrace is available
                        if (isset($debug_info['backtrace'])) {
                            $tables[] = 'backtrace';
                        }

                        // let's display data
                        foreach ($tables as $table) {

                            // start generating output
                            $output .= '
                                <div id="zdc-' . $table . '-' . $identifier . $counter . '" class="zdc-box zdc-' . $table . '-table">
                                    <table cellspacing="0" cellpadding="0" border="1">
                                        <tr>
                            ';

                            // print table headers
                            foreach (array_keys($debug_info[$table][0]) as $header) {
                                $output .= '<th>' . $header . '</th>';
                            }

                            $output .= '</tr>';

                            // print table rows and columns
                            foreach ($debug_info[$table] as $index => $row) {

                                $output .= '<tr class="' . (($index + 1) % 2 == 0 ? 'even' : '') . '">';

                                foreach (array_values($row) as $column) {

                                    $output .= '<td valign="top">' . $column . '</td>';

                                }

                                $output .= '</tr>';

                            }

                            // wrap up data tables
                            $output .= '
                                    </table>
                                </div>
                            ';

                        }

                        // finish block
                        $output .= '
                                    </td>
                                </tr>
                            </table>
                        ';

                    }

                    // if anything was generated for the current block
                    if ($counter > 0) {

                        // enclose generated output in a special div
                        $blocks[$block]['generated'] = '<div id="zdc-' . $block . '">' . $output . '</div>';

                    }

                } elseif ($block == 'globals') {

                    // globals to show
                    $globals = array('POST', 'GET', 'SESSION', 'COOKIE', 'FILES', 'SERVER');

                    // start building output
                    $output = '
                        <div id="zdc-globals-submenu">
                            <ul>
                    ';

                    // iterate through the superglobals to show
                    foreach ($globals as $global) {

                        // add button to submenu
                        $output .=
                            '<li>
                                <a href="javascript:zdc_toggle(\'zdc-globals-' . strtolower($global) . '\')">$_' .
                                    $global . '
                                </a>
                            </li>
                        ';

                    }

                    // finish building the submenu
                    $output .= '
                            </ul>
                            <div class="clear"></div>
                        </div>
                    ';

                    // iterate thought the superglobals to show
                    foreach ($globals as $global) {

                        // make the superglobal available
                        global ${'_' . $global};

                        // add to the generated output
                        $output .= '
                            <table cellspacing="0" cellpadding="0" border="1" id="zdc-globals-' . strtolower($global) . '" class="zdc-entry">
                                <tr>
                                    <td class="zdc-counter" valign="top">001</td>
                                    <td class="zdc-data">
                                        <div class="zdc-box">
                                            <strong>$_' . $global . '</strong>
                                            <pre>' . htmlentities(var_export(${'_' . $global}, true)) . '</pre>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        ';

                    }

                    // enclose generated output in a special div
                    $output = '<div id="zdc-globals">' . $output . '</div>';

                    $blocks[$block]['generated'] = $output;

                }

            }

            // if there's an error, show the console
            if ($blocks['unsuccessful-queries']['counter'] > 0 || $blocks['errors']['counter'] > 0) {

                $this->minimize_console = false;

            }

            // finalize output by enclosing the debug console's menu and generated blocks in a container
            $output =  '
                <div id="zdc" style="display:' . ($this->minimize_console ? 'none' : 'block') . '">
                    <a name="zdc-top"></a>
                    <ul class="zdc-main">
            ';

            // are there any error messages?
            if ($blocks['errors']['counter'] > 0) {

                // button for reviewing errors
                $output .= '
                    <li>
                        <a href="javascript:zdc_toggle(\'zdc-errors\')">' .
                            $this->language['errors'] . ': <span>' . $blocks['errors']['counter'] . '</span>
                        </a>
                    </li>
                ';

            }

            // common buttons
            $output .= '
                <li>
                    <a href="javascript:zdc_toggle(\'zdc-successful-queries\')">' .
                        $this->language['successful_queries'] . ': <span>' . $blocks['successful-queries']['counter'] . '</span>&nbsp;(' .
                        $this->_fix_pow($this->total_execution_time) . ' ' . $this->language['miliseconds'] . ')
                    </a>
                </li>
                <li>
                    <a href="javascript:zdc_toggle(\'zdc-unsuccessful-queries\')">' .
                        $this->language['unsuccessful_queries'] . ': <span>' . $blocks['unsuccessful-queries']['counter'] . '</span>
                    </a>
                </li>
            ';

            if (isset($this->debug_info['warnings'])) {

                $output .= '
                    <li>
                        <a href="javascript:zdc_toggle(\'zdc-warnings\')">' .
                            $this->language['warnings'] . ': <span>' . count($this->warnings) . '</span>
                        </a>
                    </li>
                ';

            }

            $output .= '
                <li>
                    <a href="javascript:zdc_toggle(\'zdc-globals-submenu\')">' .
                        $this->language['globals'] . '
                    </a>
                </li>
            ';

            // wrap up debug console's menu
            $output .= '
                </ul>
                <div class="clear"></div>
            ';

            foreach (array_keys($blocks) as $block) {

                $output .= $blocks[$block]['generated'];

            }

            // wrap up
            $output .= '</div>';

            // add the minified version of the debug console
            $output .= '
                <div id="zdc-mini">
                    <a href="javascript:zdc_toggle(\'console\')">' .
                        $blocks['successful-queries']['counter'] . ($warnings ? '<span>!</span>' : '') . ' / ' . $blocks['unsuccessful-queries']['counter'] . '
                    </a>
                </div>
            ';

            // tidy the output
            $pattern = array(

                // remove blank lines
                "/[\r\n]+\s*[\r\n]+/",

                // remove spaces used for indentation
                "/^\s+/m",

            );

            $replacement = array(

                "\r\n",
                "",

            );

            // perform the tidying
            $output = preg_replace($pattern, $replacement, $output);

            // get the URL to the class
            // server protocol (http, https)
            preg_match('/(.*?)\//', $_SERVER['SERVER_PROTOCOL'], $matches);

            $protocol = strtolower($matches[1]);

            // this is the url that will be used for automatically including
            // the CSS and the JavaScript files
            $path = rtrim(preg_replace('/\\\/', '/', $protocol . '://' . $_SERVER['SERVER_NAME'] . DIRECTORY_SEPARATOR . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']))), '/');

            // link the required javascript
            $output = '<script type="text/javascript" src="' . $path . '/public/javascript/database.js"></script>' . $output;

            // link the required css file
            $output = '<link rel="stylesheet" href="' . $path . '/public/css/database.css" type="text/css">' . $output;

            // if output is to be returned rather than printed to the screen
            if ($return) return $output;

            // show generated output
            echo $output;

        }

    }

    /**
     *  Ends a transaction which means that if all the queries since {@link transaction_start()} are valid, it writes
     *  the data to the database, but if any of the queries had an error, ignore all queries and treat them as if they
     *  never happened.
     *
     *  <code>
     *  // start transactions
     *  $db->transaction_start();
     *
     *  // run queries
     *
     *  // if all the queries since "transaction_start" are valid, write data to the database;
     *  // if any of the queries had an error, ignore all queries and treat them as if they never happened
     *  $db->transaction_complete();
     *  </code>
     *
     *  @since  2.1
     *
     *  @return boolean                     Returns TRUE on success or FALSE on error.
     */
    function transaction_complete()
    {

        $sql = 'COMMIT';

        // if a transaction is in progress
        if ($this->transaction_status !== 0) {

            // if this was a test transaction or there was an error with one of the queries in the transaction
            if ($this->transaction_status === 3 || $this->transaction_status === 2) {

                // rollback changes
                $this->query('ROLLBACK');

                // set flag so that the query method will know that no transaction is in progress
                $this->transaction_status = 0;

                // if it was a test transaction return TRUE or FALSE otherwise
                return ($this->transaction_status === 3 ? true : false);

            }

            // if all queries in the transaction were executed successfully and this was not a test transaction

            // commit transaction
            $this->query($sql);

            // set flag so that the query method will know that no transaction is in progress
            $this->transaction_status = 0;

            // if query was successful
            if ($this->last_result) return true;

            // if query was unsuccessful
            return false;

        }

        // if no transaction was in progress
        // save debug information
        $this->_log('unsuccessful-queries', array(

            'query' =>  $sql,
            'error' =>  $this->language['no_transaction_in_progress'],

        ), false);

        return false;

    }

    /**
     *  Starts the transaction system.
     *
     *  Transactions work only with databases that support transaction-safe table types. In MySQL, these are InnoDB or
     *  BDB table types. Working with MyISAM tables will not raise any errors but statements will be executed
     *  automatically as soon as they are called (just like if there was no transaction).
     *
     *  If you are not familiar with transactions, have a look at {@link http://dev.mysql.com/doc/refman/5.0/en/commit.html}
     *  and try to find a good online resource for more specific information.
     *
     *  <code>
     *  // start transactions
     *  $db->transaction_start();
     *
     *  // run queries
     *
     *  // if all the queries since "transaction_start" are valid, write data to database;
     *  // if any of the queries had an error, ignore all queries and treat them as if they never happened
     *  $db->transaction_complete();
     *  </code>
     *
     *  @param  boolean     $test_only      (Optional) Starts the transaction system in "test mode", causing the queries
     *                                      to be rolled back (when {@link transaction_complete()} is called ) - even if
     *                                      all queries are valid.
     *
     *                                      Default is FALSE.
     *
     *  @since  2.1
     *
     *  @return boolean                     Returns TRUE on success or FALSE on error.
     */
    function transaction_start($test_only = false)
    {

        $sql = 'START TRANSACTION';

        // if a transaction is not in progress
        if ($this->transaction_status === 0) {

            // set flag so that the query method will know that a transaction is in progress
            $this->transaction_status = ($test_only ? 3 : 1);

            // try to start transaction
            $this->query($sql);

            // returns TRUE, if query was executed successfully
            if ($this->last_result) return true;

            return false;

        }

        // save debug information
        $this->_log('unsuccessful-queries', array(

            'query' =>  $sql,
            'error' =>  $this->language['transaction_in_progress'],

        ), false);

        return false;

    }

    /**
     *  Checks whether a table exists in the current database.
     *
     *  <code>
     *  // checks whether table "users" exists
     *  table_exists('users');
     *  </code>
     *
     *  @param  string  $table      The name of the table to check if it exists in the database.
     *
     *  @since  2.3
     *
     *  @return boolean             Returns TRUE if table given as argument exists in the database or FALSE if not.
     *
     *
     */
    function table_exists($table)
    {

        // check if table exists in the database
        return $this->fetch_assoc($this->query('SHOW TABLES LIKE ?', array($table))) !== false ? true : false;

    }

    /**
     *  Shorthand for truncating tables.
     *
     *  <i>Truncating a table is quicker then deleting all rows, as stated in the MySQL documentation at
     *  {@link http://dev.mysql.com/doc/refman/4.1/en/truncate.html}. Truncating a table also resets the value of the
     *  AUTO INCREMENT column.</i>
     *
     *  <code>
     *  $db->truncate('table');
     *  </code>
     *
     *  @param  string  $table          Table to truncate.
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @since  1.0.9
     *
     *  @return boolean                 Returns TRUE on success of FALSE on error.
     */
    function truncate($table, $highlight = false)
    {

        // run the query
        $this->query('

            TRUNCATE
                ' . $table

        , '', false, $highlight);

        // returns TRUE, if query was executed successfully
        if ($this->last_result) return true;

        return false;

    }

    /**
     *  Shorthand for UPDATE queries.
     *
     *  When using this method, column names will be enclosed in grave accents " ` " (thus, allowing seamless usage of
     *  reserved words as column names) and values will be automatically escaped.
     *
     *  After an update, see {@link affected_rows} to find out how many rows were affected.
     *
     *  <code>
     *  $db->update(
     *      'table',
     *      array(
     *          'column1'   =>  'value1',
     *          'column2'   =>  'value2',
     *      ),
     *      'criteria = ?',
     *      array($criteria)
     *  );
     *  </code>
     *
     *  @param  string  $table          Table in which to update.
     *
     *  @param  array   $columns        An associative array where the array's keys represent the columns names and the
     *                                  array's values represent the values to be inserted in each respective column.
     *
     *                                  Column names will be enclosed in grave accents " ` " (thus, allowing seamless
     *                                  usage of reserved words as column names) and values will be automatically
     *                                  {@link escape()}d.
     *
     *                                  A special value may also be used for when a column's value needs to be
     *                                  incremented or decremented. In this case, use <i>INC(value)</i> where <i>value</i>
     *                                  is the value to increase the column's value with. Use <i>INC(-value)</i> to decrease
     *                                  the column's value:
     *
     *                                  <code>
     *                                  $db->update(
     *                                      'table',
     *                                      array(
     *                                          'column'    =>  'INC(?)',
     *                                      ),
     *                                      'criteria = ?',
     *                                      array(
     *                                          $value,
     *                                          $criteria
     *                                      )
     *                                  );
     *                                  </code>
     *
     *                                  ...is equivalent to
     *
     *                                  <code>
     *                                  $db->query('UPDATE table SET column = colum + ? WHERE criteria = ?', array($value, $criteria));
     *                                  </code>
     *
     *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers ("?", question
     *                                  marks) in <i>$column</i>, <i>$table</i> and <i>$where</i>. Each item will be
     *                                  automatically {@link escape()}-ed and will replace the corresponding "?".
     *
     *                                  Default is "" (an empty string).
     *
     *  @param  boolean $highlight      (Optional) If set to TRUE, the debug console will open automatically and will
     *                                  show the query.
     *
     *                                  Default is FALSE.
     *
     *  @since  1.0.9
     *
     *  @return boolean                 Returns TRUE on success of FALSE on error
     */
    function update($table, $columns, $where = '', $replacements = '', $highlight = false)
    {

        // if $replacements is specified but it's not an array
        if ($replacements != '' && !is_array($replacements)) {

            // save debug information
            $this->_log('unsuccessful-queries',  array(

                'query' =>  '',
                'error' =>  $this->language['warning_replacements_not_array']

            ));

            return false;

        }

        $cols = '';

        // start creating the SQL string and enclose field names in `
        foreach ($columns as $column_name => $value) {

            // if special INC() keyword is used
            if (preg_match('/INC\((\-{1})?(.*?)\)/i', $value, $matches) > 0) {

                $cols .= ($cols != '' ? ', ' : '') . '`' . $column_name . '` = `' . $column_name . '` ' . ($matches[1] == '-' ? '-' : '+') . ' ?';

                $columns[$column_name] = $matches[2];

            // the usual way
            } else {

                $cols .= ($cols != '' ? ', ' : '') . '`' . $column_name . '` = ?';

            }

        }

        // run the query
        $this->query('

            UPDATE
                ' . $table . '
            SET
                ' . $cols .

            ($where != '' ? ' WHERE ' . $where : '')

        , array_merge(array_values($columns), $replacements == '' ? array() : $replacements), false, $highlight);

        // returns TRUE if query was executed successfully
        if ($this->last_result) return true;

        return false;

    }

    /**
     *  Writes debug information to a <i>log.txt</i> log file at {@link log_path} <i>if</i> {@link debug} is TRUE and the
     *  viewer's IP address is in the {@link debugger_ip} array (or <i>$debugger_ip</i> is an empty array).
     *
     *  <i>This method must be called after all the queries in a script!</i>
     *
     *  <i>Make sure you're calling it BEFORE {@link show_debug_console()} so that you can see in the debug console if
     *  writing to the log file was successful or not.</i>
     *
     *  @since  1.1.0
     *
     *  @return void
     */
    function write_log()
    {

          // if
        if (

            // debug is enabled AND
            $this->debug &&

            // debugger_ip is an array AND
            is_array($this->debugger_ip) &&

                (

                    // debugger_ip is an empty array OR
                    empty($this->debugger_ip) ||

                    // the viewer's IP is the allowed array
                    in_array($_SERVER['REMOTE_ADDR'], $this->debugger_ip)

                )

        ) {

            // tries to create/open the 'log.txt' file
            if ($handle = @fopen((rtrim($this->log_path, '/') != '' ? '/' : '') . 'log.txt', 'ab')) {

                // iterate through the debug information
                foreach ($this->debug_info['successful-queries'] as $debug_info) {

                    // the following regular expressions strips newlines and indenting from the MySQL string, so that
                    // we have it in a single line
                    $pattern = array(
                        "/\s*(.*)\n|\r/",
                        "/\n|\r/"
                    );
                    $replace = array(
                        ' $1',
                        ' '
                    );

                    // write to log file
                    fwrite($handle, print_r(

                        '###################' . "\n" .
                        '# DATE:           #: ' . date('Y M d H:i:s') . "\n" .
                        '# QUERY:          #: ' . trim(preg_replace($pattern, $replace, $debug_info['query'])) . "\n" .

                        // if execution time is available
                        // (is not available for unsuccessful queries)
                        (isset($debug_info['execution_time']) ?

                            '# ' . strtoupper($this->language['execution_time']) . ': #: ' .  $this->_fix_pow($debug_info['execution_time']) . ' ' . $this->language['miliseconds'] . "\n"
                             : ''

                        ) .

                        // if there is a warning message
                        (isset($debug_info['warning']) && $debug_info['warning'] != '' ?

                            '# WARNING:        #: ' . strip_tags($debug_info['warning']) . "\n"
                            : ''

                        ) .

                        // if there is an error message
                        (isset($debug_info['error']) && $debug_info['error'] != '' ?

                            '# ERROR:          #: ' . $debug_info['error'] . "\n"
                            : ''

                        ) .

                        // if not an action query, show whether the query was returned from the cache or was executed
                        ($debug_info['affected_rows'] === false ?

                            '# FROM CACHE:     #: ' .

                            (isset($debug_info['from_cache']) && $debug_info['from_cache'] === true  ?

                                'YES' :
                                'NO'

                            ) . "\n"

                            : ''

                        ) .

                        '# BACKTRACE:      #:' . "\n"

                    , true));

                    // write full backtrace info
                    foreach ($debug_info['backtrace'] as $backtrace) {

                        fwrite($handle, print_r(

                            '#                 #' . "\n" .
                            '# FILE            #: ' . $backtrace['file'] . "\n" .
                            '# LINE            #: ' . $backtrace['line'] . "\n" .
                            '# FUNCTION        #: ' . $backtrace['function'] . "\n"

                        , true));

                    }

                    // finish writing to the log file
                    fwrite($handle, '###################' . "\n\n");

                }

                // close log file
                fclose($handle);

            // if log file could not be created/opened
            } else {

                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['could_not_write_to_log'],

                ));

            }

        }

    }

    /**
     *  Checks if the connection to the MySQL server has been previously established by the connect() method.
     *
     *  @access private
     */
    function _connected()
    {

        // if there's no connection to a MySQL database
        if (!$this->link_identifier) {

            // tries to connect to the MySQL database
            if (!($this->link_identifier = @mysql_connect(
                $this->credentials['host'],
                $this->credentials['user'],
                $this->credentials['password'],
                $this->credentials['is_new']
            ))) {

                // if connection could not be established
                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['could_not_connect_to_database'],
                    'error'     =>  mysql_error(),

                ));

                // return FALSE
                return false;

            }

            // if connection could be established
            // select the database
            if (!($this->database = @mysql_select_db($this->credentials['database'], $this->link_identifier))) {

                // if database could not be selected
                // save debug information
                $this->_log('errors', array(

                    'message'   =>  $this->language['could_not_select_database'],
                    'error'     =>  mysql_error($this->link_identifier),

                ));

                // return FALSE
                return false;

            }

        }

        // return TRUE if there is no error
        return true;

    }

    /**
     *  PHP's microtime() will return elapsed time as something like 9.79900360107E-5 when the elapsed time is too short.
     *
     *  This function takes care of that and returns the number in the human readable format.
     *
     *  @access private
     */
    function _fix_pow($value)
    {

        // use value as literal
        $value = (string)$value;

        // if the power is present in the value
        if (preg_match('/E\-([0-9]+)$/', $value, $matches) > 0) {

            // convert to human readable format
            $value = '0.' . str_repeat('0', $matches[1] - 1) . preg_replace('/\./', '', substr($value, 0, -strlen($matches[0])));

        }

        // return the value
        return number_format($value * 1000, 3);

    }

    /**
     *  Handles saving of debug information and halts the execution of the script on fatal error or if the
     *  {@link halt_on_errors} property is set to TRUE
     *
     *  @access private
     */
    function _log($category, $data, $fatal = true)
    {

        // if debugging is on
        if ($this->debug) {

            // if category is different than "warnings"
            // (warnings are generated internally)
            if ($category != 'warnings') {

                // get backtrace information
                $backtrace_data = debug_backtrace();

                // unset first entry as it refers to the call to this particular method
                unset($backtrace_data[0]);

                $data['backtrace'] = array();

                // iterate through the backtrace information
                foreach ($backtrace_data as $backtrace) {

                    // extract needed information
                    $data['backtrace'][] = array(

                        $this->language['file']     =>  (isset($backtrace['file']) ? $backtrace['file'] : ''),
                        $this->language['function'] =>  $backtrace['function'] . '()',
                        $this->language['line']     =>  (isset($backtrace['line']) ? $backtrace['line'] : ''),

                    );

                }

            }

            // saves debug information
            $this->debug_info[$category][] = $data;

            // if the saved debug info is about a fatal error
            // and execution is to be stopped on fatal errors
            if ($fatal && $this->halt_on_errors) {

                // show the debugging window
                $this->show_debug_console();

                // halt execution
                die();

            }

        }

    }

}

?>
