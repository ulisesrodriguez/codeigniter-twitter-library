<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*

  Author		Ulises Rodríguez
  Site:			http://www.ulisesrodriguez.com	
  Twitter:		https://twitter.com/#!/isc_ulises
  Facebook:		http://www.facebook.com/ISC.Ulises
  Github:		https://github.com/ulisesrodriguez
  Email:		ing.ulisesrodriguez@gmail.com
  Skype:		systemonlinesoftware
  Location:		Guadalajara Jalisco Mexíco
 
  Twitetr
*/


class Twitterlib{
	
	private $config = array(); 
	
	private $twitter = null;
		
	private $user = null;
	
	private $user_profile = null;
		 
    public function __construct()
    {
        
		 require 'twitter/twitteroauth/twitteroauth.php';
 
    }
 	
	
	public function config( $CONSUMER_KEY, $CONSUMER_SECRET ){
		
		if( empty( $CONSUMER_KEY  ) or empty( $CONSUMER_SECRET ) ) return false;
		
		$this->config['CONSUMER_KEY'] = $CONSUMER_KEY;
		$this->config['CONSUMER_SECRET'] = $CONSUMER_SECRET;
	
	}
	
	
	
	public function oauth_authlink( $callback = '' ){
		
    
    	$oauth = new TwitterOAuth( $this->config['CONSUMER_KEY'], $this->config['CONSUMER_SECRET'] );
         
   		//oauth_clearcookies();
         
		/* Solicitar el token a twitter */
		$tok = $oauth->getRequestToken( $callback );
        				 
		/* Dejar los tokens guardados al usuario para pasos después, son temporales */
		setcookie('oauth_request_token', $tok['oauth_token'], 0 );
		setcookie('oauth_request_token_secret', $tok['oauth_token_secret'] , 0 );
			 
		/* Construir el url de autenticación */
		return $oauth->getAuthorizeURL($tok['oauth_token'],true);
	}
	
	
	
	public function oauth_authenticate(){
		
		$token = isset( $_GET['oauth_token'] ) ? $_GET['oauth_token'] : '';
		$oauth_verifier = isset( $_GET['oauth_verifier'] ) ? $_GET['oauth_verifier'] : null;
			/* 
		if ( $token == '' || !isset($_COOKIE['oauth_request_token']) || !isset($_COOKIE['oauth_request_token_secret']) 
		|| $_COOKIE['oauth_request_token']=='' || $_COOKIE['oauth_request_token_secret']==''
		|| $token != $_COOKIE['oauth_request_token'] ) 
		{
			return false;
		}
	 */
		// Usamos los tokens temporales
		//$to = new TwitterOAuth( $this->config['CONSUMER_KEY'], $this->config['CONSUMER_SECRET'], $_COOKIE['oauth_request_token'], $_COOKIE['oauth_request_token_secret']);
		$to = new TwitterOAuth( $this->config['CONSUMER_KEY'], $this->config['CONSUMER_SECRET'], $token, $oauth_verifier);
	 
		/* Ahora solicitamos los tokens de acceso, que serán permanentes */
		$tok = $to->getAccessToken( $oauth_verifier );
		if ( $to->lastStatusCode() != 200 )
			return false;
			
			
		 
		$token = (string) $tok['oauth_token'];
		$token_secret = (string) $tok['oauth_token_secret'];
		$userid = (int) $tok['user_id'];
		if ($userid == 0 || empty($token) || empty($token_secret) )
			return false;
				 
		$info = array();
		$info['userid'] = $userid;
		$info['token'] = $token;
		$info['token_secret'] = $token_secret;
		$info['user_info'] = $to->get( 'account/verify_credentials' ); 
			 
		return $info;
	}
	 
	public function authenticate_user() 
	{   
		$info = $this->oauth_authenticate();
		if ( $info == false || !is_array($info) ) 
		{
			die( 'Autenticación no completada, datos incorrectos' ); // ustedes deben usar algo más elegante que die()
		}
	 	
		
		return $info;			
	}
	
	
 
}

?>