<html>
<head>
    <title>Streaming Live Event</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Blinker&display=swap" rel="stylesheet">
    <style>
    body, html {
        background-color: #30332C;
        height: 100%;
        font-family: 'Blinker', sans-serif;
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
            background: #30332C;
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
            <img src="/images/illustrations/clock.svg">
            This stream will start in<br>
            <span id="timeTillStream"></span>

            @if(request('tablet'))
                <a href="/tablet" style="display: block; font-size: 14px; color: white; margin-top: 50px;">I'll come back later</a>
            @else
                <a href="/bookings" style="display: block; font-size: 14px; color: white; margin-top: 50px;">I'll come back later</a>
            @endif
        </div>
    </div>

    <div id="time" style="display: none;">{{ $startTime }}</div>

    <div id="videos">
        <div id="subscriber" class="stream"></div>
        <!-- <div id="publisher" class="stream"></div> -->
    </div>

    <script>
    var time = document.getElementById("time").innerHTML;
    console.log(time);

    // Set the date we're counting down to
    var countDownDate = new Date(time).getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

      // Get today's date and time
      var now = new Date().getTime();

      // Find the distance between now and the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      var remainingString = '';

      if (days > 0) {
          remainingString += days + ' Days ';
      }

      if (hours > 0) {
          remainingString += hours + ' Hours ';
      }

      remainingString += minutes + ' Minutes ' + seconds + ' Seconds'

      // Display the result in the element with id="demo"
      document.getElementById("timeTillStream").innerHTML = remainingString;

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        location.reload();
        document.getElementById("timeTillStream").innerHTML = "EXPIRED";
      }
    }, 1000);
    </script>
</body>
</html>
