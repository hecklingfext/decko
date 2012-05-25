<?php
define('INCLUDE_CHECK',true);

require 'config.php';
require 'functions.php';
//if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') die(header("Location: /"));

session_name('deckoLogin');
// Starting the session

session_set_cookie_params(2*7*24*60*60);
// Making the cookie live for 2 weeks

session_start();
$script = '
	<script type="text/javascript" src="js/slide.js"></script>
    <link rel="stylesheet" href="css/slide.css" type="text/css" media="screen" />
	';
$script_more = '';

if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();

	// Destroy the session
}

if(isset($_GET['logoff']))
{
	$_SESSION = array();
	session_destroy();
	header("Location: index.php?loggedoff");
	exit;
}

if(isset($_GET['loggedoff']))
{
    $script_more .= "localStorage.clear();";
}

if($_POST['submit']=='Login')
{
	// Checking whether the Login form has been submitted

	$err = array();
	// Will hold our errors

	if(!$_POST['username'] || !$_POST['pwd'])
		$err[] = 'All the fields must be filled in!';

	if(!count($err))
	{
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		$_POST['pwd'] = mysql_real_escape_string(md5($_POST['pwd']));
		$_POST['rememberMe'] = (int)$_POST['rememberMe'];

		// Escaping all input data

        $sql_str = "SELECT id,usr FROM users WHERE usr='{$_POST['username']}' AND pass='".$_POST['pwd']."'";
		$row = mysql_fetch_assoc(mysql_query($sql_str));

		if($row['usr'])
		{
			// If everything is OK login

			$_SESSION['usr']=$row['usr'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];

			// Store some data in the session

			setcookie('tzRemember',$_POST['rememberMe']);
			// We create the tzRemember cookie
		}
		else $err[]='Wrong username and/or password!';
	}

	if($err)
		$_SESSION['msg']['login-err'] = implode('<br />',$err);
		// Save the error messages in the session
    
    header("Location: index.php");
    exit;
}
else if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	$err = array();

	if(strlen($_POST['signup'])<4 || strlen($_POST['signup'])>32)
	{
		$err[]='Your username must be between 3 and 32 characters!';
	}

	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['signup']))
	{
		$err[]='Your username contains invalid characters!';
	}

	if(!checkEmail($_POST['email']))
	{
		$err[]='Your email is not valid!';
	}

	if(!count($err))
	{
		// If there are no errors
		$pass = substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)),0,6);
		// Generate a random password

		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['signup'] = mysql_real_escape_string($_POST['signup']);
		// Escape the input data

        $sql_str = "INSERT INTO users(usr,pass,email,regIP,updateDate)
					VALUES(
                        '".$_POST['signup']."',
                        '".md5($pass)."',
                        '".$_POST['email']."',
                        '".$_SERVER['REMOTE_ADDR']."',
                        '".date("Y-m-d H:i:s")."'
                    )";
		mysql_query($sql_str);

		if(mysql_affected_rows($link)==1)
		{
            $usr_id = mysql_insert_id($link);
            $sent = send_email($_POST['email'], 'Welcome, '.$_POST['signup'].'.', 'Your password is: '.$pass.'. Please change it soon.');
            if ($sent)
            {
                $_SESSION['msg']['reg-success']='<h1>We sent you an email with your new password!</h1><br /><br />';

                $_SESSION['usr']= $_POST['signup'];
                $_SESSION['id'] = $usr_id;
            }
            else
            {
                $err[]='Email failed to send!';
            }
		}
		else $err[]='This username is already taken!';
	}

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}

	header("Location: index.php");
	exit;
}

if($_SESSION['msg'])
{
	// The script below shows the sliding panel on page load
	$script .= '
	<script type="text/javascript">
	$(function(){
		$("div#panel").show();
		$("#toggle a").toggle();
        $("#ui-panel").css("z-index", 1);
	});
	</script>';
}

$script .= '
<script type="text/javascript">
    '.$script_more.'
</script>';

function getHeader()
{
    $header = "
<div id='toppanel'>
    <div id='panel'>
        <div class='content clearfix'>
            <div class='left'>
				<h1>Welcome to MTG-Decko</h1>
				<h2>Simple Deck Editor</h2>		
				<p class='grey'>This tool was made for the player who knows what cards he wants but is tired of a million clicks to put the list on his computer.</p>
				<h2>Archive/Revision History</h2>
				<p class='grey'>This tool will also let you keep your online list up-to-date while keeping a back log of what sounded good at the time.</p>
			</div>";
    if (!$_SESSION['id'])
    {
        $header .= "
			<div class='left'>
				<!-- Login Form -->
				<form class='clearfix' action='#' method='post'>
                    <h1>Member Login</h1>";
        if ($_SESSION['msg']['login-err'])
        {
            $header .= "<div class='err'>".$_SESSION['msg']['login-err']."</div>";
            unset($_SESSION['msg']['login-err']);

        }
        $header .= "
					<label class='grey' for='username'>Username:</label>
					<input class='field' type='text' name='username' id='username' value='' size='23' />
					<label class='grey' for='pwd'>Password:</label>
					<input class='field' type='password' name='pwd' id='pwd' size='23' />
	            	<label><input name='rememberme' id='rememberme' type='checkbox' checked='checked' value='forever' /> &nbsp;Remember me</label>
        			<div class='clear'></div>
					<input type='submit' name='submit' value='Login' class='bt_login' />
					<a class='lost-pwd' href='#'>Lost your password?</a>
				</form>
			</div>
			<div class='left right'>			
				<!-- Register Form -->
				<form action='#' method='post'>
					<h1>Not a member yet? Sign Up!</h1>";
        if ($_SESSION['msg']['reg-err'])
        {
            $header .= "<div class='err'>".$_SESSION['msg']['reg-err']."</div>";
            unset($_SESSION['msg']['reg-err']);
        }

        $header .= "
					<label class='grey' for='signup'>Username:</label>
					<input class='field' type='text' name='signup' id='signup' value='' size='23' />
					<label class='grey' for='email'>Email:</label>
					<input class='field' type='text' name='email' id='email' size='23' />
					<label>A password will be e-mailed to you.</label>
					<input type='submit' name='submit' value='Register' class='bt_register' />
				</form>
			</div>";
    }
    else
    {
        $header .= "
            <div class='left'>
                <h1>Members panel</h1>
                <p>You can put member-only data here</p>
                <a href='registered.php'>View a special member page</a>
                <p>- or -</p>
                <a href='?logoff'>Log off</a>
            </div>
            <div class='left right'>";

        if($_SESSION['msg']['reg-success'])
        {
            $header .= "<div class='success'>".$_SESSION['msg']['reg-success']."</div>";
            unset($_SESSION['msg']['reg-success']);
        }

        $header .= "
            </div>
			<div class='left right'>			
                <h1>Recently added deck lists</h1>
                <div id='recent_deck_lists'> </div>
			</div>";
    }

    $header .= "
		</div>
	</div> <!-- /login -->	

	<!-- The tab on top -->	
	<div class='tab'>
		<ul class='login'>
			<li class='left'>&nbsp;</li>
			<li>Hello <div id='usr_name'>" . ($_SESSION['usr'] ? $_SESSION['usr'] : 'Guest') . "</div>!</li>
			<li class='sep'>|</li>
			<li id='toggle'>
				<a id='open' class='open' href='#'>" . ($_SESSION['usr'] ? 'Account Settings' : 'Log In | Register') . "</a>
				<a id='close' style='display: none;' class='close' href='#'>Close Panel</a>			
			</li>
			<li class='right'>&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->";
    return $header;
}

?>
