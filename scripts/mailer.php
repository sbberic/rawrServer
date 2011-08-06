<? 

 
class Mailer
{
   /**
    * sendWelcome - Sends a welcome message to the newly
    * registered user, also supplying the username and
    * password.
    */
   function sendWelcome($user, $email, $pass){
      $from = "From: justin";
      $subject = "rawr!";
      $body = $user.",\n\n"
             ."You're now a confirmed user!\n"
             ."Here's your info:\n\n"
             ."Username: ".$email."\n"
             ."Password: ".$pass."\n\n"
             ."To use your real name and see which of your friends are also using "
             ."rawr, connect to facebook under the profile tab!"
             ."\nhappy posting!"
             ."\n\n"
             ."- Justin";

      return mail($email,$subject,$body,$from);
   }
   
   /**
    * sendConfirm - Sends the confirmation email 
    * with a link and no contact info in case of accidental incorrect data
    * includes confirm code to confirm.php
    */
   function sendConfirm($user, $confirm,$email){
      $from = "From: justin";
      $subject = "rawr!";
      $body = $user.",\n\n"
             ."Thanks for signing up with rawr!\n\n "
             ."Please click the link below to confirm this email and your account.\n"
             ."http://50.56.33.203/scripts/confirm.php?code=".$confirm."\n\n"
             ."If you did not sign up for rawr, do not click the link and ignore this email."
             ."\n\n"
             ."- Justin";

      return mail($email,$subject,$body,$from);
   }
   
   /**
    * sendNewPass - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendNewPass($user, $email, $pass){
      $from = "From: Justin";
      $subject = "rawr - Your new password";
      $body = $user.",\n\n"
             ."This is your password "
             ."dont forget :P\n\n"
             
             ."Username: ".$email."\n"
             ."Password: ".$pass."\n\n"
             ."\n\n"
             ."- Justin";
             
      return mail($email,$subject,$body,$from);
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
 
?>
