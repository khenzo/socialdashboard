<?php

/**
 * Demonstration of the various OAuth flows. You would typically do this
 * when an unknown user is first using your application. Instead of storing
 * the token and secret in the session you would probably store them in a
 * secure database with their logon details for your website.
 *
 * When the user next visits the site, or you wish to act on their behalf,
 * you would use those tokens and skip this entire process.
 *
 * The Sign in with Twitter flow directs users to the oauth/authenticate
 * endpoint which does not support the direct message permission. To obtain
 * direct message permissions you must use the "Authorize Application" flows.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      https://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 3) Visit this page using your web browser.
 *
 * @author themattharris
 */

require '../lib/twitter/tmhOAuth.php';
require '../lib/twitter/tmhUtilities.php';
require '../lib/config.php';
require '../lib/db/DB.php';



$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => TWITTER_CUNSUMER_KEY,
  'consumer_secret' => TWITTER_CUNSUMER_SECRET,
));

$here = tmhUtilities::php_self();
session_start();


// reset request?
if (isset($_REQUEST['wipe'])) {
  session_destroy();
  header("Location: {$here}");

// already got some credentials stored?
} elseif ( isset($_SESSION['access_token']) ) {
  $tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

  $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));
  if ($code == 200) {
    $resp = json_decode($tmhOAuth->response['response']); 
    $auth_result = "<script>var result =".user_lookup()."</script>";
  } else {
    $resp = json_decode($tmhOAuth->response['response'], true);
    $auth_result = $resp['errors']['0']['message'];
  }
// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));

  if ($code == 200) {
    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    unset($_SESSION['oauth']);
    header("Location: {$here}");
  } else {
    outputError($tmhOAuth);
  }
// start the OAuth dance
} elseif ( isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize']) ) {
  $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

  $params = array(
    'oauth_callback'     => $callback
  );

  if (isset($_REQUEST['force_write'])) :
    $params['x_auth_access_type'] = 'write';
  elseif (isset($_REQUEST['force_read'])) :
    $params['x_auth_access_type'] = 'read';
  endif;

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);
  if ($code == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
    $force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
    $authurl = $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
    header('Location: ' . $authurl);

  } else {
    outputError($tmhOAuth);
  }
}

function user_lookup(){
    $user_data = array();
    $auth_success = "";
    //Faccio un lookup sul profilo utente per recuperare i dati
    if ($_SESSION['access_token']['oauth_token']){
        $tmhOAuth = new tmhOAuth(array(
          'consumer_key'    => TWITTER_CUNSUMER_KEY,
          'consumer_secret' => TWITTER_CUNSUMER_SECRET,
          'user_token'      => $_SESSION['access_token']['oauth_token'],
          'user_secret'     => $_SESSION['access_token']['oauth_token_secret']
        ));

        $ids[] = $_SESSION['access_token']['user_id'];
        define('LOOKUP_SIZE', 100);

        // lookup users
        $paging = ceil(count($ids) / LOOKUP_SIZE);
        $users = array();
        for ($i=0; $i < $paging ; $i++) {
          $set = array_slice($ids, $i*LOOKUP_SIZE, LOOKUP_SIZE);
          $tmhOAuth->request('GET', $tmhOAuth->url('1.1/users/lookup'), array(
            'user_id' => $set[0] //implode(',', $set)
          ));

          if ($tmhOAuth->response['code'] == 200) {
            $data = json_decode($tmhOAuth->response['response'], true);
            $users = array_merge($users, $data);
          } else {
            echo $tmhOAuth->response['response'];
            break;
          }
        }

        $profile_image = ($users[0]['profile_image_url']);
        
        // Set your connection info here
        Db::setConnectionInfo(DB_NAME, DB_USER, DB_PASSWORD);

        // Inserting twitter account data
        $user_data_slim = array(
            'TWITTER_USER_ID' => $_SESSION['access_token']['user_id'],
            'PLATFORM_ID' => $_SESSION['jigowatt']['user_id']
        );

        // Inserting twitter account data
        $user_data = array(
            'SCREEN_NAME' => $_SESSION['access_token']['screen_name'], 
            'OAUTH_TOKEN' => $_SESSION['access_token']['oauth_token'], 
            'OAUTH_TOKEN_SECRET' => $_SESSION['access_token']['oauth_token_secret'],
            'TWITTER_USER_ID' => $_SESSION['access_token']['user_id'],
            'PROFILE_IMAGE_URL' => $profile_image,
            'PLATFORM_ID' => $_SESSION['jigowatt']['user_id']
        );

        $get_account = Db::execute('SELECT * FROM twitter WHERE PLATFORM_ID=:PLATFORM_ID AND TWITTER_USER_ID=:TWITTER_USER_ID', $user_data_slim); 

        if ($get_account == 0){
                $result = Db::getLastInsertId('INSERT INTO twitter (PLATFORM_ID, SCREEN_NAME, OAUTH_TOKEN, OAUTH_TOKEN_SECRET, TWITTER_USER_ID, PROFILE_IMAGE_URL, CREATION_DATE) VALUES(:PLATFORM_ID, :SCREEN_NAME, :OAUTH_TOKEN, :OAUTH_TOKEN_SECRET, :TWITTER_USER_ID, :PROFILE_IMAGE_URL, NOW())', $user_data); 
        } else {
                $result = Db::getLastInsertId('UPDATE twitter SET SCREEN_NAME=:SCREEN_NAME, OAUTH_TOKEN=:OAUTH_TOKEN, OAUTH_TOKEN_SECRET=:OAUTH_TOKEN_SECRET, PROFILE_IMAGE_URL=:PROFILE_IMAGE_URL, CREATION_DATE=NOW() WHERE PLATFORM_ID=:PLATFORM_ID AND TWITTER_USER_ID=:TWITTER_USER_ID', $user_data);
        }
        

        if ($result >= "0"){
           $auth_success =  array("IMAGE" => $profile_image, "SCREEN_NAME" => $user_data['SCREEN_NAME']); 
           $auth_success = json_encode($auth_success);
        }

    }

    return $auth_success;
}

?>

    <?php include_once('../header.php'); ?>
    <div class="container-fluid" id="main-container">
			<?php include_once('../sidebar.php'); ?>
			<div id="main-content" class="clearfix">
				<div id="page-content" class="clearfix">
					<div class="row-fluid">
						<!--PAGE CONTENT BEGINS HERE-->
                            <a href="?authorize=1">Authorize Application (with callback)</a>
                            <?php echo $auth_result; ?>
						<!--PAGE CONTENT ENDS HERE-->
					</div><!--/row-->
				</div><!--/#page-content-->
			</div><!--/#main-content-->
		</div><!--/.fluid-container#main-container-->
    <?php include_once('../footer.php'); ?>
<script>
    console.log(result);
</script>
