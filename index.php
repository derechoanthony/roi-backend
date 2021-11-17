<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require_once __DIR__ . '/vendor/autoload.php';

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
    $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

    // Optional: Allow Ajax CORS requests with Authorization header
    // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

    return $response;
});

// The RoutingMiddleware should be added after our CORS middleware so routing is performed first
$app->addRoutingMiddleware();

// The routes
$app->get('/api/v0/users', function (Request $request, Response $response): Response {
    $response->getBody()->write('List all users');

    return $response;
});

$app->get('/api/v0/users/{id}', function (Request $request, Response $response, array $arguments): Response {
    $userId = (int)$arguments['id'];
    $response->getBody()->write(sprintf('Get user: %s', $userId));

    return $response;
});

$app->post('/api/v0/users', function (Request $request, Response $response): Response {
    // Retrieve the JSON data
    $parameters = (array)$request->getParsedBody();

    $response->getBody()->write('Create user');

    return $response;
});

$app->delete('/api/v0/users/{id}', function (Request $request, Response $response, array $arguments): Response {
    $userId = (int)$arguments['id'];
    $response->getBody()->write(sprintf('Delete user: %s', $userId));

    return $response;
});

// Allow preflight requests
// Due to the behaviour of browsers when sending a request,
// you must add the OPTIONS method. Read about preflight.
$app->options('/api/v0/users', function (Request $request, Response $response): Response {
    // Do nothing here. Just return the response.
    return $response;
});

// Allow additional preflight requests
$app->options('/api/v0/users/{id}', function (Request $request, Response $response): Response {
    return $response;
});

// Using groups
$app->group('/api/v0/users/{id:[0-9]+}', function (RouteCollectorProxy $group) {
    $group->put('', function (Request $request, Response $response, array $arguments): Response {
        // Your code here...
        $userId = (int)$arguments['id'];
        $response->getBody()->write(sprintf('Put user: %s', $userId));

        return $response;
    });

    $group->patch('', function (Request $request, Response $response, array $arguments): Response {
        $userId = (int)$arguments['id'];
        $response->getBody()->write(sprintf('Patch user: %s', $userId));

        return $response;
    });

    // Allow preflight requests
    $group->options('', function (Request $request, Response $response): Response {
        return $response;
    });
});
//manager user list
$app->get('/api/v0/user/company/manager/{comp_id}', function (Request $request,Response  $response, $args) {

    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
    $dbname = 'roi';
    $port = 3306;
    // $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);

    // $mysqli = mysqli_connect('aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com', 'admin', 'TycKdB7X106OU4GH', 'roi', 3306);
    
    $uid = (int)$args['comp_id'];
    $sql = "select `user_id`,`first_name`,`last_name`,`username`,`currency`,`user_role` from roi_users where company_id=$uid and user_role=3; ";
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname , $port);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            $data[]=[
                "uid"=>$obj->user_id,
                "fname"=>$obj->first_name,
                "lname"=>$obj->last_name,
                "username"=>$obj->username,
                "currency"=>$obj->currency,
                "user_role"=>$obj->user_role,
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
//user company list
$app->get('/api/v0/user/company/{comp_id}', function (Request $request,Response  $response, $args) {

    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
    $dbname = 'roi';
    $port = 3306;
    // $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);

    // $mysqli = mysqli_connect('aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com', 'admin', 'TycKdB7X106OU4GH', 'roi', 3306);
    
    $uid = (int)$args['comp_id'];
    $sql = "select `user_id`,`first_name`,`last_name`,`username`,`currency`,`user_role` from roi_users where company_id=$uid;";
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname , $port);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            $data[]=[
                "uid"=>$obj->user_id,
                "fname"=>$obj->first_name,
                "lname"=>$obj->last_name,
                "username"=>$obj->username,
                "currency"=>$obj->currency,
                "user_role"=>$obj->user_role,
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
// user list

$app->get('/api/v0/user/{uid}', function (Request $request,Response  $response, $args) {

    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
    $dbname = 'roi';
    $port = 3306;
    // $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);

    // $mysqli = mysqli_connect('aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com', 'admin', 'TycKdB7X106OU4GH', 'roi', 3306);
    
    $uid = (int)$args['uid'];
    $sql = "select `user_id`,`first_name`,`last_name`,`company_id`,`username`,`currency`,`user_role`  from roi_users where user_id=$uid;";
    // `user_id`,`first_name`,`last_name`
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname , $port);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            $data[]=[
                "fname"=>$obj->first_name,
                "lname"=>$obj->last_name,
                "comp_id"=>$obj->company_id,
                "uid"=>$obj->user_id,
                "username"=>$obj->username,
                "currency"=>$obj->currency,
                "user_role"=>$obj->user_role,
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


// /**
//  * List
//  */
$app->get('/api/v0/company/all/{role}/{uid}', function (Request $request,Response  $response, $args) {

    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
    $dbname = 'roi';
    $port = 3306;
    // $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);

    // $mysqli = mysqli_connect('aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com', 'admin', 'TycKdB7X106OU4GH', 'roi', 3306);
    
    $role = (int)$args['role'];
    $uid = (int)$args['uid'];
    if($role==1){
        $sql = "select `company_id`,`company_name`, `account_contact`,account_contact_fname, account_contact_lname,`users`, `company_alias`, `active`, `created_dt`,  `licenses`,   `contract_start`, `contractFiles`,`contract_end`,`notes`, `structures`,`created_dt` from roi_companies;";
    }else{
        $compId = "SELECT company_id FROM roi_users where user_id=$uid;";
        $mysqli_comp = new mysqli($dbhost, $dbuser, $dbpass, $dbname , $port);
        $query_comp = $mysqli_comp->query($compId);
        $obj_comp = $query_comp->fetch_object();
        
        $sql = "select `company_id`,
            `account_contact`,`company_name`,
            account_contact_fname, account_contact_lname
            `users`, `company_alias`, `active`, `created_dt`,  `licenses`,   `contract_start`, `contractFiles`,`contract_end`,`notes`, `structures`,`created_dt` from roi_companies where company_id=$obj_comp->company_id;";
        
    }
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname , $port);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }
    $query = $mysqli->query($sql);
    if ($query) {
        while($obj = $query->fetch_object()){
            if(is_null($obj->users) ){
               $user_count = 0;
            }else{
                $user_count = $obj->users;
            }
            // var_dump($obj->account_contact);
            if(is_null($obj->licenses) ){
                $licenses = 0;
             }else{
                 $licenses = $obj->licenses;
             }
            if(is_null($obj->account_contact_fname) && is_null($obj->account_contact_lname)){
                $contact_person = ' - ';
             }else{
                 $contact_person = $obj->account_contact_fname.' '.$obj->account_contact_lname;
             }
             $cdate = explode(" ",$obj->created_dt);
            $data[]=[
                "company_id"=>$obj->company_id,
                "company_name"=>$obj->company_name,
                "company_alias"=>$obj->company_alias,
                "account_contact"=>$obj->account_contact,
                "contact_person"=>$contact_person,
                "active"=>($obj->active == 1) ? 'Active':'In-Active',
                "users"=>$user_count,
                "created_dt"=>$cdate[0],
                "licenses"=>$licenses,
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




//get structures
$app->get('/api/v0/structure/all', function (Request $request,Response  $response, $args) {
    $sql = "select rcs.structure_id, rcs.structure_title, rcs.company_id, rcs.active, rcs.created_dt, rcs.notes, rc.company_name from roi_company_structures as rcs inner join roi_companies as rc on rcs.company_id = rc.company_id;";
    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
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
                "structure_id"=>$obj->structure_id,
                "structure_title"=>$obj->structure_title,
                "company_id"=>$obj->company_id,
                "active"=>($obj->active == 1) ? "Active" : "In-Active",
                "created_dt"=>$obj->created_dt,
                "notes"=>($obj->notes == null) ? "Empty" : $obj->notes,
                "company_name"=>$obj->company_name
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


//get structures
$app->get('/api/v0/structure/{comp_id}', function (Request $request,Response  $response, $args) {
    $id = (int)$args['comp_id'];
    $sql = "select rcs.structure_id, rcs.structure_title, rcs.company_id, rcs.active, rcs.created_dt, rcs.notes, rc.company_name from roi_company_structures as rcs inner join roi_companies as rc on rcs.company_id = rc.company_id and rc.company_id=$id;";
    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
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
                "structure_id"=>$obj->structure_id,
                "structure_title"=>$obj->structure_title,
                "company_id"=>$obj->company_id,
                "active"=>($obj->active == 1) ? "Active" : "In-Active",
                "created_dt"=>$obj->created_dt,
                "notes"=>($obj->notes == null) ? "Empty" : $obj->notes,
                "company_name"=>$obj->company_name
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


//get version
$app->get('/api/v0/version/{id}', function (Request $request, Response $response, array $arguments): Response {


    $versionId = (int)$arguments['id'];
    $sql = "select * from roi_structure_versions where structure_id = $versionId;";
    $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
    $dbuser = 'admin';
    $dbpass = 'TycKdB7X106OU4GH';
    $dbname = 'roi';
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if($mysqli->connect_errno ) {
       printf("Connect failed: %s<br />", $mysqli->connect_error);
       exit();
    }

    $query = $mysqli->query($sql);
    if ($query) {
        $data = [];
        while($obj = $query->fetch_object()){
            
            $data[]=[
                "version_id"=>$obj->version_id,
                "version_name"=>$obj->version_name,
                "version_stage"=>$obj->version_stage,
                "version_notes"=>($obj->notes == null) ? "-" : $obj->notes,
                "created_dt"=>$obj->created_dt,
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


$app->post('/api/v0/company', function (Request $request,Response  $response, $args) {
    try {
        //code...
        $haveFile = true;
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = (array)($request->getUploadedFiles()['uploadFile'] ?? []);
        $filename = "";
        if ($uploadedFile) {
            $directory = './uploads';
            $uploadedFile = $uploadedFiles['uploadFile'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = moveUploadedFile($directory, $uploadedFile);
            } 
        }
            $params = $request->getParsedBody();
            $companyName = $params['companyName'];
            $companyAlias = ($params['companyAlias']=="") ? "NA" : $params['license'];
            $license = ($params['license']=="") ? 0 : $params['license'];
            $contacts = ($params['contacts']=="") ? "NA" : $params['contacts'];
            $contactsEmail = ($params['contactsEmail']=="") ? "NA" : $params['contactsEmail'];
            $contractStart = ($params['contractStart']=='') ? date("Y-m-d") : $params['contractStart'];
            $contractEnd = ($params['contractEnd']=='') ? date("Y-m-d") : $params['contractEnd'];
            $notes = ( $params['notes']=='') ? 'NA' : $params['notes'];
            $structures =  ($params['structure']=='') ? 1 : $params['structure'];
            $created_dt = date("Y-m-d");
            $contactfname = ($params['contactfname']=="") ? "NA" : $params['contactfname'];
            $contactlname = ($params['contactlname']=="") ? "NA" : $params['contactlname'];
            
            $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
            $dbuser = 'admin';
            $dbpass = 'TycKdB7X106OU4GH';
            $dbname = 'roi';
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s<br />", $mysqli->connect_error);
                exit();
            }
                $sql = "insert into roi_companies(company_name,company_alias,licenses,account_contact,account_email,contract_start,contract_end,notes,contractFiles,structures,created_dt,account_contact_fname,account_contact_lname) values
                        ('$companyName','$companyAlias','$license','$contacts','$contactsEmail','$contractStart','$contractEnd','$notes','$filename','$structures','$created_dt','$contactfname','$contactlname');";
               
                $mysqli->query($sql);
                $last_id = $mysqli->insert_id;
                $data = ["company_id"=>$last_id];
                $response->getBody()->write((string)json_encode(
                    ["data"=>[$data],"success"=>"true","message"=>"ok"]));
                $response->withHeader('Access-Control-Allow-Origin', '*');
                return $response;
        
    } catch (\Throwable $th) {
        $data = ["success"=>false, "data"=>$th, "message"=>"error"];
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>"true","message"=>"ok"]));
        $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }   
});

$app->post('/api/v0/structure', function (Request $request,Response  $response, $args) {
    try {
        //code...
            $params = $request->getParsedBody();
            // $structures =  ($params['structure']=='') ? 1 : $params['structure'];
            // $companyName = $params['companyName'];
            $structure_title = $params['templateName'];
            $company_id = $params['tplcompanyId'];
            $active = ($params['templateStatus'] == '') ? 1 : $params['templateStatus'];
            $notes = $params['templateNotes'];
            $created_dt = date("Y-m-d");
            
            $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
            $dbuser = 'admin';
            $dbpass = 'TycKdB7X106OU4GH';
            $dbname = 'roi';
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s<br />", $mysqli->connect_error);
                exit();
            }
                $sql = "insert into roi_company_structures(structure_title,company_id,active,created_dt,notes)values('$structure_title',$company_id,$active,'$created_dt','$notes');";             
               
                $mysqli->query($sql);
                $last_id = $mysqli->insert_id;

                $data = ["structure_id"=>$last_id,"company_id"=>$company_id, "title"=>$structure_title,"status"=>$active, "created_dt"=>$created_dt, "notes"=>$notes];
                $response->getBody()->write((string)json_encode(
                    ["data"=>[$data],"success"=>"true","message"=>"ok"]));
                $response->withHeader('Access-Control-Allow-Origin', '*');
                return $response;
        
    } catch (\Throwable $th) {
        $data = ["success"=>false, "data"=>$th, "message"=>"error"];
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>"true","message"=>"ok"]));
        $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }   
});

// add new version
$app->post('/api/v0/version', function (Request $request,Response  $response, $args) {
    try {
        //code...
            $params = $request->getParsedBody();
            $structure_id = $params['structure_id'];
            $version_name = $params['version_name'];
            $version_stage = $params['version_stage'];
            $version_notes = $params['version_notes'];
            $created_dt = date("Y-m-d");
            
            $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
            $dbuser = 'admin';
            $dbpass = 'TycKdB7X106OU4GH';
            $dbname = 'roi';
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s<br />", $mysqli->connect_error);
                exit();
            }
                $sql = "insert into roi_structure_versions(version_name,structure_id,version_stage,created_dt,notes) values
                ('$version_name',$structure_id,$version_stage,'$created_dt','$version_notes');
                ";
               
                $mysqli->query($sql);
                $last_id = $mysqli->insert_id;

                $data = ["version_id"=>$last_id,"version_name"=>$version_name, "version_stage"=>$version_stage,"created_dt"=>$created_dt, "notes"=>$version_notes];
                $response->getBody()->write((string)json_encode(
                    ["data"=>[$data],"success"=>"true","message"=>"ok"]));
                $response->withHeader('Access-Control-Allow-Origin', '*');
                return $response;
        
    } catch (\Throwable $th) {
        $data = ["success"=>false, "data"=>$th, "message"=>"error"];
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>"true","message"=>"ok"]));
        $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }   
});

$app->post('/api/v0/add/user', function (Request $request,Response  $response, $args) {
    try {
        //code...
            $params = $request->getParsedBody();
            $fname = $params['fname'];
            $lname = $params['lname'];
            $uname = $params['email'];
            $role = $params['userType'];
            $company_id = $params['company_id'];
            $pwd = ($params['pwd']=="") ? "365d38c60c4e98ca5ca6dbc02d396e53" : MD5($params['pwd']);
          
            $vcode = '11388c9893335786ab178b4561c729bce92d8db4a099c5452c001a4ed273fd04';
            $currency = $params['currency'];
            $created_dt = date("Y-m-d");
            
            $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
            $dbuser = 'admin';
            $dbpass = 'TycKdB7X106OU4GH';
            $dbname = 'roi';
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s<br />", $mysqli->connect_error);
                exit();
            }
            //validate if the company is suitable to add new users
            $comp_licenses = "select licenses from roi_companies where company_id=$company_id;";
            $query = $mysqli->query($comp_licenses);
                if ($query) {
                    $obj = $query->fetch_object();
                    if($obj->licenses === NULL || $obj->licenses === 0){
                        $response->getBody()->write((string)json_encode(
                            ["data"=>[],"success"=>false,"message"=>"All license are use"]));
                            
                    }else{
                        $sql = "insert into roi_users(username,password,company_id,first_name,last_name,currency,user_role,verification_code) values
                            ('$uname','$pwd',$company_id,'$fname','$lname','$currency','$role','$vcode');
                            ";
                            $mysqli->query($sql);
                            $last_id = $mysqli->insert_id;

                            $data = ["uid"=>$last_id,"fname"=>$fname, "lname"=>$lname,"created_dt"=>$created_dt, "company_id"=>$company_id,"role"=>$role];
                            $response->getBody()->write((string)json_encode(
                                ["data"=>[$data],"success"=>true,"message"=>"$fname $lname Successfully Added."]));
                            $response->withHeader('Access-Control-Allow-Origin', '*');
                    }
                }
                return $response;
        
    } catch (\Throwable $th) {
        $data = ["success"=>false, "data"=>$th, "message"=>"error"];
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>false,"message"=>"ok"]));
        $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }   
});

$app->post('/api/v0/update/user/{uid}', function (Request $request,Response  $response, $args) {
    try {
        //code...
            $params = $request->getParsedBody();
            $uid = (int)$args['uid'];
            $fname = $params['fname'];
            $lname = $params['lname'];
            $uname = $params['email'];
            $role = $params['userType'];
            $company_id = $params['company_id'];
            $pwd = $params['pwd'];
          
            $vcode = '11388c9893335786ab178b4561c729bce92d8db4a099c5452c001a4ed273fd04';
            $currency = $params['currency'];
            $created_dt = date("Y-m-d");
            
            $dbhost = 'aws-sandbox-development.cmhzsdmoqjl7.us-east-1.rds.amazonaws.com';
            $dbuser = 'admin';
            $dbpass = 'TycKdB7X106OU4GH';
            $dbname = 'roi';
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s<br />", $mysqli->connect_error);
                exit();
            }
            //validate if the company is suitable to add new users
            $comp_licenses = "select licenses from roi_companies where company_id=$company_id;";
            $query = $mysqli->query($comp_licenses);
                if ($query) {
                    $obj = $query->fetch_object();
                    if($obj->licenses === NULL || $obj->licenses === 0){
                        $response->getBody()->write((string)json_encode(
                            ["data"=>[],"success"=>false,"message"=>"All license are use"]));
                            
                    }else{
                        if($pwd == ""){
                            $userpwd = "";
                        }else{
                            $userpwd = ",password='$pwd'";
                        }
    
                        $sql = "update roi_users set username='$uname' $userpwd ,company_id=$company_id,first_name='$fname',last_name='$lname',currency='$currency',user_role='$role' where user_id=$uid;
                            ";
                            $mysqli->query($sql);
                            $last_id = $uid;

                            $data = ["uid"=>$last_id,"fname"=>$fname, "lname"=>$lname,"created_dt"=>$created_dt, "company_id"=>$company_id,"role"=>$role];
                            $response->getBody()->write((string)json_encode(
                                ["data"=>[$data],"success"=>true,"message"=>"$fname $lname Successfully Updated."]));
                            $response->withHeader('Access-Control-Allow-Origin', '*');
                    }
                }
                return $response;
        
    } catch (\Throwable $th) {
        $data = ["success"=>false, "data"=>$th, "message"=>"error"];
        $response->getBody()->write((string)json_encode(
            ["data"=>[$data],"success"=>false,"message"=>"ok"]));
        $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }   
});












/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @param UploadedFile $uploadedFile file uploaded file to move
 * @return string filename of moved file
 */
function moveUploadedFile($directory, UploadedFileInterface $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}
$app->run();