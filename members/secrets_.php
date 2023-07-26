<?php
/* Fill in all these details to get your system up and running. Rename this file to secrets_.php */
/* Sample Values Filled */
$OrgName = 'Cehpoint';
$servername = 'localhost'; //Name of server
$username = 'root'; // User name for the database
$password = 'admin'; //Password for the database
$MainDB = 'db_cms'; //Name of the main database where all CMS information is stored (login, certificates, event information)
$formDB = "db_forms"; //Name of the database for recording generated form details
$mailerDB = "db_mailer"; //Name of the mailer database
$startPath = ''; //Leave it blank if the files are in the root location of your web server else fill in with the location

/* Mailer Details */
/* I MADE CHANGES IN THIS SECTION TO MAKE IT WORK */
$mailerHostname = "ssl://smtppro.zoho.in"; //Add hostname of the mail server. eg: smtppro.zoho.in, ssl://smtp.gmail.com
$mailerUname = "jit@cehpoint.co.in"; //Username of the sender account. eg: jit@cehpoint.co.in
$mailerPassword = "mail_password"; //Password of the sender mail account

/* Default mail template details */
$buttonLabel = "Click here"; //Label that is present in button
$buttonURL = "https://sample.org/"; //URL that button directs too
$logoURL = "https://sample.org/logo.png"; //URL for organisation logo in mail
$coverURL = "https://sample.org/cover.jpeg"; //URL for organisation cover page in mail

/* Forgot Password section details */
$hostName = "localhost/domain/"; //Host name for local testing
// $hostName = "sample.host.name/"; //Host name for deployment
$forgotPwdExtension = "members/change-password.php"; //Path to change password page

/* Forgot Password Mail Template details */
$reachEmail = 'sample@sample.host.name'; //contact mail-id present in the forgot password email
$darkLogo = 'https://sample.logo.path'; //Path for organisation logo in forgot password mail
$logoHREF = "htpps://sample.org"; //Website that clicking on organisation logo leads to

/* API Keys */
$shortcm_authorization = "ABCDEFABCDEFABCDEFABCDEF"; // Visit https://short.io/features/api for more details
$shortcm_domain = "sample.domain"; // Domain for generating the shortened URL
?>