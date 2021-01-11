<?php
include ('config/Poll.php');
$poll = new Poll();
$pollData = $poll->getPoll();
?>
<form action="" method="post" name="pollForm">
<?php
foreach($pollData as $poll){
echo "<h3>".$poll['question']."</h3>";
$pollOptions = explode("||||", $poll['options']);
echo "<ul>";
for( $i = 0; $i < count($pollOptions); $i++ ) {
echo '<li><input type="radio" name="options" value="'.$i.'" > '.$pollOptions[$i].'</li>';
}
echo "</ul>";
echo '<input type="hidden" name="pollid" value="'.$poll['pollid'].'">';
echo '<br><input type="submit" name="vote" class="btn btn-primary" value="Vote">';
echo '<a href="results.php?pollID="'.$poll['pollid']."> View Results</a>";
}
?>
</form>