<?php

namespace App\Controller\Auth;

use App\Controller\BaseController;
// use App\Handler\Auth\CAuthHandler;
// use App\Model\User;
// use App\Service\Auth\CAuthService;
// use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
// use App\Helper\CCiper;
// use App\Helper\Mailer\CSendEmail;

class CAuthController 
{
    /**
     * Users model
     *
     * @var \App\model\User
     */
    private $userModel;

    /**
     * Service for this module
     *
     * @var \App\Service\Auth
     */
    private $authService;

    /**
     * Validation for this module
     *
     * @var \App\Handler\Auth
     */
    private $authHandler;
    private $email;
    /**
     * Create a new CAuthService instance.
     *
     * @param \Interop\Container\ContainerInterface
     *
     * @return void
     */
    public function __construct()
    {
        // $this->authService = new CAuthService();
        // $this->userModel = new User();
        // $this->authHandler = new CAuthHandler();
        // $this->email = new CSendEmail();
    }

    /**
     * Authenticate user
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return \Slim\Http\Response
     */
    public function login(Request $request, Response $response)
    {
        // $model = $this->convertToModel($request, $this->userModel);
        // $validation = $this->authHandler->loginHandler($model);

        // if (!$validation->isValid()) {
        //     return $this->returnWithErrors(
        //         $response,
        //         $validation->getErrors()
        //     );
        // }
        // $data = $this->authService->login($model);
        $data = ["message"=>"ok"];
        return $response->withJson($data);
    }
}