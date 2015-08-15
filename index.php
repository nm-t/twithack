
<!DOCTYPE html>
<html>
	<head>
		<title>TwitHack 2015</title>

		<link href="bootstrap.min.css" rel="stylesheet">
	</head>
	
	<body>

		<!-- Navigation bar -->
		<nav class="navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">TwitHack</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

		<!-- Header -->
		<div class="container">
			<p align="center">
				<img src="TwitHackTemp.png" width=50% height=50%>
			</p>
		</div>

    <!-- Post a question -->
    <div class="container">
      <p><h2>Got a question?</h2></p>
      
      <!-- TODO: Use jQuery for placeholder text -->
      <form>
      <input type="text" name="answer_1" style="width:80%">
      <button type="button" class="btn btn-sm btn-info">Post!</button>
      <p>Suggested hashtags:<br>
        <!-- TODO: Insert dynamic hashtag adding -->
        <h4>
          <span class="label label-default">#cat</span>
          <span class="label label-default">#animals</span>
          <span class="label label-default">#life</span>
          <span class="label label-default">#philosophy</span>
        </h4>
      </p>
    </div>
		
		<!-- Buttons for categories -->
		<nav class="navbar">
			<div class="container">
				<h1>
					<button type="button" class="btn btn-default">Popular</button>
					<button type="button" class="btn btn-primary">New</button>
					<button type="button" class="btn btn-success">Random</button>
					<button type="button" class="btn btn-info">Default</button>
			</h1>
			</div>
		</nav>

		<!-- Main body -->
		<div class="container">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Question</th>
                <th>Username</th>
                <th>Tags</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td><p>Why is the sky blue?
                  <?php
                	echo "pisse";
                	?>
                	</p>
					<form>
					<input type="text" name="answer_1" size="50" margin-bottom="5">
					<br>
                	<button type="button" class="btn btn-xs btn-info" style="margin: 5px 1px">Reply</button>
                  <button type="button" class="btn btn-xs btn-default" style="margin: 5px 1px">Clear</button>
                </td>
                <td>@ThomasEngine
                  <!-- TODO: Add "official" tick thing -->
                </td>
                <td>
                  <h4>
                  <span class="label label-default">#sky</span>
                  <span class="label label-default">#blue</span>
                  </h4>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td><p>What's on in the city today?</p>
          <form>
          <input type="text" name="answer_1" size="50" margin-bottom="5"><br>
                	<button type="button" class="btn btn-xs btn-info" style="margin: 5px 1px">Reply</button>
                  <button type="button" class="btn btn-xs btn-default" style="margin: 5px 1px">Clear</button>
                </td>
                <td>@smallcat</td>
                <td>
                  <h4>
                  <span class="label label-default">#city</span>
                  <span class="label label-default">#melbourne</span>
                  </h4></td>
              </tr>
              <tr>
                <td>3</td>
                <td><p>Who was the band that was just on on stage 3? #soundwave</p>
          <form>
          <input type="text" name="answer_1" size="50" margin-bottom="5"><br>
                	<button type="button" class="btn btn-xs btn-info" style="margin: 5px 1px">Reply</button>
                  <button type="button" class="btn btn-xs btn-default" style="margin: 5px 1px">Clear</button>
                </td>
                </td>
                <td>@happybee</td>
                <td>
                  <h4>
                  <span class="label label-default">#soundwave</span>
                  <span class="label label-default">#music</span>
                  </h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
	</body>

</html>