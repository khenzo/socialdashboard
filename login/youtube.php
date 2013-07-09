<?php
// Call set_include_path() as needed to point to your client library.
require_once '../lib/google/src/Google_Client.php';
require_once '../lib/google/src/Google_YouTubeService.php';
require_once '../lib/config.php'; set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();$OAUTH2_CLIENT_ID = YOUTUBE_OAUTH2_CLIENT_ID;
$OAUTH2_CLIENT_SECRET = YOUTUBE_OAUTH2_CLIENT_SECRET;


/* You can acquire an OAuth 2 ID/secret pair from the API Access tab on the Google APIs Console
  <http://code.google.com/apis/console#access>
For more information about using OAuth2 to access Google APIs, please visit:
  <https://developers.google.com/accounts/docs/OAuth2>
Please ensure that you have enabled the YouTube Data API for your project. */


$client = new Google_Client();
$client->setClientId('399886696429.apps.googleusercontent.com');
$client->setClientSecret('zlP1JKgLr2dIYYZdGQF9n1vg');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

$youtube = new Google_YoutubeService($client);

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
  }

  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: ' . $redirect);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {

  try {
    $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
      'mine' => 'true',
    ));

    $htmlBody = '';
    foreach ($channelsResponse['items'] as $channel) {
      $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

      $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
        'playlistId' => $uploadsListId,
        'maxResults' => 50
      ));

      $htmlBody .= "<h3>Videos in list $uploadsListId</h3><ul>";
      foreach ($playlistItemsResponse['items'] as $playlistItem) {
        $htmlBody .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
          $playlistItem['snippet']['resourceId']['videoId']);
      }
      $htmlBody .= '</ul>';
    }
  } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
    echo "qui";
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>

    
<?php include_once('../header.php'); ?>
    <div class="container-fluid" id="main-container">
			<?php include_once('../sidebar.php'); ?>
			<div id="main-content" class="clearfix">
				<div id="page-content" class="clearfix">
					<div class="row-fluid">
						<!--PAGE CONTENT BEGINS HERE-->
<?=$htmlBody?>
						<!--PAGE CONTENT ENDS HERE-->
					</div><!--/row-->
				</div><!--/#page-content-->
			</div><!--/#main-content-->
		</div><!--/.fluid-container#main-container-->
    <?php include_once('../footer.php'); ?>  <script type="text/javascript" src="//www.google.com/jsapi"></script>
  <script type="text/javascript" src="https://apis.google.com/js/client.js?onload=onJSClientLoad"></script>
