<?php

function send_email($to, $subject, $body)
{
    $headers = 'From: andy@awross.me' . "\r\n" .
               'Reply-To: andy@awross.me' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    if (mail($to, $subject, wordwrap($body, 70), $headers))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function checkEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

function getRecentDecks()
{
    $my_decks = "<ul>";
    $usr = $_SESSION['usr'];
    $q = "SELECT id,name,updateDate FROM decks WHERE usr = '".$usr."' ORDER BY updateDate desc LIMIT 7";
    $result = mysql_query($q);
    while($row=mysql_fetch_array($result))
    {
        $my_decks .= "<a href='?deck_id=".$row['id']."'><li>".$row['name']." - ".$row['updateDate']."</li></a>";
    }
    $my_decks .= "</ul>";
    
    return $my_decks;
}
?>