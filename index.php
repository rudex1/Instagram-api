<?php
/* configuracion de una api para descargar imagens */
set_time_limit(0); //sservidor
ini_set('default_socket_timeout', 300);

session_start();//inicio de secion 

/*************************** Instagram api *******************************/

define('clientID', '0340e213b8d04f96ba4fdc4c0e9149a2');
define('clientSecret', 'e9dcc62425304138b604f450098147b4');
define('redirectURL', 'https://localhost:3000/rudex1/api_instagram/index.php');
define('imageDirectory', 'pics/'); //donde se guarda todas las fotos

/*********************** CONECTANDO CON INSTAGRAM ****************************/
function connectToInstagram($url){
	$ch = curl_init();							//el enlace para transferir data

	curl_setopt_array($ch, array( 				//opsiones para transferir via curl
			CURLOPT_URL => $url, 			 	//el enlace
			CURLOPT_RETURNTRANSFER => true,		//muestra los resultados
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2			//No se preocupen por el host

		)); 

	$result = curl_exec($ch);	//ejecuta la transferencia
	curl_close($ch); 			//sierra sesion de curl
	return $result; 			//retorna todos las data 

}

/************************** EL ID DEL USUARIO *******************************/
function getUserID($userName){
	$url = 'https//api.instagram.com/v1/users/search?g='. $userName .'&client_id'. clientID;
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);	//json va a decodificar la incriptacion

	return $results['data'][0]['id'];				//retorna el ID del usuario

}

/************************** DEMOSTRAR LAS IMAGENES **************************/
function printImages($userID){
		$url = 'https//api.instagram.com/v1!users/'. $userID .'/media/recent?client_id='. clientID . '&count=5';
		$instagramInfo = connectToInstagram($url);
		$results =json_decode($instagramInfo, true);

		/******** PASAR LOS RESULTADOS *******/
		foreach ($results['data'] as $item ) {
			$image_url = $item['images']['low_resolution']['url'];
			echo '<img src="'. $image_url .'" /><br/>';
			savePicture($image_url);
		}
}
/************************** SALVAR LAS FOTOS *******************************/
function savePicture($image_url){
	echo $image_url .'<br/>';
	$filename = basename($image_url);
	echo $filename .'<br/>';
	/***** SELECCION DEL FOLDER pics *****/
	$destination = imageDirectory.$filename;
	file_put_contents($destination, file_get_contents($image_url));

}

if ($_GET['code']) {
	$code = $_GET['code'];
		$url = "https://api.instagram.com/oauth/access_token";
		$access_token_settings  = array(
			'client_id' 		=>  	clientID,
			'client_secret' 	=>  	clientSecret,
			'grant_type ' 		=>  	'authorisation_code',
			'code'				=>  	$code
			);
		$curl = curl_init($url);											//tenemos que transferir data
		curl_setopt($curl, CURLOPT_POST, true);								//usando POST
		curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);		//Usando esta configuracion
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);						//Vamos a retornar los sesultados como String/cadena
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);											//va y buscar la data!
		curl_close($curl);													//cerramos la conexion

		$results = json_decode($result, true);
		$result['user'][userName];
		$userID = getUserID($userName);

		printImages($userID);

}else
	{ ?>


<!DOCTYPE html>
<html>

	<link rel="stylesheet" href="css/myStyle.css">
	<head>
	
	</head>
	<body>
		<a href="https://api.instagram.com/oauth/authorise/?client_id=<?php echo clientID;?>&redirect_url=<?php echo redirectURL; ?>&response_type=code"> Entra a instagram para descargar tus fotos</a>

	</body>
</html>

<?php 
}
 ?>