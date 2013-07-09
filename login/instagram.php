<?php include_once('../lib/config.php'); $is_return = isset($_REQUEST['return']) ? $_REQUEST['return'] : "0";?>
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
    <?php include_once('../footer.php'); ?><script type="text/javascript" src="../lib/instagram/instagram-js-sdk.js"></script>
  <script>
      var IG = new Instagram();
      var token = "";
      <?php if ($is_return != "1"){ ?> 
          var param = {
              client_id: '<?php echo INSTAGRAM_CLIENT_ID ?>',
              redirect_uri: '<?php echo INSTAGRAM_REDIRECT_URI ?>',
              scope: 'basic+comments+likes+relationships',
              response_type: 'token'
          }
      
            IG.auth(param); //then will go to the authorize page
      <?php } else {?>
            token = IG.getToken();
            IG.setOptions({
                token: token
            });
            IG.currentUser(function(res){
                if (!jQuery.isEmptyObject(res)){
                    if (res.meta.code == "200"){
                        SetInstagramAccount(res.data, token);
                    } else{
                        alert("Errore nelle API di Instagram")
                    }
                }
            });

      function SetInstagramAccount(res, token){
        $.ajax({
            type: 'GET',
            url: '../actions/SetInstagramAccount.php',
            dataType: 'json',
            data: {
                data: res,
                access_token: token
               }
            }).done(function(response) {
                console.log(response);
            }).fail(function(objRequest) {
                alert("Impossibile salvare i dati dell'account");
            });
      }

 <?php } ?>
  </script>
