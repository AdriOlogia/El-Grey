<?php
// The secret token to add as a GitHub or GitLab secret, or otherwise as https://www.example.com/?token=secret-token
define("TOKEN", "");
// The SSH URL to your repository
define("REMOTE_REPOSITORY", "https://gitlab.com/five-media/ombu-fintech/ombutech.git");
// The path to your repostiroy; this must begin with a forward slash (/)
define("DIR", "c:\\Ombu_wamp64\\www\\ombutech");
// The branch route
define("BRANCH", "refs/heads/prod");
// The name of the file you want to log to.
define("LOGFILE", "deploy.log");
// The path to the git executable
define("GIT", "git.exe");
// A command to execute before pulling
define("BEFORE_PULL", "");
// define("BEFORE_PULL", "drush cr");
// A command to execute after successfully pulling
define("AFTER_PULL", "");
// define("AFTER_PULL", "drush fra -y --bundle=delocos && drush cr");

require_once(DIR_BASE."deployer.php");
?>