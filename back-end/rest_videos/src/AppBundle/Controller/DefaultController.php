<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller{

  // definido por default
  public function indexAction(Request $request){

    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', [
        'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
    ]);
  }

  /* 2 versiones de POST
  http://localhost/curso%20symfony/symfony/web/app_dev.php/login

  "Content-Type"		:		"application/x-www-form-urlencoded"

  (campo) json 			:

  {
  "email": "ricardo@gmail.com",
  "password": "ricardo",
  }

  -- Otro para obtener token

  (campo) json 			:

  {
  "email": "ricardo@gmail.com",
  "password": "ricardo",
  "gethash": "true"
  }

  regresa : "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImVtYWlsIjoicmljYXJkb0BnbWFpbC5jb20iLCJuYW1lIjoicmljYXJkbyIsInN1cm5hbWUiOiJyaWNhcmRvbyIsInBhc3N3b3JkIjoiNjUzMDRkYWMzODIzMDY5NjczYWE5ZDNiOTBkY2I5ZjQ0OTM4ZTJkMTJmNTg1MDlhZGRjOTE1ZDA4OTIyYjY0YiIsImltYWdlIjpudWxsLCJpYXQiOjE0OTI1NjE0MDgsImV4cCI6MTQ5MzE2NjIwOH0.bkZwaNkumobNr3w4JxP-NcCWGMegxtWKeWYfWi9e2Gs"

  */
  public function loginAction(Request $request){
    $helpers = $this->get("app.helpers");
    $jwt_auth = $this->get("app.jwt_auth");

    // Recibir json por POST
    $json = $request->get("json", null);

    if($json != null){
    	$params = json_decode($json);

    	$email = (isset($params->email)) ? $params->email : null;
    	$password = (isset($params->password)) ? $params->password : null;
    	$getHash = (isset($params->gethash)) ? $params->gethash : null;

    	$emailContraint = new Assert\Email();
    	$emailContraint->message = "This email is not valid !!";

    	$validate_email = $this->get("validator")->validate($email, $emailContraint);

    	// Cifrar password
    	$pwd = hash('sha256', $password);

    	if(count($validate_email) == 0 && $password != null){

    		if($getHash == null || $getHash == "false"){
    			$signup = $jwt_auth->signup($email, $pwd);
    		}else{
    			$signup = $jwt_auth->signup($email, $pwd, true);
    		}

    		return new JsonResponse($signup);
    	}else{
    		return $helpers->json(array(
    			"status" => "error",
    			"data" => "Login not valid!!"
    			));
    	}

    }else{
    	return $helpers->json(array(
    			"status" => "error",
    			"data" => "Send json with post !!"
    			));
    }
  }

  /*  funcion dond eprobamos token (hash)

  Token pasarlo por cabecera
  aqui lo pasamos cn body   por  authorization


  POST    http://localhost/curso%20symfony/symfony/web/app_dev.php/pruebas
  authorization: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImVtYWlsIjoicmljYXJkb0BnbWFpbC5jb20iLCJuYW1lIjoicmljYXJkbyIsInN1cm5hbWUiOiJyaWNhcmRvbyIsInBhc3N3b3JkIjoiNjUzMDRkYWMzODIzMDY5NjczYWE5ZDNiOTBkY2I5ZjQ0OTM4ZTJkMTJmNTg1MDlhZGRjOTE1ZDA4OTIyYjY0YiIsImltYWdlIjpudWxsLCJpYXQiOjE0OTI1NjE0MDgsImV4cCI6MTQ5MzE2NjIwOH0.bkZwaNkumobNr3w4JxP-NcCWGMegxtWKeWYfWi9e2Gs"

  Regresa si esta bien:   bool(true)  porque solo la revizamos var_dump
  */
  public function pruebasAction(Request $request){
    $helpers = $this->get("app.helpers");

    $hash_mi_token = $request->get("authorization", null);
    //$check = $helpers->authCheck($hash_mi_token);

    /* =====================================================
    para datos decodificados
    $check = $helpers->authCheck($hash_mi_token, true);
    ===================================================== */
    $check = $helpers->authCheck($hash_mi_token, "true");

    var_dump($check);

    die();
  }

}
