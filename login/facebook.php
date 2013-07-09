<?php
  //error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', '1');
  require_once '../lib/config.php';	
  require_once '../lib/facebook/facebook.php';	
  require_once '../lib/db/DB.php';	


   
  $app_id = FB_APP_ID;
  $app_secret = FB_APP_SECRET; 
  $my_url = FB_MY_URL;

   session_start();
   $access_token = isset($_SESSION['fb_access_token']) ? $_SESSION['fb_access_token'] : "";
   $code = $_REQUEST["code"];

   $facebook = new Facebook ( array ('appId' => $app_id, 'secret' => $app_secret, 'cookie' => true ) );
   Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
   Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
   Facebook::$CURL_OPTS[CURLOPT_SSLVERSION] = 3;

   if ($access_token ==""){
       if(empty($code)) {
         $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
         $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
           . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
           . $_SESSION['state'] 
           . "&scope=read_stream,publish_stream,read_insights,manage_pages,offline_access";

         echo("<script> top.location.href='" . $dialog_url  . "'</script>");
       }

       if($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
         $token_url = "https://graph.facebook.com/oauth/access_token?"
           . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
           . "&client_secret=" . $app_secret . "&code=" . $code;

         $response = file_get_contents($token_url);
         $params = null;
         parse_str($response, $params);

         $access_token = $params['access_token'];
         $_SESSION['fb_access_token'] = $access_token;
         GetFacebookPagesID($facebook, $access_token);
       }
       else {
         echo("The state does not match. You may be a victim of CSRF.");
       }
   } else {
         GetFacebookPagesID($facebook, $access_token);
   }


   function GetFacebookPagesID($facebook, $token){
     $fql = "SELECT page_id FROM page_admin WHERE uid = me() AND type != 'APPLICATION'";
     $stack_pages_id = $facebook->api ( array ('method' => 'fql.query', 'access_token' => $token, 'query' => $fql ) );
     GetFacebookPagesInfo($facebook, $stack_pages_id, $token);
   }

   function GetFacebookPagesInfo($facebook, $stack_pages_id, $token){
       $page_id_array = array();
       $stack_pages_info = array();
       foreach ($stack_pages_id as $page){
           array_push($page_id_array, $page['page_id']);
       }
       $pages_id = "'" . implode("','", $page_id_array) . "'";
       $fql = "SELECT page_id, name, fan_count, pic_square, pic_big, type, founded  from page WHERE page_id IN (".$pages_id.") AND type != 'APPLICATION'";
       $stack_pages_info = $facebook->api (array ('method' => 'fql.query', 'access_token' => $token, 'query' => $fql ));
       if (!empty($stack_pages_info)){
           SaveFacebookPages($stack_pages_info);
       }
   }

   function SaveFacebookPages($stack_pages_info){
       $platform_id = isset($_SESSION['jigowatt']['user_id']) ? $_SESSION['jigowatt']['user_id'] : "-1";
       $sql = "";
       // Set your connection info here
       Db::setConnectionInfo(DB_NAME, DB_USER, DB_PASSWORD);
       
       foreach ($stack_pages_info as $page){
           $page_id = $page['page_id'];
           $name = htmlentities($page['name']);
           $name = addslashes($name);
           $fan_count = $page['fan_count'];
           $pic_square = $page['pic_square'];
           $pic_big = $page['pic_big'];

           $record = array("PLATFORM_ID" => $platform_id, "NAME" => $name, "PAGE_ID" => $page_id, "USERS" => $fan_count, "PIC_SQUARE" => $pic_square, "PIC_BIG" => $pic_big);
	       $result = Db::execute('INSERT INTO facebook_pages (PLATFORM_ID, NAME, PAGE_ID, USERS, PIC_SQUARE, PIC_BIG) VALUES(:PLATFORM_ID, :NAME, :PAGE_ID, :USERS, :PIC_SQUARE, :PIC_BIG) ON DUPLICATE KEY
	       UPDATE USERS=:USERS, PIC_SQUARE=:PIC_SQUARE, PIC_BIG=:PIC_BIG;', $record);
       }
   }

 ?>
    <?php include_once('../header.php'); ?>
    <div class="container-fluid" id="main-container">
			<?php include_once('../sidebar.php'); ?>
			<div id="main-content" class="clearfix">
				<div id="page-content" class="clearfix">
					<div class="row-fluid">
						<!--PAGE CONTENT BEGINS HERE--

						<!--PAGE CONTENT ENDS HERE-->
					</div><!--/row-->
				</div><!--/#page-content-->
			</div><!--/#main-content-->
		</div><!--/.fluid-container#main-container-->
    <?php include_once('../footer.php'); ?>

