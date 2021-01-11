<?php
class Poll {
  // (A) Datenbankverbinung via PDO
  private $pdo = null;
  private $stmt = null;
  public $error = "";
  function __construct(){
    try {
      $this->pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, 
        DB_USER, DB_PASSWORD, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  // (B) Datenbank Schließen
  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  // (C) Frage Speichern
  function save ($question, $options, $pid=null){
    // (C1) AUTO COMMIT OFF
    $this->pdo->beginTransaction();
    $pass = true;

    // (C2) POLL QUESTION
    if ($pid===null) {
      $sql = "INSERT INTO `poll_questions` (`poll_question`) VALUES (?)";
      $data = [$question];
    } else {
      $sql = "UPDATE `poll_questions` SET `poll_question`=? WHERE `poll_id`=?";
      $data = [$question, $pid];
    }
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
    } catch (Exception $ex) {
      $pass = false;
      $this->error = $ex->getMessage();
    }

    // (C3) GET POLL ID FOR NEW ENTRY, DELETE OLD OPTIONS FOR EDIT
    if ($pass) {
      if ($pid===null) {
        $pid = $this->pdo->lastInsertId();
      } else {
        try {
          $this->stmt = $this->pdo->prepare("DELETE FROM `poll_options` WHERE `poll_id`=?");
          $this->stmt->execute([$pid]);
        } catch (Exception $ex) {
          $pass = false;
          $this->error = $ex->getMessage();
        }
      }
    }

    // (C4) ADD OPTIONS
    if ($pass) {
      $sql = "INSERT INTO `poll_options` (`poll_id`, `option_id`, `option_text`) VALUES ";
      $data = [];
      foreach ($options as $i=>$o) {
        $sql .= "(?,?,?),";
        $data[] = $pid; $data[] = $i + 1; $data[] = $o;
      }
      $sql = substr($sql, 0, -1) . ";";
      try {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($data);
      } catch (Exception $ex) {
        $pass = false;
        $this->error = $ex->getMessage();
      }
    }

    // (C5) RESULTS
    if ($pass) { $this->pdo->commit(); }
    else { $this->pdo->rollBack(); }
    return $pass;
  }

  // (D) DELETE POLL
  function del ($pid) {
    // (D1) AUTO COMMIT OFF
    $this->pdo->beginTransaction();
    $pass = true;

    // (D2) REMOVE VOTES + OPTIONS + QUESTIONS
    $sequence = [
      "DELETE FROM `poll_votes` WHERE `poll_id`=?",
      "DELETE FROM `poll_options` WHERE `poll_id`=?",
      "DELETE FROM `poll_questions` WHERE `poll_id`=?"
    ];
    foreach ($sequence as $sql) {
      try {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute([$pid]);
      } catch (Exception $ex) {
        $pass = false;
        $this->error = $ex->getMessage();
      }
      if (!$pass) { break; }
    }

    // (D3) RESULTS
    if ($pass) { $this->pdo->commit(); }
    else { $this->pdo->rollBack(); }
    return $pass;
  }

  // (E) GET POLL
  function get ($pid, $uid=null) {
    // (E1) GET POLL QUESTION
    $poll = ["q"=>"", "o"=>[], "v"=>[], "u"=>null];
    $this->stmt = $this->pdo->prepare("SELECT * FROM `poll_questions` WHERE `poll_id`=?");
    $this->stmt->execute([$pid]);
    while ($row = $this->stmt->fetch()) { $poll['q'] = $row['poll_question']; }

    // (E2) GET POLL OPTIONS
    $this->stmt = $this->pdo->prepare("SELECT * FROM `poll_options` WHERE `poll_id`=?");
    $this->stmt->execute([$pid]);
    while ($row = $this->stmt->fetch()) { $poll['o'][$row['option_id']] = $row['option_text']; }

    // (E3) GET VOTES
    $this->stmt = $this->pdo->prepare(
      "SELECT COUNT(`option_id`) `votes`, `option_id` FROM `poll_votes` WHERE `poll_id`=? GROUP BY `option_id`"
    );
    $this->stmt->execute([$pid]);
    while ($row = $this->stmt->fetch()) { $poll['v'][$row['option_id']] = $row['votes']; }

    // (E4) ALSO GET USER VOTE IF SPECIFIED
    if ($uid !== null) {
      $this->stmt = $this->pdo->prepare("SELECT * FROM `poll_votes` WHERE `poll_id`=? AND `user_id`=?");
      $this->stmt->execute([$pid, $uid]);
      while ($row = $this->stmt->fetch()) { $poll['u'] = $row['option_id']; }
    }

    // (E5) RETURN RESULTS
    /* $poll = [
     *   "q" => QUESTION,
     *   "o" => [OPTION ID => OPTION NAME],
     *   "v" => [OPTION ID => TOTAL VOTES],
     *   "u" => USER VOTED OPTION ID (IF $UID SPECIFIED)
     * ]
     */
    return $poll;
  }

  // (F) SAVE USER VOTE
  function vote ($uid, $pid, $oid=null) {
    // (F1) SQL - DELETE OR REPLACE
    if ($oid===null) {
      $sql = "DELETE FROM `poll_votes` WHERE `user_id`=? AND `poll_id`=?";
      $data = [$uid, $pid];
    } else {
      $sql = "REPLACE INTO `poll_votes` (`user_id`, `poll_id`, `option_id`) VALUES (?,?,?)";
      $data = [$uid, $pid, $oid];
    }

    // (F2) RUN
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
    } catch (Exception $ex) {
      $pass = false;
      $this->error = $ex->getMessage();
    }
    return true;
  }
}

// (G) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// (H) NEW POLL OBJECT
$POLL = new Poll();