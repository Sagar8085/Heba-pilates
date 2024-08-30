<!DOCTYPE html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <style>
    html, body{
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        /*background: linear-gradient(180deg, #3D40AD 0%, #6B6ED7 100%);*/
    }
    .spinner {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 50px;
    }

    i {
        color: #804c9e;
    }

    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        width: 100%;
    }
  </style>
</head>

<body>
    <div class="container">
        Please wait...
    </div>

    <div id="status">{{ $status }}</div>

    <script>
        var status = document.getElementById("status").innerHTML;
        window.ReactNativeWebView.postMessage(status);
        // if (window.ReactNativeWebView.postMessage.length !== 1){
        //     setTimeout(waitForBridge, 200);
        // }
        // else {
        //     var status = document.getElementById("status").innerHTML;
        //     window.ReactNativeWebView.postMessage('hello');
        // }
    </script>
</body>
