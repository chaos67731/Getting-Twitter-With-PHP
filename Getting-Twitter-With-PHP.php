<?php	 		 	
$twitter_feed_html_cache = 'cache/twitter_feed.html'; 
$twitter_feed_html = NULL;
if(is_file($twitter_feed_html_cache) && filemtime($twitter_feed_html_cache) > time() - 1800) {
    $twitter_feed_html = file_get_contents($twitter_feed_html_cache);
} else {
    ob_start();
    $username = "SHAQ";
    $displayname ="SHAQ's The Man";
    $limit = 5; 
	
    // Checks if Twitter feed is good 
    $checkfeed = curl_init('http://twitter.com/'.$username);
    curl_setopt($checkfeed, CURLOPT_RETURNTRANSFER, true);
    curl_exec($checkfeed);
    if(curl_getinfo($checkfeed, CURLINFO_HTTP_CODE) == 404) {
    	$username = "God";
    	$displayname ="God";
    }
    //Gives Tweet id
    $twaddtime = 1;
    // Start Of Main Code
    // curl_close($ch); 
    $feed = 'https://api.twitter.com/1/statuses/user_timeline.rss?screen_name='.$username.'&count='.$limit;

    $displayname_fix ="<span class=\"tw_displayname\">$displayname : </span>";
    $ch = curl_init();
    $timeout = 5; // set to zero for no timeout
    curl_setopt ($ch, CURLOPT_URL, $feed );
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    $tweets = $file_contents;
    $tweet = explode("<item>", $tweets);
    $tcount = count($tweet) - 1;
    for ($i = 1; $i <= $tcount; $i++) {
    $endtweet = explode("</item>", $tweet[$i]);
    // Pull In Tweet.
    $title = explode("<title>", $endtweet[0]);
    $content = explode("</title>", $title[1]);
    $content[0] = str_replace("&#8211;", "&mdash;", $content[0]);
    $content[0] = preg_replace("/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/", '<a  title="$4" class="linksfromtwit" href="http://$2$3" target="_blank" rel="nofollow">Check Out This Link!</a>', $content[0]);
    $content[0] = str_replace("$username: ", "$displayname_fix", $content[0]);
    $content[0] = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" rel=\"nofollow\" target=\"_blank\">@\\1</a>", $content[0]);
    $content[0] = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" rel=\"nofollow\"  target=\"_blank\">#\\1</a>", $content[0]);
    $mytweets[] = $content[0];
    // Pull In Link
    $link = explode("<link>", $endtweet[0]);
    $linkcontent = explode("</link>", $link[1]);
    $linkmytweets[] = $linkcontent[0];
    // Pull In Time
    $twittime = explode("<pubDate>", $endtweet[0]);
    $timecontent = explode("</pubDate>", $twittime[1]);
    $timecontent[0] = strtotime("$timecontent[0]");
    $timemytweets[] = $timecontent[0];
    }
    // If your name has a "_" in it this will fix it for the H2
    function fix_title($username) {
      return (string)str_replace(array("_"), ' ', $username);
      break;
    }
    ?>
    <div class="twitter" id="twit">
      <div class="twit-title"> <a href="http://twitter.com/<?php	 		 	 echo $username; ?>"><?php	 		 	 echo fix_title($username); ?> Tweets</a> </div>
      <ul>
        <?php	 		 	
    // This is to set the parts you pulled in (time - tweets - links)	
    while (
    	(list(, $thetweets) = each($mytweets )) && 
    	(list(, $linktweets) = each($linkmytweets)) &&
    	(list(, $timetweets) = each($timemytweets))
     ){
     // This Has to do with showing how long ago the post was
    $interval = time()-$timetweets;
    
    $year 	= $interval/29030400;
    $month 	= $interval/2419200;
    $week 	= $interval/604800;
    $days 	= $interval/86400;
    $hour 	= $interval/3600;
    $min 	= $interval/60;
    $sec 	= $interval/1;
    
    $frac_w			= $week - floor($week);
    $frac_d			= $days - floor($days);
    $frac_h 		= $hour - floor($hour);
    $frac_m 		= $min 	- floor($min);
    
    $theweeks 	= floor ($week)			.' Weeks ';
    $thedays 	= floor ($frac_w*7)		.' Days ';
    $thehours 	= floor ($frac_d*24) 	.' Hours ';
    $themins 	= floor ($frac_h*60) 	.' Minutes ';
    $thesecs 	= floor ($frac_m*60) 	.' Seconds ';
    
     // This is for the Twitter User Image
    $userimage = 'http://img.tweetimag.es/i/'.'hcgdietteam'; // Added "hcgdietteam" to the call and not using $username due to image not showing? ? ? 
    // Adds Unique Id to each tweet
    $i = $twaddtime++;
    ?>
        <li class="tweets" id="<?php	 		 	 print 'tweet'.$i; ?>" > <img class="twitdesign" width="48" height="48" alt="<?php	 		 	 echo $username ?>Twitter Picture" title="<?php	 		 	 echo $username ?> Twitter Picture" src="https://si0.twimg.com/profile_images/1624255640/hcg_dietp_normal.jpg" /> <?php	 		 	 echo $thetweets?> <span class="timespan"> <a rel="nofollow" href="<?php	 		 	 echo $linktweets ?>" target="_blank" title="<?php	 		 	 echo $username ?> Tweet | <?php	 		 	 echo $linktweets ?>">Post:
          <?php	 		 	
    $time_agos = array(
    $thedays,
    $thehours,
    $themins /*,
    $thesecs */
    );
    foreach($time_agos as $time_ago){
    if ($theweeks<"1" && $time_ago>"1"  )
    	echo $time_ago;
    else 
    	echo "";
    
    }
    if ($theweeks>"1")
    	echo $theweeks ; /*.$thedays This is out until I stop it from Showing 0 days */
    else 
    	echo "";
    ?>
          Ago</a> </span> </li>
        <?php	 		 	 
    } 
    
    ?>
      </ul>
    </div>
    <?php	 		 	 
	$twitter_feed_html = ob_get_contents();
	ob_end_clean();
    file_put_contents($twitter_feed_html_cache, $twitter_feed_html);
}
echo $twitter_feed_html;
?>
<!-- For Some Reason The Images stopped being pulled in. :(  I hard coded it. 
src="<?php // echo $userimage ?>.jpg"
-->