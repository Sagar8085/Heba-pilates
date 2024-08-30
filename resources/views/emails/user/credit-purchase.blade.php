<!DOCTYPE html>
<!-- Set the language of your main document. This helps screenreaders use the proper language profile, pronunciation, and accent. -->
<html lang="en">
  <head>
    <!-- The title is useful for screenreaders reading a document. Use your sender name or subject line. -->
    <title>Heba Pilates Credit Pack</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Never disable zoom behavior! Fine to set the initial width and scale, but allow users to set their own zoom preferences. -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* RESET STYLES */
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* GMAIL BLUE LINKS */
        u + #body a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }

        /* SAMSUNG MAIL BLUE LINKS */
        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }

        /* These rules set the link and hover states, making it clear that links are, in fact, links. */
        /* Embrace established conventions like underlines on links to keep emails accessible. */
        a { color: #17B5C8; font-weight: 600; text-decoration: underline; }
        a:hover { color: #000000 !important; text-decoration: none !important; }
        a.button:hover { color: #ffffff !important; background-color: #17B5C8 !important; }
        a.button{ background: #17B5C8; color: white !important; border-radius:40px; padding:15px 40px; margin:50px auto; text-decoration: none; }

        /* These rules adjust styles for desktop devices, keeping the email responsive for users. */
        /* Some email clients don't properly apply media query-based styles, which is why we go mobile-first. */
        @media screen and (min-width:600px) {
            h1 { font-size: 48px !important; line-height: 48px !important; }
            .intro { font-size: 24px !important; line-height: 36px !important; }
        }
    </style>
  </head>
  <body style="margin: 0 !important; padding: 0 !important;">

    <!-- Some preview text. -->
    <div style="display: none; max-height: 0; overflow: hidden;">

    </div>

    <!-- This ghost table is used to constrain the width in Outlook. The role attribute is set to presentation to prevent it from being read by screenreaders. -->
    <!--[if (gte mso 9)|(IE)]>
    <table cellspacing="0" cellpadding="0" border="0" width="600" align="center" role="presentation"><tr><td>
    <![endif]-->
    <!-- The role and aria-label attributes are added to wrap the email content as an article for screen readers. Some of them will read out the aria-label as the title of the document, so use something like "An email from Your Brand Name" to make it recognizable. -->
    <!-- Default styling of text is applied to the wrapper div. Be sure to use text that is large enough and has a high contrast with the background color for people with visual impairments. -->
    <div role="article" aria-label="An email from Your Brand Name" lang="en" style="background-color: white; color: #2b2b2b; font-family: 'Avenir Next', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-size: 18px; font-weight: 400; line-height: 28px; margin: 0 auto; max-width: 600px; padding: 40px 20px 40px 20px;">

        <!-- Logo section and header. Headers are useful landmark elements. -->
        <header>
            <!-- Since this is a purely decorative image, we can leave the alternative text blank. -->
            <!-- Linking images also helps with Gmail displaying download links next to them. -->
            <a href="{{ env('APP_URL') }}">
                <center><img src="https://s3.eu-west-2.amazonaws.com/prod.hebepilates/emails/hebalogo-stripeline-dark.png" alt="" height="80" width="260"></center>
            </a>
            <!-- The h1 is the main heading of the document and should come first. -->
            <!-- We can override the default styles inline. -->
            <h1 style="color: #000000; font-size: 32px; font-weight: 800; line-height: 32px; margin: 48px 0; text-align: center;">
                Thanks for purchasing a Heba Pilates Credit Pack!
            </h1>
        </header>

        <!-- Main content section. Main is a useful landmark element. -->
        <main>
            <!-- Since this is a purely decorative image, we can leave the alternative text blank. -->
            <!-- Linking images also helps with Gmail displaying download links next to them. -->
            <a><img alt="" src="https://s3.eu-west-2.amazonaws.com/prod.hebepilates/emails/heba-default-email-banner.jpg" width="600" border="0" style="border-radius: 4px; display: block; max-width: 100%; min-width: 100px; width: 100%;"></a>
            <!-- A level 2 heading is used to keep the document order correct. -->
            <h2 style="color: #000000; font-size: 28px; font-weight: 600; line-height: 32px; margin: 48px 0 24px 0; text-align: center;">
                Your Confirmation Email
            </h2>
            <p>Thank you {{ $props['user']->first_name }} for purchasing a {{ $props['credit'] }} with Heba Pilates. </p>

            <p>Your credits are active immediately so go ahead and make your booking for your nearest studio.</p>
            <p style="text-align:center; padding:40px 0;"><a href="{{ env('APP_URL') }}/studio" class="button">Make a Reservation</a></p>

            <p>With your allocated credits you can book one week in advance for any sessions you plan to reserve. Bookings can be made at any time through your profile account via the Heba App.</p>

            <p>We look forward to seeing you soon.</p>

            <p><a href="{{ env('APP_URL') }}/membership">View your account</a> to upgrade to a membership, purchase an additional credit package or view your existing purchases.</p>

            <br>
            <p>Healthy Regards,</p>

            <h4>Team Heba</h4>


        </main>

        <!-- Footer information. Footer is a useful landmark element. -->
        <footer>
            <!-- This link uses descriptive text to inform the user what will happen with the link is tapped. -->
            <!-- It also uses inline styles since some email clients won't render embedded styles from the head. -->
            <!-- <p style="font-size: 16px; font-weight: 400; line-height: 24px; margin-top: 48px;">
                You received this email because you just signed up to Heba Pilates using this email address. Don't worry, if you don't want to receive marketing communications from us <a href="{{ env('APP_URL') }}/myaccount/marketing" style="color: #17B5C8; text-decoration: underline;">you can unsubscribe here</a>.
            </p> -->

            <!-- The address element does exactly what it says. By default, it is block-level and italicizes text. You can override this behavior inline. -->
            <address style="font-size: 16px; font-style: normal; font-weight: 400; line-height: 24px;">
                <strong>Your pilates specialists at:</strong> Windsor Marina, Maidenhead Road, Windsor, SL4 5TZ
            </address>
        </footer>

    </div>
    <!--[if (gte mso 9)|(IE)]>
    </td></tr></table>
    <![endif]-->
  </body>
</html>
