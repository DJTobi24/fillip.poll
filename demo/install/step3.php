<?php 
$error=0;
$vals=array('','','','','','');
	if(isset($_POST['e'])){
		$errors=$_POST['e'];
		if(!empty($errors)){
		  $error=1;	
		}
		$replaced=array('Array','[db_host]','[db_user]','[db_name]','[username]','[password]','[warning]','1','(',')','Fatal Error');
		$errors=str_replace($replaced,'',$errors);
		$vars=explode('@>#__!',$errors);
		$errors=explode('=>',$vars[0]);
		$errs='';
		$count=count($errors);
    if ($count == 1) {
        if (strstr(strtolower($_POST['e']), "duplicate")) {
            $errs.='<li> it seems like the database you entered is not empty, the Smooth Ajax Poll require an empty database</li>';
        }else{
          $errs.='<li>'.$_POST['e'].'</li>';
        }
    }else{
  		for($i=1;$i<$count;$i++){
  			$errs.='<li>'.$errors[$i].'</li>';
  		}
    }
		$errs='Please fix those errors to continue :<ul>'.$errs.'</ul>';
    if (isset($vars[1])) {
		  $vals=explode('*_@#/',$vars[1]);
    }
	}
	$chk='checked="checked"';
 ?>
 <span id="loadin" style="display:none;">Loading <img src="image/loading.gif" /></span>
 <h1 style="background: url('image/configuration.png') no-repeat;">Step 3 - Configuration</h1>
<div style="width: 100%; display: inline-block;">
  <div style="float: left; width: 569px;">
    <?php if ($error) { ?>
    <div class="warning"><?php echo $errs; ?></div>
    <?php } ?>
    <form action="#" method="post" enctype="multipart/form-data" id="form3">
      
      <p>1 . Please enter your database connection details.</p>
      <div class="inner">
        <table>
          <tr>
            <td width="185"><span class="required">*</span>Database Host:</td>
            <td><input type="text" name="db_host" value="<?php echo $vals[0]; ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span>User:</td>
            <td><input type="text" name="db_user" value="<?php echo $vals[1];   ?>" /></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td><input type="text" name="db_password" value="<?php echo $vals[2];   ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span>Database Name:</td>
            <td><input type="text" name="db_name" value="<?php echo $vals[3];    ?>" /></td>
          </tr>
          <tr>
            <td>Database Prefix:</td>
            <td><input type="text" name="db_prefix" value="<?php echo $vals[4];    ?>" /></td>
          </tr>
        </table>
      </div>
      
      <div style="text-align: right;"><a id="stp3" class="button"></a></div>
    </form>
  </div>
  <div id="sidebar">
    <ul>
      <li><del>Welcome</del></li>
      <li><del>Pre-Installation</del></li>
      <li><b>Configuration</b></li>
      <li>Finished</li>
    </ul>
  </div>
</div>