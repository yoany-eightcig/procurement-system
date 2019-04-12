<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PDO;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $locate_base_url;
   	public $locate_username;
   	public $locate_password;
   	
    public $sessionToken = null;

    public function __construct() 
    {
        $this->locate_base_url = env('LOCATE_BASE_URL', '');
        $this->locate_username = env('LOCATE_USERNAME', '');
        $this->locate_password = env('LOCATE_PASSWORD', '');

    	// Login
    	$loginRequest = array(
    	    'email' => $this->locate_username,
    	    'password' => $this->locate_password,
    	);

    	$loginResponse = $this->locateRequest('POST', '/login', null, $loginRequest);
    	$this->sessionToken = $loginResponse->session_token;

    	return;
    }

    public function locateRequest($curlRequestType, $endpoint, $sessionToken = null, $postData = null) {

        // Create CURL Request
        $curlRequest = curl_init();

        // Set CURL Options
        curl_setopt($curlRequest, CURLOPT_CUSTOMREQUEST, $curlRequestType);
        curl_setopt($curlRequest, CURLOPT_URL, $this->locate_base_url . $endpoint);
        curl_setopt($curlRequest, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);

        // Check for POST Data
        if($postData !== null) {
            curl_setopt($curlRequest, CURLOPT_POSTFIELDS, json_encode($postData));
        }

        // BASIC Auth
        if($sessionToken !== null) {
            curl_setopt($curlRequest, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curlRequest, CURLOPT_USERPWD, $sessionToken);
        }
        // Execute CURL Request
        $response = curl_exec($curlRequest);	
        $httpCode = curl_getinfo($curlRequest, CURLINFO_HTTP_CODE);

        // Check HTTP Status Code
        if($httpCode == 200 || $httpCode == 201) {
        	$json_object = json_decode($response);
        	if ($json_object) {
        		return $json_object;
        	} else {
				return $response;        		
        	}
        }
        else {
            //throw new Exception($httpCode . ' - ' . $response);
            dd($response);
        }
    }


}

class SSP {
    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    static function data_output ( $columns, $data )
    {
        $out = array();

        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();

            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                $column = $columns[$j];

                // Is there a formatter?
                if ( isset( $column['formatter'] ) ) {
                    $row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                }
                else {
                    $row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
                }
            }

            $out[] = $row;
        }

        return $out;
    }


    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit ( $request, $columns )
    {
        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {
            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    static function order ( $request, $columns )
    {
        $order = '';

        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = SSP::pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    $orderBy[] = '`'.$column['db'].'` '.$dir;
                }
            }

            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    static function filter ( $request, $columns, &$bindings )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = SSP::pluck( $columns, 'dt' );

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {
                    $binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $globalSearch[] = "`".$column['db']."` LIKE ".$binding;
                }
            }
        }

        // Individual column filtering
        for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search( $requestColumn['data'], $dtColumns );
            $column = $columns[ $columnIdx ];

            $str = $requestColumn['search']['value'];

            if ( $requestColumn['searchable'] == 'true' &&
             $str != '' ) {
                $binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                $columnSearch[] = "`".$column['db']."` LIKE ".$binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }


    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $sql_details SQL connection details - see sql_connect()
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    static function simple ( $request, $sql_details, $table, $primaryKey, $columns, $report_id = 0)
    {
        $bindings = array();
        $db = SSP::sql_connect( $sql_details );

        // Build the SQL query string from the request
        $limit = SSP::limit( $request, $columns );
        $order = SSP::order( $request, $columns );
        $where = SSP::filter( $request, $columns, $bindings );

        // dd("SELECT SQL_CALC_FOUND_ROWS `", implode("`, `", SSP::pluck($columns, 'db')) );

        $reportQuery = "";
        if ($report_id == 1) {
            $reportQuery = "WHERE ((quantity+unissued+on_order) <= ave)";
        } else if ($report_id == 2) {
            $reportQuery = "WHERE ((quantity+unissued+on_order) <= (ave/4))";
        } else if ($report_id == 3) {
            $reportQuery = "WHERE (quantity >= ave*3)";
        } else if ($report_id == 4) {
            $reportQuery = "WHERE (quantity >= ave*6)";
        } else if ($report_id == 5) {
            $reportQuery = "WHERE (ave = 0)";
        }

        if (!empty($where) && !empty($reportQuery)) {
            // $reportQuery = $reportQuery;
            $where = str_replace("WHERE", "AND", $where);
        }

        // Main query to actually get the data
        $data = SSP::sql_exec( $db, $bindings,
            "SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", SSP::pluck($columns, 'db'))."` FROM `parts` $reportQuery $where $order $limit"
        );

        // Data set length after filtering
        $resFilterLength = SSP::sql_exec( $db,
            "SELECT FOUND_ROWS()"
        );
        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        $resTotalLength = SSP::sql_exec( $db,
            "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table`"
        );
        $recordsTotal = $resTotalLength[0][0];


        /*
         * Output
         */
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => SSP::data_output( $columns, $data )
        );
    }


    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    static function sql_connect ( $sql_details )
    {
        try {
            $db = @new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
                $sql_details['user'],
                $sql_details['pass'],
                array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
            );
        }
        catch (PDOException $e) {
            SSP::fatal(
                "An error occurred while connecting to the database. ".
                "The error reported by the server was: ".$e->getMessage()
            );
        }

        return $db;
    }


    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    static function sql_exec ( $db, $bindings, $sql=null )
    {
        // Argument shifting
        if ( $sql === null ) {
            $sql = $bindings;
        }

        $stmt = $db->prepare( $sql );
        //echo $sql;

        // Bind parameters
        if ( is_array( $bindings ) ) {
            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
                $binding = $bindings[$i];
                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
            }
        }

        // Execute
        try {
            $stmt->execute();
        }
        catch (PDOException $e) {
            SSP::fatal( "An SQL error occurred: ".$e->getMessage() );
        }

        // Return all
        return $stmt->fetchAll();
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal ( $msg )
    {
        echo json_encode( array( 
            "error" => $msg
        ) );

        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind ( &$a, $val, $type )
    {
        $key = ':binding_'.count( $a );

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }


    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    static function pluck ( $a, $prop )
    {
        $out = array();

        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }
}

