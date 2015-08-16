<?php
require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = "2nsbBRuAOZDLRzWpmNe0zes18";
$consumer_secret = "DXAPr55PXruIRQMSSMvS2Y4CE3yFCfd6t3ijp7JCCb8TOJvpub";

// Database settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twithack";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";

session_start();
ob_start();

$logged_in = false;

// Use user access tokens to authentication for posting questions or replying
if(isset($_SESSION['access_token']) && isset($_SESSION['access_token_secret'])) {
	$access_token = $_SESSION['access_token'];
	$access_token_secret = $_SESSION['access_token_secret'];
} else {
	$access_token = '';
	$access_token_secret = '';
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");

// Base URL of the site
$base_url = "http://localhost/";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>AskTwitter</title>

		<link href="bootstrap.min.css" rel="stylesheet">
		
		<!-- Include custom favicon -->
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		
		<meta charset="UTF-8">
	</head>
	
	<body>
		<!-- Navigation bar -->
      <ul class="nav nav-tabs" role="tablist" style="margin: 10px 10px">
        <li role="presentation" <?php if(!isset($_GET["yourquestions"])) echo "class=\"active\"" ?>><a href=index.php>Home</a></li>
        <li role="presentation" <?php if(isset($_GET["yourquestions"])) echo "class=\"active\"" ?>><a href="?yourquestions">Your questions</a></li>
<?php
// User login
if(!isset($_SESSION['access_token']) || !isset($_SESSION['access_token_secret'])) {
  if(isset($_GET['oauth_verifier'])) {
    $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier'], "oauth_token" => $_GET['oauth_token']));
    $_SESSION['access_token'] = $access_token['oauth_token'];
    $_SESSION['access_token_secret'] = $access_token['oauth_token_secret'];
	$_SESSION['screen_name']  = $access_token['screen_name'];
   
    print("<div class=\"alert alert-success\" role=\"alert\" style=\"position: fixed;\" top=\"60px\"><strong>WEOW!</strong> Successfully logged in as @" . $access_token['screen_name'] . ". <a href=" . $base_url . ">Click here to continue.</a></div>");
    header("Location: http://localhost/");
    exit;
  } else {
    $access_token = $connection->oauth("oauth/request_token", array("oauth_callback" => $base_url));

    $url = $connection->url("oauth/authenticate", array("oauth_token" => $access_token['oauth_token']));
    print("<p align=right>You are not logged in. <a href=\"" . $url . "\">Log in with Twitter!</a></p>");
  }
} else {
	// Handle log outs
	if(isset($_GET['logout'])) {
		session_destroy();
		//print("<p align=right>You have logged out. <a href=index.php>Return</a></p>");
		print("<div class=\"alert alert-success\" role=\"alert\" style=\"position: fixed;\" top=\"-200px\">You have logged out. <a href=index.php>Return</a></div>");
    exit;
	}

  $userscreenname = $_SESSION['screen_name'];
  //print_r($content); // DEBUG
  print("<p align=right>You are logged in as @" . $userscreenname . " <a href=\"?logout\">Log out.</a></p>");
  $logged_in = true;
}
?>
      </ul>

		<!-- Header -->
		<div class="container">
			<br />
			<br />
			<img src="AskTwitter.png">
      <h5>What's something that Google can't answer?</h5>
		</div>
  
<?php
// Handle a user posting a question.
if(isset($_GET['submitquestion'])) {
	// Post the tweet and redirect indicating success.
	$statues = $connection->post("statuses/update", array("status" => $_GET['tweet']));
	header("Location: " . $base_url . "?success");
	exit;
} 

// Display success message if a question/answer is posted.
if(isset($_GET['success'])) {
  ?>
  <div class="container">
    <div class="alert alert-success" role="alert">
      Your submission was successful!
    </div>
  </div>
  <?php
}

if(!isset($_GET['qid']) && $logged_in) {
?>
	<!-- Post a question -->
	<div class="container">
    <p><h2>Got a question?</h2></p>	  
      <!-- TODO: Use jQuery for placeholder text -->
      <form>
      <input type="text" name="tweet" style="width:80%; height:55x; font-size:24px">
      <input type="submit" name="submitquestion" class="btn btn-lg btn-info" value="Post!"></input>
	  </form>
      <p>Suggested hashtags:<br>
        <!-- TODO: Insert dynamic hashtag adding -->
        <h4>
          <a href="?q=%23cat" class="label label-default">#cat</a>
          <a href="?q=%23animals" class="label label-default">#animals</a>
          <a href="?q=%23life" class="label label-default">#life</a>
          <a href="?q=%23philosophy"  class="label label-default">#philosophy</a>
        </h4>
      </p>
    </div>
<?php
}

if(!isset($_GET['qid'])) {
?>
		<!-- Buttons for categories -->
		<nav class="navbar">
			<div class="container">
				<h1>
					<a type="button" class="btn btn-default">Popular</a>
					<a href="?q=%3F" type="button" class="btn btn-primary">New</a>
					<a href="?q=%23qkqn" class="btn btn-success">Random</a>
			 </h1>
		<!-- Search functionality -->
			<form>
  			<input type="text" name="q" style="width:40%">
  			<input type="submit" name="submit" class="btn btn-sm btn-info" value="Search"></input>
			</form>
			</div>
		</nav>
		
<?php } ?>		

<!-- Main body -->

<?php

// Handle up and down votes

 if(isset($_GET["upvote"]) && isset($_GET["voteid"])) {
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $_GET["voteid"]);
	if($result->num_rows != 1) {
		// Update the count
		$conn->query("INSERT INTO votes (postid, count, userid) VALUES (" . $_GET["voteid"] . ", 1, 0)");
	} else {
		// Increment the count on the vote
		$conn->query("UPDATE votes SET count=count+1 WHERE postid=" . $_GET["voteid"]);
	}
 }
  
 // Handle downvoting of a question
 if(isset($_GET["downvote"]) && isset($_GET["voteid"])) {
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $_GET["voteid"]);
	if($result->num_rows != 1) {
		// Update the count
		$conn->query("INSERT INTO votes (postid, count, userid) VALUES (" . $_GET["voteid"] . ", -1, 0)");
	} else {
		// Increment the count on the vote
		$conn->query("UPDATE votes SET count=count-1 WHERE postid=" . $_GET["voteid"]);
	}
 }

// Get http request data to determine if we're viewing questions or replies
if (isset($_GET['qid'])) {  
  $questionid = $_GET['qid'];
  $screename = $_GET['sname'];

  // Handle a submitted answer to a question.
  if(isset($_GET['submit'])) {
    $statues = $connection->post("statuses/update", array("status" => "@" . $screename  . " " . $_GET['tweet'], "in_reply_to_status_id" => $questionid));
    header("Location: http://localhost/index.php?qid=" . $questionid . "&sname=" . $screename . "&success");
    exit;
  }

  print("<div class=\"container\">");
  //print("<p>Viewing replies to question " . $questionid . "</p>");

  // Get the original question
  $question = $connection->get("statuses/show", array("id" => $questionid));
  
  print("<hr><b>@" . $question->user->screen_name . "</b> asked:");
  print("<p><i>" . $question->text . "</i></p>"); 
  print("<a href=http://twitter.com/" . $question->user->screen_name . "/status/" . $question->id . ">Permalink</a><hr />");	
    print("<h3>Answers</h3><br />");

  // Get and display replies to a particular question
  $replycount = 0;
  $replies = $connection->get("search/tweets", array("q" => "@" . $screename, "count"=>1000));
  foreach($replies->statuses as $reply) { 
    if($reply->in_reply_to_status_id == $questionid) {
		$replycount++;
		
		// Get the score for each tweet
		$result = $conn->query("SELECT * FROM votes WHERE postid=" . $reply->id);
		if($result->num_rows != 1) {
			$count  = 0;
		} else {
			$row = $result->fetch_assoc();
			$count = $row["count"];
		}
		
      print("<p><a href=https:twitter.com/". $reply->user->screen_name . ">@" . $reply->user->screen_name . "</a> (" . $count . " points) <a href=\"?upvote&voteid=" . $reply->id ."&qid=" . $questionid . "&sname=" . $screename . "\">+</a>/<a href=\"?downvote&voteid=" . $reply->id ."&qid=" . $questionid . "&sname=" . $screename . "\">-</a><br />" . $reply->text . "</p><hr />");
    }
  }
  
  
  if($replycount == 0) {
	print("<p>No answers so far. Be the first to answer!</p>");
  }
  // Display message if there are no replies
  //if($replycount == 0) {
    //print("There are no replies to this question yet. Post one below!");
  //}
    //print("<form><input type=\"text\" name=\"tweet\" style=\"width:80%\"><input type=\"hidden\" name=\"sname\" value=\"" . $screename . "\"><input type=\"hidden\"  name=\"qid\" value=\"" . $questionid . "\"><br />");
    //print("<input type=\"submit\" name=\"submit\"></input></form><hr />");
    //print("<button type=\"button\" class=\"btn btn-default btn-info\" style=\"margin: 5px 1px\">Submit</button>");
    //print("</form></div>");

// Handle a user posting a response.
if(isset($_GET['submit'])) {
  print("it is set?");
  // Post the tweet and redirect indicating success.
  $statues = $connection->post("statuses/update", array("status" => $_GET['tweet']));
  header("Location: " . $base_url . "?success");
  exit;
}




if(isset($_GET['qid']) && $logged_in) {
?>
  <!-- Post a response -->
  <div class="container">
    <?php
    // Display message if there are no replies
    //if($replycount == 0) {
      //print("There are no replies to this question yet. Post one below!");
    //}
    ?>
    <form><input type="text" name="tweet" style="width:80%">
    <input type="submit" name="submit" class="btn btn-sm btn-info" value="Submit"></input>
    <input type="hidden" name="sname" value="<?php echo($screename); ?>"></input>
    <input type="hidden" name="qid" value="<?php echo($questionid); ?>"></input>
    </form>
  </div>
<?php
}

} else {
  // Get query if there is one?
  if(isset($_GET['q'])) {
    $query = $_GET['q'];
  } else {
    // Default search query
    $query = '?';
  }
  
  print("<div class=\"container\"><table class=\"table table-striped\" style=\"width:100%\"><thead><tr><th>Score</th><th>Question</th><th>Username</th><th>Tags</th></tr></thead><tbody>");

  // Get top questions matching query or get user questions
  if(isset($_GET['yourquestions'])) {
	$questions = $connection->get("statuses/user_timeline", array("count"=>100));
	//print_r($questions);
  } else {
	$questions = $connection->get("search/tweets", array("q" => $query, "count"=>100))->statuses;
  }
  
  //$count = 1;
  foreach($questions as $tweet) {
	// Get the score for each tweet
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $tweet->id);
	if($result->num_rows != 1) {
		$count  = 0;
	} else {
		$row = $result->fetch_assoc();
		$count = $row["count"];
	}
		
  $isquestion = false;

    // Delimit tweets by space character.
    $words = explode(" ", $tweet->text);
    $totalWords = sizeof($words);
	//print_r($tweet);
	
	// SUPAR HACK to determine if it's a question
	foreach($words as $word) {
		// Skip hashtags
		if(substr($word, 0, 1) == "#") {
			continue;
		}
		
		$isquestion = (substr($word, -1) == "?");
	}
	
	// Skip non-questions
	if(!$isquestion) {
		continue;
	}
  
    print("<tr><td style=\"width:5%\">" . $count . " <a href=\"?upvote&voteid=" . $tweet->id . (isset($_GET['q']) ? ("&q=" . urlencode($_GET['q'])) : "") . "\">+</a>/<a href=\"?downvote&voteid=" . $tweet->id . (isset($_GET['q']) ? ("&q=" . urlencode($_GET['q'])) : "") . "\">-</a></td><td style=\"width:50%\"><a href=\"?qid=" . $tweet->id . "&sname=" . $tweet->user->screen_name . "\">" . $tweet->text . "</a> (" . time2str($tweet->created_at) .")<br />");
    //$count++;
    
    //print("<form><input type=\"text\" name=\"answer_1\" size=\"50\" margin-bottom=\"5\"><br />");
    //print("<button type=\"button\" class=\"btn btn-xs btn-info\" style=\"margin: 5px 1px\">Reply</button>");
    //print("<button type=\"button\" class=\"btn btn-xs btn-default\" style=\"margin: 5px 1px\">Clear</button></td>");
    print("<td style=\"width:10%\"><a href=\"http://twitter.com/" . $tweet->user->screen_name . "\">@" . $tweet->user->screen_name . "</a></td>");

    

    // Populate Tags column
    print("<td style=\"word-wrap: break-word; width:30%\"><h4>");
	$tagcount = 0;
	
    for($j = 0; $j < $totalWords; $j++){
	
      // Remove all special characters
      //$stripped = preg_replace('/[^a-z]/i', '', $words[$j]);
      //print($stripped . "<br />");

      // Check if the words are hashtags
      if (substr($words[$j], 0, 1) == "#"){
		$tagcount++;
		
		// Limit the number of hash tags
		if($tagcount > 3) {
			break;
		}

          //<a href="?q=%23life" class="label label-default">#life</a>
        //print("<a href=\"?q=%23" . $words[$j] . "\" class=\"label label-default\">" . $words[$j] . "</a>");
        print("<a href=\"?q=%23" . substr($words[$j], 1) . "\" class=\"label label-default\">" . $words[$j] . "</a>&nbsp;");
        //print($words[$j] . " ");
      }
    }
    print("</h4></td>");
  }
  // Close the table up
  print("</tr></tbody></table>");
}

function time2str($ts) {
    if(!ctype_digit($ts)) {
        $ts = strtotime($ts);
    }
    $diff = time() - $ts;
    if($diff == 0) {
        return 'now';
    } elseif($diff > 0) {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) { return 'Yesterday'; }
        if($day_diff < 7) { return $day_diff . ' days ago'; }
        if($day_diff < 31) { return ceil($day_diff / 7) . ' weeks ago'; }
        if($day_diff < 60) { return 'last month'; }
        return date('F Y', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 120) { return 'in a minute'; }
            if($diff < 3600) { return 'in ' . floor($diff / 60) . ' minutes'; }
            if($diff < 7200) { return 'in an hour'; }
            if($diff < 86400) { return 'in ' . floor($diff / 3600) . ' hours'; }
        }
        if($day_diff == 1) { return 'Tomorrow'; }
        if($day_diff < 4) { return date('l', $ts); }
        if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
        if(ceil($day_diff / 7) < 4) { return 'in ' . ceil($day_diff / 7) . ' weeks'; }
        if(date('n', $ts) == date('n') + 1) { return 'next month'; }
        return date('F Y', $ts);
    }
}

?>

	<div class="container">
			<p align="center">
				&copy; Team public catic void 2015
			</p>
		</div>
	</body>

</html>