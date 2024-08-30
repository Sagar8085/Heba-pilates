<html>
<head>
    <title>AF Live Class</title>
    <style>
    body, html {
        background-color: gray;
        height: 100%;
    }

    #videos {
        position: relative;
        width: 100%;
        height: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    #subscriber {
    }

    .stream {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 100;
    }

    /* .subscribers {
        position: absolute;
        left: 370px;
        bottom: 10px;
        width: calc(100% - 370px);
        height: 246px;
        z-index: 10;
        overflow-x: auto;
    }

    .subscribers__box {
        width: 5000px;
        height: 246px;
        margin-left: 10px;
    }

    .OT_subscriber {
        float: left;
        width: 360px!important;
        height: 246px!important;
        margin-left: 10px!important;
        z-index: 10!important;
    } */

    div.video-trainer div.OT_widget-container {
        /* display: flex!important;
        align-items: center;
        justify-content: center */
    }

    div.video-trainer video.OT_video-element {
        /* width: auto!important;
        height: auto!important; */
    }
    </style>
    <script src="https://static.opentok.com/v2/js/opentok.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    // $(function() {
    //     console.log('width = ' + $(document).width());
    //     if ($(document).width() <= 1000) {
    //         window.location.href = 'https://apps.apple.com/ph/app/volution-elites-member/id1458012222';
    //     }
    // });
    </script>
</head>
<body>
    <div id="videos">
        @if($type === 'member')
            <div id="subscriber" class="stream"></div>
        @endif

            <div id="publisher" class="stream"></div>
    </div>

    <script>
    // replace these values with those generated in your TokBox Account
    var apiKey = "47155174";
    var sessionId = "{{ $sessionId }}";
    var token = "{{ $token }}";

    // Handling all of our errors here by alerting them
    function handleError(error) {
      if (error) {
        alert(error.message);
      }
    }

    // (optional) add server code here
    initializeSession();

    function initializeSession() {
        var session = OT.initSession(apiKey, sessionId);
        var numberOfStreams = 1;

        // Create a publisher
        var publisher = OT.initPublisher('publisher', {
            insertMode: 'append',
            fitMode: 'contain',
            width: '100%',
            height: '100%'
        }, handleError);

        // Connect to the session
        session.connect(token, function(error) {
            // If the connection is successful, initialize a publisher and publish to the session
            if (error) {
                handleError(error);
            } else {
                session.publish(publisher, handleError);
            }
        });
    }
    </script>
</body>
</html>
