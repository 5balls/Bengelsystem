<?php
require_once 'konfiguration.php';
require_once 'SQL.php';

/// Logout
////////////////////////////////////////////////////////
if (isset($_GET['logout']) || isset($_POST['logout'])) {
    // remove all session variables
    session_unset();

    // destroy the session
    session_destroy();
    echo '<!doctype html><html><head><meta http-equiv="Refresh" content="0; URL=index.php" /></head></html>';
}

/// Login
////////////////////////////////////////////////////////
if (isset($_POST['login'])) {
    $messages = [];

    $HelferEmail = $_POST['helfer-email'];
    $HelferPasswort = $_POST['helfer-passwort'];

    if (empty($messages)) {
        HelferLogin($db_link, $HelferEmail, $HelferPasswort, 0);
    } else {
        // Fehlermeldungen ausgeben:
        echo '<div class="error"><ul>';
        foreach ($messages as $message) {
            echo '<li>' . htmlspecialchars($message) . '</li>';
        }
        echo '</ul></div>';
    }
}

if (!isset($_SESSION["HelferID"])) {
    ?>
<!doctype html>
<html lang=de>
<head>
  <title><?php echo EVENTNAME ?> Home</title>
  <link rel="stylesheet" href="css/style_desktop.css" media="screen and (min-width:781px)"/>
  <link rel="stylesheet" href="css/style_mobile.css" media="screen and (max-width:780px)"/>
  <script src="js/helferdb.js" type="text/javascript"></script>
  <script src="<?php echo JQUERY ?>" type="text/javascript"></script>
  <script>
  console.log("log","<?php echo JQUERY ?>");
  window.onload = function() {
    if (!window.jQuery) {
       document.getElementById("jquerywarn").innerHTML = "<font size=+2 color=red><b>You have not installed the jquery library! (also check dhtmlx scheduler for the calendar)</b></font>";
    }
}
</script>
  <meta name="viewport" content="width=480" />
  <meta charset="utf-8">
</head>
<body>
<div id=jquerywarn></div>
<style>
    /* Desktop Style (Default) */
    .adaptive-container {
        width: 50%;
        margin: 0 auto; /* Centers the image on desktop */
    }
    .adaptive-img {
        width: 90%;
        height: auto;
        display: block;
        border-radius: 8px;
    }

    /* Mobile Style (Triggers on screens smaller than 768px) */
    @media (max-width: 768px) {
        .adaptive-container {
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>
<div class="adaptive-container">
   <img alt="Condor Logo" class="adaptive-img" src="https://jonglaria.org/conventions/condor/banner.png" />
</div>
   <h1>Condor Helferschichtensystem</h1>
<form method="post" action="#Info">

  <fieldset>
    <legend>Login</legend>
    
    <table border="0" style="border: 0px solid black;">
            <tr>     
              <td style="border: 0px solid black;">Email</td></tr><tr><td style="border: 0px solid black;">
              <input name="helfer-email" type="text" size=35 value="<?php echo htmlspecialchars($HelferEmail ?? '')?>" required>
              </td>
            <tr>
            <tr>     
              <td style="border: 0px solid black;">Passwort</td></tr>
              <tr><td style="border: 0px solid black;">
              <input name="helfer-passwort" id="helfer-passwort" type="password" size=35 value="<?php echo htmlspecialchars($HelferHandy ?? '')?>" required>
              </td><td style="border: 0px solid black;">
              <input type="button" value="Passwort zeigen" style="width:180px !important" onclick="showPassword('helfer-passwort')">
              </td>
            <tr>
    </table>
    
    
  </fieldset>
  
  <p><button style="width: 100px" name="login" value="1">Login</button></p>


 </form> 
<div>
Noch keinen Account? <a href="https://login.jonglaria.org/requestaccount">Hier einen anlegen!</a>
</div>
<div>
Passwort vergessen? <a href="https://login.jonglaria.org/password/requestreset/">Hier zurücksetzen!</a>
</div>

</body>
</html>
    <?php
    exit;
}


$HelferID = $_SESSION["HelferID"];
$HelferName = $_SESSION["HelferName"];
$HelferEmail = $_SESSION["HelferEmail"];
$AdminID = isset($_SESSION["AdminID"]) ? $_SESSION["AdminID"]  : -1;
//TODO vereinheitlichen. index.php verwendet HelferIsAdmin
$HelferIsAdmin = $AdminStatus = $_SESSION["AdminStatus"];
$AliasHelferID = $_SESSION["AliasHelferID"] ?? $HelferID ;
$AliasHelferName = $_SESSION["AliasHelferName"] ?? $HelferName ;
$HelferLevel = $_SESSION["HelferLevel"];

?>
