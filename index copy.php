<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','roi');






require_once realpath("vendor/autoload.php");

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// This middleware will append the response header Access-Control-Allow-Methods with all allowed methods
$app->add(function (Request $request, RequestHandlerInterface $handler): Response {
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

    $response = $handler->handle($request);

    $response = $response->withHeader('Access-Control-Allow-Origin', '*');
    $response = $response->withHeader('Access-Control-Allow-Headers', '*');
    // $response = $response->withHeader("Access-Control-Allow-Headers": "X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
    // $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    // $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

    // Optional: Allow Ajax CORS requests with Authorization header
    // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

    return $response;
});
/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

/**
 * Company routes
 *  */ 
/**
 * Entry
 */
$app->post('/company', function (Request $request,Response  $response, $args) {
    $dsn = "mysql:host=localhost;dbname=roi";
    $db = new PDO($dsn, DB_USER, DB_PASS);

    $json = $request->getBody();
    $h_token = $request->getHeaders()['token'];
    $data = json_decode($json, true);
   
    $company_name = filter_var($data['companyName'],FILTER_SANITIZE_STRING);
    $company_alias = filter_var($data['companyAlias'],FILTER_SANITIZE_STRING);
    $licenses = filter_var($data['license'],FILTER_SANITIZE_STRING);
    $active= '1';
    $structure = 1;
    $created_dt = date("Y-m-d H:i:s");
    $contract_start = filter_var($data['contractStart'],FILTER_SANITIZE_STRING);

    $contractFiles = filter_var($data['contractFiles'],FILTER_SANITIZE_STRING);
    $contract_end = filter_var($data['contractEnd'],FILTER_SANITIZE_STRING);
    $notes= filter_var($data['notes'],FILTER_SANITIZE_STRING);
    
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'roi';
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
 
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }


    $sql = "INSERT INTO `roi_companies` ( `company_name`, `company_alias`, `active`, `created_dt`,  `licenses`,   `contract_start`, `contractFiles`,`contract_end`,`notes`, `structures`) 
    VALUES ('$company_name', '$company_alias', '$active', '$created_dt', '$licenses', '$contract_start', '$contractFiles', '$contract_end', '$notes','$structure');";
    if ($mysqli->query($sql)) {
        $last_id = $mysqli->insert_id;
        $data['id'] = $last_id;
        unset($data['token_id']);
        $response->getBody()->write((string)json_encode(
            ["data"=>$data,"success"=>"true","message"=>"ok"]));
     }
     if ($mysqli->errno) {
        $response->getBody()->write((string)json_encode(
            ["data"=>[],"success"=>"false","message"=>"Could not insert record into table:  $mysqli->error"]));
     }
     $mysqli->close();
    
    return $response->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization,XMLHttpRequest')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');;
});
/**
 * Update
 */
// $app->post('/company', function (Request $request,Response  $response, $args) {
    // $app->put('/books/{id}', function ($request, $response, array $args) {
$app->put('/company/edit/{id}', function (Request $request,Response  $response, array  $args) {
    $id = $args['id'];
    $json = $request->getBody();
    $data = json_decode($json, true);
    
    $company_name = filter_var($data['companyName'],FILTER_SANITIZE_STRING);
    $company_alias = filter_var($data['companyAlias'],FILTER_SANITIZE_STRING);
    $licenses = filter_var($data['license'],FILTER_SANITIZE_STRING);
    $active= '1';
    $structure = 1;
    $created_dt = date("Y-m-d H:i:s");
    $contract_start = filter_var($data['contractStart'],FILTER_SANITIZE_STRING);

    $contractFiles = filter_var($data['contractFiles'],FILTER_SANITIZE_STRING);
    $contract_end = filter_var($data['contractEnd'],FILTER_SANITIZE_STRING);
    $notes= filter_var($data['notes'],FILTER_SANITIZE_STRING);
    
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'roi';
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }

    $sql = "update `roi_companies` set 
        `company_name`= '$company_name', 
        `company_alias` = '$company_alias', 
        `active` = '$active',  
        `licenses` = '$licenses',   
        `contract_start` = '$contract_start', 
        `contractFiles` = '$contractFiles',
        `contract_end` = '$contract_end',
        `notes` = '$notes', 
        
        `structures`= '$structure' where company_id =$id;";
    
        if ($mysqli->query($sql)) {
            // $last_id = $mysqli->insert_id;
            $data['id'] = $id;
            unset($data['token_id']);
            $response->getBody()->write((string)json_encode(
                ["data"=>$data,"success"=>"true","message"=>"ok"]));
         }
         if ($mysqli->errno) {
            $response->getBody()->write((string)json_encode(
                ["data"=>[],"success"=>"false","message"=>"Could not insert record into table:  $mysqli->error"]));
         }
         $mysqli->close();


    return $response;
});
// /**
//  * Preview
//  */
$app->get('/company/info/{id}', function (Request $request,Response  $response, $args) {
    $id = $args['id'];
    $sql = "select `company_name`, `company_alias`, `active`, `created_dt`,  `licenses`,   `contract_start`, `contractFiles`,`contract_end`,`notes`, `structures` from roi_companies where company_id =$id;";
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'roi';
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            $data = [
                "company_name"=>$obj->company_name,
                "company_alias"=>$obj->company_alias,
                "active"=>$obj->active,
                "created_dt"=>$obj->created_dt,
                "licenses"=>$obj->licenses,
                "contract_start"=>$obj->contract_start,
                "contractFiles"=>$obj->contractFiles,
                "contract_end"=>$obj->contract_end,
                "notes"=>$obj->notes,
                "structures"=>$obj->structures,
            ];
        }
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>"true","message"=>"ok"]));
     }
     if ($mysqli->errno) {
        $response->getBody()->write((string)json_encode(
            ["data"=>[],"success"=>"false","message"=>"Could not insert record into table:  $mysqli->error"]));
     }
     $mysqli->close();
    return $response;
});
// /**
//  * List
//  */
$app->get('/company/all', function (Request $request,Response  $response, $args) {
    $sql = "select `company_id`,`company_name`, `company_alias`, `active`, `created_dt`,  `licenses`,   `contract_start`, `contractFiles`,`contract_end`,`notes`, `structures`,`created_dt` from roi_companies;";
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'roi';
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            $data[]=[
                "company_id"=>$obj->company_id,
                "company_name"=>$obj->company_name,
                "company_alias"=>$obj->company_alias,
                "active"=>$obj->active,
                "created_dt"=>$obj->created_dt,
                "licenses"=>$obj->licenses,
                "contract_start"=>$obj->contract_start,
                "contractFiles"=>$obj->contractFiles,
                "contract_end"=>$obj->contract_end,
                "notes"=>$obj->notes,
                "structures"=>$obj->structures,
            ];
        }
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>"true","message"=>"ok"]));
     }
     if ($mysqli->errno) {
        $response->getBody()->write((string)json_encode(
            ["data"=>[],"success"=>"false","message"=>"Could not insert record into table:  $mysqli->error"]));
     }
     $mysqli->close();
     $response->withHeader('Access-Control-Allow-Origin', '*');
    return $response;
});

// Run app
$app->run();