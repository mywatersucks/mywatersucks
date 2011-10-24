<?php

    /**
    * English language file for the Zebra_Database class, by Stefan Gabos <contact@stefangabos.ro>.
    *
    * @version      1.0
    * @author       Stefan Gabos <contact@stefangabos.ro>
    *
    */

    $this->language = array(

        'affected_rows'                     => 'affected rows',
        'backtrace'                         => 'backtrace',
        'cache_path_not_writable'           => 'Could not cache query. Make sure path exists and is writable.',
        'close_all'                         => 'close all',
        'could_not_connect_to_database'     => 'Could not connect to database',
        'could_not_seek'                    => 'could not seek to specified row',
        'could_not_select_database'         => 'Could not select database',
        'could_not_write_to_log'            => 'Could not write to log file. Make sure the folder exists and is writable.',
        'email_subject'                     => 'Slow query on %s!',
        'email_content'                     => "The following query exceeded normal running time of %s seconds by running %s seconds: \n\n %s",
        'errors'                            => 'errors',
        'execution_time'                    => 'execution time',
        'explain'                           => 'explain',
        'data_not_an_array'                 => 'The third argument of <em>insert_bulk()</em> needs to be an array of arrays.',
        'file'                              => 'file',
        'file_could_not_be_opened'          => 'Could not open file',
        'from_cache'                        => 'from cache',
        'function'                          => 'function',
        'globals'                           => 'globals',
        'line'                              => 'line',
        'miliseconds'                       => 'ms',
        'mysql_error'                       => 'MySQL error',
        'no_transaction_in_progress'        => 'No transaction in progress.',
        'not_a_valid_resource'              => 'Not a valid resource (make sure you specify a resource as argument for fetch_assoc()/fetch_obj() if you are executing a query inside the loop)',
        'optimization_needed'               => '<strong>WARNING</strong>: The first few results returned by this query are the same as returned by <strong>%s</strong> other queries!',
        'returned_rows'                     => 'returned rows',
        'successful_queries'                => 'successful queries',
        'to_top'                            => 'to the top',
        'transaction_in_progress'           => 'Transaction could not be started as another transaction is in progress.',
        'unsuccessful_queries'              => 'unsuccessful queries',
        'warning_charset'                   => 'No default charset and collections were set. Call set_charset() after connecting to the database.',
        'warning_replacements_not_array'    => '<em>$replacements</em> must be an arrays of values',
        'warning_replacements_wrong_number' => 'the number of items to replace is different than the number of items in the <em>$replacements</em> array',
        'warning_wait_timeout'              => 'The value of MySQL\'s <em>wait_timeout</em> variable is set to %s. The <em>wait_timeout</em> variable represents the time, in seconds, that MySQL will wait before killing an idle connection. After a script finishes execution, the MySQL connection is not actually terminated but it is put in an idle state and is being reused if the same user requires a database connection (a very common scenario is when users navigate through the pages of a website). The default value of <em>wait_timeout</em> is 28800 seconds, or 8 hours. If you have lots of visitors this can lead to a <em><a href="http://dev.mysql.com/doc/refman/5.5/en/too-many-connections.html" target="_blank">Too many connections</a></em> error, as eventualy there will be times when no <a href="http://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_max_connections" target="_blank">free connections</a> will be available. The recommended value is 300 seconds (5 minutes).',
        'warnings'                          => 'warnings',

    );

?>
