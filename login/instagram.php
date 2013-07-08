<!doctype html>
<html>
<head>
  <title>Sample Instagram API Implementation</title>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="../lib/instagram/instagram-js-sdk.js"></script>
  <script>

      var IG = new Instagram();

      var param = {
          client_id: '496066464a8c4e78bd6cc0e04e190d2d',
          redirect_uri: 'http://www.phru.it/socialdashboard/dashboard.php',
          scope: 'basic+comments+likes+relationships',
          response_type: 'token'
      }

      IG.auth(param); //then will go to the authorize page

      //handle the fn token
      var token = IG.getToken();
      console.log(token);

      //you need to set token before you use it
      IG.setOptions({
          token: token
      });


  </script>
</head>
<body>

</body>
</html>
