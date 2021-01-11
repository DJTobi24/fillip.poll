<?php

$error=array();
define('DIR_APP', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SMOOTH_POLL', str_replace('\'', '/', realpath(DIR_APP . '../')) . '/');

if (phpversion() < '5.0') {
			$error['warning'] = 'Deine PHP Version muss über 5 liegen!';
		}
	
		if (ini_get('session.auto_start')) {
			$error['warning'] = 'Die App wird nicht mit session.auto_start = enabled funktionieren!';
		}

		if (!extension_loaded('mysql')) {
			$error['warning'] = ' MySQL extension muss aktiv sein!';
		}
		
		if (!is_writable(DIR_SMOOTH_POLL . 'includes/pollDbAccess.php')) {
			$error['warning'] = 'Warning: pollDbAccess.php needs to be writable for Smooth AJAX Poll to be installed !';
		}

?>

<div id="gCtr">
<h1 style="background: url('image/installation.png') no-repeat;">Step 2 - Pre-Installation</h1>
<div style="width: 100%; display: inline-block;">
  <div style="float: left; width: 569px;">
    <?php if (isset($error['warning'])) { ?>
    <div id="warning" class="warning"><?php echo $error['warning']; ?></div>
    <?php } ?>
      <p>1. Please configure your PHP settings to match requirements listed below.</p>
      <div class="inner">
        <table width="100%">
          <tr>
            <th width="35%" align="left"><b>PHP Settings</b></th>
            <th width="25%" align="left"><b>Current Settings</b></th>
            <th width="25%" align="left"><b>Required Settings</b></th>
            <th width="15%" align="center"><b>Status</b></th>
          </tr>
          <tr>
            <td>PHP Version:</td>
            <td><?php echo phpversion(); ?></td>
            <td>5.0+</td>
            <td align="center"><?php echo (phpversion() >= '5.0') ? '<img src="image/good.png" alt="Good" />' : '<img src="image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>Register Globals:</td>
            <td><?php echo (ini_get('register_globals')) ? 'On' : 'Off'; ?></td>
            <td>Off</td>
            <td align="center"><?php echo (!ini_get('register_globals')) ? '<img src="image/good.png" alt="Good" />' : '<img src="image/bad.png" alt="Bad" />'; ?></td>
          </tr>
         
          <tr>
            <td>Session Auto Start:</td>
            <td><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
            <td>Off</td>
            <td align="center"><?php echo (!ini_get('session_auto_start')) ? '<img src="image/good.png" alt="Good" />' : '<img src="image/bad.png" alt="Bad" />'; ?></td>
          </tr>
        </table>
      </div>
      <p>2. Please make sure the extensions listed below are installed.</p>
      <div class="inner">
        <table width="100%">
          <tr>
            <th width="35%" align="left"><b>Extension</b></th>
            <th width="25%" align="left"><b>Current Settings</b></th>
            <th width="25%" align="left"><b>Required Settings</b></th>
            <th width="15%" align="center"><b>Status</b></th>
          </tr>
          <tr>
            <td>MySQL:</td>
            <td><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('mysql') ? '<img src="image/good.png" alt="Good" />' : '<img src="image/bad.png" alt="Bad" />'; ?></td>
          </tr>
           <tr>
            <td>MySQLi:</td>
            <td><?php echo extension_loaded('mysqli') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('mysqli') ? '<img src="image/good.png" alt="Good" />' : '<img src="image/bad.png" alt="Bad" />'; ?></td>
          </tr>
        </table>
      </div>
      <p>3. Please make sure you have set the correct permissions on the files list below.</p>
      <div class="inner">
        <table width="100%">
          <tr>
            <th align="left"><b>Files</b></th>
            <th width="15%" align="left"><b>Status</b></th>
          </tr>
           <tr>
            <td><?php echo DIR_SMOOTH_POLL . 'includes/pollDbAccess.php'; ?></td>
            <td><?php echo is_writable(DIR_SMOOTH_POLL . 'includes/pollDbAccess.php') ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
        </table>
      </div>
      <div style="text-align: right;"><a id="stp2" class="button"></a></div>
  </div>
  <div id="sidebar">
    <ul>
      <li><del>Welcome</del></li>
      <li><b>Pre-Installation</b></li>
      <li>Configuration</li>
      <li>Finished</li>
    </ul>
  </div>
</div>
</div>