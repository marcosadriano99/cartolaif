<?php

require_once '_app/Config.php';

$Login = $fb->getRedirectLoginHelper(); //carregamentos dos arquivos do FB

//permissoes que pagamos depois do usuario fazer o login
//develope-docs-login in fb-permissions

//atenção: todas as permissions que adicionar tem que ir no app tambem
//no app em revisão do app - iniciar um envio - permissions - enviar p/analise
$permissions = ['email', 'user_birthday', 'user_friends', 'user_likes'];

/*for pictures...
https://developers.facebook.com/docs/graph-api/reference/user/picture/
$permissions = ['email', 'user_photos'];
*/

/*for publications...
https://developers.facebook.com/docs/graph-api/reference/v2.9/user/feed
$permissions = ['email', 'publish_actions'];
*/


//SKD DO FB
try {
	//token is used for each user -> (used - usuario), (each - cada)
    if (isset($_SESSION['facebook_access_token'])) {
        $accessToken = $_SESSION['facebook_access_token'];
    } else {
        $accessToken = $Login->getAccessToken();
    }
} //verificações de login e outras coisas do usuario 
	catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
//verification for user login
if (isset($accessToken)) {
    if (isset($_SESSION['facebook_access_token'])) {
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    } else {
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        $oAuth2Client = $fb->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    $code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT);
    if (isset($_GET['code'])) {
        header('Location: ./');
    }
    try {
    	//permissions after login
        $profile_request = $fb->get('/me?fields=name,first_name,last_name,email, birthday,taggable_friends.limit(2),likes');//limit(2) - limit  of two friends in friends list
        $profile = $profile_request->getGraphNode()->asArray();
        
        /*permissions after login to pictures
		$profile_request = $fb->get('/me?fields=name,picture.width(200){url},cover'); solicita a url e a foto da capa
		*/

		/*permissions for publications
			 $posts_request = $fb->get('/me/posts?fields=name,link,type,status_type&limit=2');
	        $posts = $posts_request->getGraphEdge()->asArray();
	        $post = [
				//envio da mensagem

	            "message" => "Comece hoje mesmo a fazer integração com facebook em seu site Vlw",

	            //alterando as informações do post enviado
	            
	            "link" => "http://alexnascimento.com.br",
	            "picture" => "http://globoesporte.globo.com/platb/files/166/2013/04/Hulk1.jpg",
	        ];

	        //posting the post

	        //$enviar_post = $fb->post('/me/feed', $post);
	        //$response = $enviar_post->getGraphNode()->asArray();
	        //var_dump($response);
		*/
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;

    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    var_dump($profile);
    //for posts
    //var_dump($posts);
    
    /*picture information
		echo "<img src='{$profile['cover']['source']}'>";
	    echo "<img src='{$profile['picture']['url']}'>";

    */

    $logoff = filter_input(INPUT_GET, 'sair', FILTER_DEFAULT);
    if (isset($logoff) && $logoff == 'true'):
        session_destroy();
        header("Location: ./");
    endif;
    echo '<a href="?sair=true">Sair</a>';
    var_dump($_SESSION);
}else {

    //redirect page after login
    $loginUrl = $Login->getLoginUrl('http://localhost/#.php', $permissions);

    echo '<a href="' . $loginUrl . '">Entrar com facebook</a>';
    echo $accessToken;
    var_dump($_SESSION);
}