<html>
<head>
    <title>Streaming Live Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0">
    <style>
    body, html {
        background-color: #000;
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

    <style>
        body {
            margin: 0;
        }
        
        #waiting {
            position: absolute;
            width: 100%;
            min-height: 100vh;
            top: 0;
            left: 0;
            z-index: 101;
            background: #9590b4;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 30px;
        }

        #waiting img {
            width: 90%;
            display: block;
            margin: 0 auto;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div id="waiting">
        <div style="text-align: center; max-width: 350px;">
            <img src="/images/illustrations/video-streaming.svg">
            The instructor hasn't started broadcasting yet, please wait
        </div>
    </div>

    <div id="videos">
        <div id="subscriber" class="stream"></div>
        <!-- <div id="publisher" class="stream"></div> -->
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
        var numberOfStreams = 0;

            // Subscribe to a newly created stream
        session.on('streamCreated', function(event) {

            console.log('STREAM CREATED!');

            $('#waiting').fadeOut();

            numberOfStreams++;
            $('.subscribers__box').css('width', (numberOfStreams * 360) + 'px');

            var subscriber = session.subscribe(event.stream, 'subscriber', {
                insertMode: 'append',
                fitMode: 'contain',
                width: '100%',
                height: '100%'
            }, handleError);

            subscriber.on('videoElementCreated', function(event) {
                console.log('new video created');
                console.log(event);
                console.log(event.target.stream.connection.data);
                // document.getElementById('subscriber-' + event.target.id);
                if (event.target.stream.connection.data === 'type=trainer') {
                    console.log('IS TRAINER');
                    $('#' + event.target.id).addClass('video-trainer');
                }
                // document.getElementById(event.target.id).setAttribute('class', 'subscriber-' + event.target.id);
            });
            // console.log('new subscription');
            // console.log(event);
        });

        // Connect to the session
        session.connect(token, function(error) {
            // If the connection is successful, initialize a publisher and publish to the session
            if (error) {
                handleError(error);
            } else {
                // session.publish(publisher, handleError);
            }
        });
    }
    </script>
</body>
</html>
