<?php
namespace AppBundle\Services;

class Helpers {

	public $jwt_auth;

	/* inyectaos a nuestro helper nuestro otro helper src\AppBundle\Services\JwtAuth.php
	   para tener metodos disponibles en Helpers.php

		 app.helpers:
	        class: AppBundle\Services\Helpers
	        arguments: ["@app.jwt_auth"]

	*/
	public function __construct($jwt_auth) {
		$this->jwt_auth = $jwt_auth;
	}


	/** Algo redundante pero nos apoyamos de metodo (JwtAuth.php) checkToken
	para verificar token aqui

	@params $hash token en si, $getIdentity = false
	*/
	public function authCheck($hash, $getIdentity = false){

		// Variable local  $mi_hemper_jwt_auth iguala a nuestra helper
		$mi_hemper_jwt_auth = $this->jwt_auth;

		$auth = false;

		if($hash != null){

			if($getIdentity == false){
				$check_token = $mi_hemper_jwt_auth->checkToken($hash);
				if($check_token == true){
					$auth = true;
				}
			}else{

				// regresa token decodificado
				$check_token = $mi_hemper_jwt_auth->checkToken($hash, true);
				if(is_object($check_token)){
					$auth = $check_token;
				}
			}

		}

		return $auth;
	}

	/* Comversion a json propio */
	public function json($data){
		$normalizers = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
		$encoders = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());

		$serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
		// Serializamos a JSON
		$json = $serializer->serialize($data, 'json');

		$response = new \Symfony\Component\HttpFoundation\Response();
		$response->setContent($json);
		$response->headers->set("Content-Type", "application/json");

		return $response;
	}

	public function hola(){
		return "Hola desde el servicio";
	}

}
