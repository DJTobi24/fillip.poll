<!DOCTYPE html>
<html>
  <head>
    <title>Umfrage ERstellen</title>
    <link rel="stylesheet" href="3b-vote.css"/>
  </head>
  <body>
    <?php
    // (A) USE POLL ID
    require "2-poll.php";
    $pid = 1; // Poll ID fixed to 1 for this demo
    $uid = 1; // User ID fixed to 1 for this demo

    // (B) SAVE POLL VOTE
    if (isset($_POST['vote'])) {
      echo $POLL->vote($uid, $pid, $_POST['vote'])
        ? "<div class='notebar'>Vote Saved</div>"
        : "<div class='notebar'>$POLL->error</div>";
    }

    // (C) GET POLL & SHOW
    $poll = $POLL->get($pid, $uid); ?>
    <div id="pollWRAP">
      <div id="pollQN"><?=$poll['q']?></div>
      <form id="pollOPT" method="post">
        <?php foreach ($poll['o'] as $oid=>$o) { ?>
        <label>
          <span class="votes"><?=$poll['v'][$oid]?></span>
          <input type="radio" name="vote" value="<?=$oid?>" 
                 onchange="document.getElementById('pollOPT').submit()"
                 <?=($oid==$poll['u']) ? "checked " : ""?>>
          <?=$o?>
        </label>
        <?php } ?>
      </form>
    </div>
  </body>
</html>