<!DOCTYPE html>
<html>
<head>
  <title>InAcademia Validation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="style/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <script src="scripts/copy.js" defer></script>
</head>

<body>

<div class='header'></div>
  <div class='content'>

    <?php
    if ($error) {
      echo "<pre id=result>$error</pre>\n";
    } else if ($profile_link) {
      $me_url = "$base_url/$short";
      echo "<p>Copy this URL to your Mastodon profile page Extra Fields section</p>";
      echo "<h2><a href=\"$me_url\" target=_blank id=me_url>$me_url</a> <img src=\"images/clipboard.svg\" onclick=\"Copy()\" height=20></h2>\n";
    } else { // Main page
    if ($short) {
      echo "You are validated, but you haven't registered a Mastodon rel=\"me\" link yet.<br>\n";
    }?>

    <h1>Paste the Mastodon rel="me" link below</h1>

    <?php
      require 'form.php';
    ?>

    <p><b>You can verify your Mastodon account here if:</b></p>
    <ul>
      <li>You are a user at a Dutch institution that is a member of SURFconext
      <li>Your institution's identity provider releases the 'faculty' attribute
    </ul>
    <br>
    <p><b>Paste the Mastodon rel="me" link below and hit the Validate button, then:</b></p>
    <ul>
      <li>Select your institution from the list on the next page and enter your login credentials
      <li>If you can't find your institution, or if you cannot authenticate, please contact the department at your institution that is responsible for provisioning user accounts.
    </ul>
  </div>

  <?php } // Main page?>

  <div class='sidebar'>
    <img src="/images/geant_logo.svg"><br>
    <img src="/images/eu_flag.svg"><br>
    <p>
      <b>How does InAcademia work?</b><br>
      <a href="https://inacademia.org">Click here</a> to find out more
    </p>
  </div>

</body>
</html>
