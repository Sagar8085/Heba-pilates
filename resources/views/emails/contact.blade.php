<!DOCTYPE html>
<!-- Set the language of your main document. This helps screenreaders use the proper language profile, pronunciation, and accent. -->
<html lang="en">
  <head>
    <!-- The title is useful for screenreaders reading a document. Use your sender name or subject line. -->
    <title>Contact Form Submission</title>
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
        a { color: #B200FD; font-weight: 600; text-decoration: underline; }
        a:hover { color: #000000 !important; text-decoration: none !important; }

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
        You have a new submission via your contact form.
    </div>
    <!-- Get rid of unwanted preview text. -->
    <!-- <div style="display: none; max-height: 0px; overflow: hidden;">
    &nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;
    </div> -->

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
            <center><img src="https://s3.eu-west-2.amazonaws.com/prod.hebepilates/emails/hebalogo-stripeline-dark.png" alt="" height="80" width="260"></center>
            <!-- The h1 is the main heading of the document and should come first. -->
            <!-- We can override the default styles inline. -->
            <h1 style="color: #000000; font-size: 32px; font-weight: 800; line-height: 32px; margin: 36px 0;">
                Contact Form Submission
            </h1>
        </header>

        <!-- Main content section. Main is a useful landmark element. -->
        <main>
            <!-- Intro copy -->
            <p class="intro" style="color: #000000; font-size: 18px; font-weight: 600; line-height: 28px;">
                From: {{ $props['email'] }}
            </p>

            <!-- Body copy -->
            <p>
                Topic: {{ $props['topic'] }}
            </p>

            <p>
                {{ $props['message'] }}
            </p>
        </main>

        <!-- Footer information. Footer is a useful landmark element. -->
        <footer>
            <!-- This link uses descriptive text to inform the user what will happen with the link is tapped. -->
            <!-- It also uses inline styles since some email clients won't render embedded styles from the head. -->
            <p style="font-size: 16px; font-weight: 400; line-height: 24px; margin-top: 48px;">
                You received this email because someone filled out the contact form on the Heba Pilates web portal.</a>.
            </p>
        </footer>

    </div>
    <!--[if (gte mso 9)|(IE)]>
    </td></tr></table>
    <![endif]-->
  </body>
</html>
