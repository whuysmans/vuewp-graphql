<?php
/**
* Plugin Name: WordPress Simplified GraphQL
* Plugin URI: http://www.conimpeto.be/
* Description: GraphQL for WordPress, forked from Tim Field and simplified for Con Impeto
* Version: 0.3.0
* Author: Werner Huysmans
* Author URI: http://conimpeto.be/
* License: GPL-3
*/
namespace CI\GraphQLWP;

require_once __DIR__ . "/Lib/autoload.php";

use GraphQL\GraphQL;
use Mohiohio\WordPress\Router;
use CI\GraphQLWP\Schema;

const ENDPOINT = '/graphql/';

Router::routes([

    ENDPOINT => function() {

        header('Access-Control-Allow-Origin: *');
        //CI: allowed type access-control-allow-origin added, to prevent cors errors (see also 'credentials false' on lokka client)
        header('Access-Control-Allow-Headers: content-type, Access-Control-Allow-Origin');
        header('Content-Type: application/json');


        $contentTypeIsJson = (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json')
            ||  (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json');

        log('contentTypeIsJson', $contentTypeIsJson);

        if ($contentTypeIsJson) {
            $rawBody = file_get_contents('php://input');

            try {
              $data = json_decode($rawBody, true);
            } catch (\Exception $exception) {
              jsonResponse(['errors' => ['message' => 'Decoding body failed. Be sure to send valid json request.']]);
            }

            // Decoded response is still empty
            if (strlen($rawBody) > 0 && null === $data) {
                jsonResponse(['errors' => ['message' => 'Decoding body failed. Be sure to send valid json request. Check for line feeds in json (replace them with "\n" or remove them)']]);
            }
        } else {
            $data = $_POST;
        }

        $requestString = isset($data['query']) ? $data['query'] : null;
        $operationName = isset($data['operation']) ? $data['operation'] : null;
        $variableValues = isset($data['variables']) ?
            ( is_array($data['variables']) ?
                $data['variables'] :
                json_decode($data['variables'],true) ) :
            null;

        if($requestString) {
            try {
                // Define your schema:
                $schema = Schema::build();
                $result = GraphQL::execute(
                    $schema,
                    $requestString,
                    /* $rootValue */ null,
                    $variableValues,
                    $operationName
                );
            } catch (\Exception $exception) {
                $result = [
                    'errors' => [
                        ['message' => $exception->getMessage()]
                    ]
                ];
            }
            //write_log(json_encode($result));
            jsonResponse($result);
        }
        jsonResponse(['errors' => ['message' => 'Wrong query format or empty query. Either send raw query _with_ Content-Type: \'application/json\' header or send query by posting www-form-data with a query="query{}..." parameter']]);
    }
]);

function write_log ( $log ) {
    if ( true === WP_DEBUG ) {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}

/**
 * Sends a json object to the client
 * @param  array  $resp response object
 * @return [type]       [description]
 */
function jsonResponse(array $resp) {
  try {
    $jsonResponse = json_encode($resp);
  } catch(\Exception $exception) {
    jsonResponse(['errors' => ['message' => 'Failed to encode to JSON the response.']]);
  }

  echo $jsonResponse;
  exit;
}

/**
 * Log a message to the SAPi (terminal) (only when WP_DEBUG is set to true)
 * @param  string $message The message to log to terminal
 * @return [type]          [description]
 */
function log($message)  {
    if (!WP_DEBUG) {
      return;
    }
    $function_args = func_get_args();
    // The first is a simple string message, the others should be var_exported
    array_shift($function_args);

    foreach($function_args as $argument) {
        $message .= ' ' . var_export($argument, true);
    }

    // send to sapi
    error_log($message, 4);

}
