
<?php
error_reporting(0);
$db_config_path = '../config/config.inc.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST) {
    
    require_once('taskCoreClass.php');
    require_once('includes/databaseLibrary.php');

    $core = new Core();
    $database = new Database();

    if(!empty($_POST['hostname']) && !empty($_POST['username']) && !empty($_POST['database'])){
        if($database->create_database($_POST) == false)
        {
            $message = $core->show_message('error',"The database could not be created, make sure your the host, username, password, database name is correct.");
        }else if ($core->write_config($_POST) == false)
        {
            $message = $core->show_message('error',"The database configuration file could not be written, please chmod application/config/database.php file to 777");
        }
        else if ($database->create_tables($_POST) == false)
        {
            $message = $core->show_message('error',"The database could not be created, make sure your the host, username, password, database name is correct.");
        } 
        else if ($core->checkFile() == false)
        {
            $message = $core->show_message('error',"File application/config/database.php is Empty");
        }
         
        if(!isset($message)) {
            $core->delete_directory('../install/');
            // header( 'Location: ' . $urlWb ) ;
            $type = 'success';
			$message = $core->show_message('success','Congrats! Installation is successful. Please wait redirecting you to the main page in seconds.. .');
			header( 'Refresh:5; url=../index.php' );
        }
    }else{
        $message = $core->show_message('error','The host, username, password, database name required.');
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Welcome to Installer</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container">
        <div class="col-md-4 col-md-offset-4">
            <h1>Umfragen Installer</h1>
            <hr>
            <?php 
            if(is_writable($db_config_path))
            {
            ?>
                <?php if(isset($message)) {
                    if( isset($type) && $type == 'success' ){
                        echo '
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        ' . $message . '
                        </div>';
                    }else{
                        echo '
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        ' . $message . '
                        </div>';
                    }
                }
                ?>
                
                <form id="install_form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="hostname">Database Hostname</label>
                    <input type="text" id="hostname" value="localhost" class="form-control" name="hostname" />
                    <p class="help-block">Your Hostname.</p>
                </div>
                
                <div class="form-group">
                    <label for="username">Database Username</label>
                    <input type="text" id="username" class="form-control" name="username" />
                    <p class="help-block">Your Username.</p>
                </div>
                
                <div class="form-group">
                    <label for="password">Database Password</label>
                    <input type="password" id="password" class="form-control" name="password" />
                    <p class="help-block">Your Password.</p>
                </div>
                
                <div class="form-group">
                    <label for="database">Database Name</label>
                    <input type="text" id="database" class="form-control" name="database" />
                    <p class="help-block">Your Database Name.</p>
                </div>
             
                <input type="submit" value="Install" class="btn btn-primary btn-block" id="submit" />
                </form>
        
                <?php 
                } 
                else {
                ?>
                <p class="alert alert-danger">
                    Die Datei config/config.inc.php muss beschreibbar sein.<br>
                    <strong>Zum Beispiel</strong>:<br />
                    <code>chmod 777 config/config.inc.php</code>
                    </p>
                <?php 
                } 
                ?>
            </div>
            
      </div>
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>
