<?php
namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth {

	public $manager;
	public $key;

	/// manager de Firebase
	public function __construct($manager) {
		$this->manager = $manager;
		$this->key = "clave-secreta";
	}

	/*
	$getHas  sirve h

	@params ($email, $password, $getHash = NULL "por default"
	*/
	public function signup($email, $password, $getHash = NULL){
		$key = $this->key;

		$user = $this->manager->getRepository('BackendBundle:User')->findOneBy(
					array(
						"email" => $email,
						"password" => $password
					)
				);

		$signup = false;
		if(is_object($user)){
			$signup = true;
		}

		if($signup == true){
			$token = array(
				"sub" => $user->getId(),
				"email" => $user->getEmail(),
				"name"	=> $user->getName(),
				"surname"	=> $user->getSurname(),
				"password"	=> $user->getPassword(),
				"image"	=> $user->getImage(),
				"iat" => time(),
				"exp" => time() + (7 * 24 * 60 * 60)
			);

			// $key llave dada para procesar token, usar variablede entorno
			$jwt = JWT::encode($token, $key, 'HS256');
			$decoded = JWT::decode($jwt, $key, array('HS256'));

			if($getHash != null){
				return $jwt;   // regresara token
			}else{
				return $decoded;  //regresara   datos con password hasheado
			}

		}else{
			return array("status" => "error", "data" => "Login failed");
		}

	}

	/* checkToken para revizar token

	@params  $jwt el token en si, $getIdentity para ver si devuelve datos decodificados (si es true ) o boleano
	*/
	public function checkToken($jwt, $getIdentity = false){
		$key = $this->key;
		$bolean_auth = false;

		try{
			$decoded = JWT::decode($jwt, $key, array('HS256'));

		}catch(\UnexpectedValueException $e){
			$bolean_auth = false;
		}catch(\DomainException $e){
			$bolean_auth = false;
		}

		// Token decodificado, si existe propiedad de peticion token.sub  (indice)
		if(isset($decoded->sub)){
			$bolean_auth = true; // return true;
		}else{
			$bolean_auth = false;  // return false;
		}

		if($getIdentity == true){
			return $decoded;	// regresa decodficacion
		}else{
			return $bolean_auth;  //return boleano
		}
	}

}
