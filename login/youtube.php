<!doctype html>
<html>
<head>
  <title>Sample YouTube Analytics API Implementation</title>
  <link type="text/css" rel="stylesheet" href="../css/youtube.css">
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="//www.google.com/jsapi"></script>
  <script type="text/javascript" src="../js/youtube.js"></script>
  <script type="text/javascript" src="https://apis.google.com/js/client.js?onload=onJSClientLoad"></script>
</head>
<body>
  <div id="login-container" class="pre-auth">This application requires access to your YouTube account.
    Please <a href="#" id="login-link">authorize</a> to continue.
  </div>
  <div class="post-auth">
    <div id="message"></div>
    <div id="chart"></div>
    <div>Choose a Video:</div>
    <ul id="video-list"></ul>
  </div>
</body>
</html>
