<?php
$token = $_REQUEST["access_token"];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="lib/instagram/instagram-js-sdk.js"></script>
        <script>
            var IG = new Instagram();
            var token = window.location.hash.substring(1);
            token = token.substr(token.indexOf("=") + 1)
            //GetAuthUser();
            MashapePinterestTest()

            //Ritorna il tipo di chart assciato all'inisght
            function GetAuthUser() {
                return $.ajax({
                    type: 'GET',
                    url: 'https://api.instagram.com/v1/users/self',
                    dataType: 'jsonp',
                    data: {
                        access_token: token
                    },
                    success: function (response) {
                        var code;
                        code = response.meta.code;
                        if (code == '200'){
                            console.log(response.data);
                        }
                    },
                    error: function (objRequest) {
                        alert("Impossibile accedere ai dati dell'utente autorizzato");
                    }
                });
            }

            function MashapePinterestTest(){
                $.ajax({
                    url: 'https://ismaelc-pinterest.p.mashape.com/likes', // The URL to the API. You can get this by clicking on "Show CURL example" from an API profile
                    type: 'GET', // The HTTP Method
                    data: {
                        u: 'khenzo'
                    }, // Additional parameters here
                    cache :false,
                    datatype: 'json',
                    success: function(data) { 
                        console.log(data); 
                    },
                    error: function(err) { alert(err); },
                    beforeSend: function(xhr) {
	                xhr.setRequestHeader("X-Mashape-Authorization", "dWY0AqCvc3T1ktkYtrJKW8uC5k78bOPz"); // Enter here your Mashape key
                    }
                });
            }

        </script>
    </head>
    <body>
        ciao
    </body>
</html>
