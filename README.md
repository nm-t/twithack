# AskTwitter

Our project, <b>AskTwitter</b>, aims to provide a crowd-sourcing community that allows Twitter users to post questions and answer them, too: much like Stack Overflow or Reddit. We're using Twitter to reduce hassle for our users who already have a Twitter account; to tap into a huge goldmine of existing questions; and to get rid of any overhead with creating our own user signup service. <br />
Using PHP, HTML, CSS (Bootstrap), and MySQL, we made a web interface that is minimalistic and easy to use. Users are able to browse questions that have been posted to Twitter, and users with Twitter accounts can log in via Twitter to post their own questions and respond to questions. <br />
Logging in is fast and secure due to the use of Twitter's API, accessed through the tmhOAuth library for PHP. This is also the tool through which Twitter's database of user-submitted tweets are collected and utilised for our application.<br />
Further, users can filter tweets using a search term: either a username, or a search term (which can accept both bare terms as well as hashtags.<br /><br />

Third-party materials used:
<ul>
<li>WAMP - Apache/MySQL server for Windows</li>
<li>TwitterOAuth - Twitter API for PHP</li>
<li>Bootstrap.css</li>
</ul>
