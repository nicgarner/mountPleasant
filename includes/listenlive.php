<?php
	$streamingurl = file_get_contents("http://localhost:8000/on-off.xsl", NULL, NULL, 0, 200); //Read a maximum of 200 characters
	$streamingurl = str_replace("\n", "", str_replace("\r", "", $streamingurl));
?>

<style>
	.offline
	{
		color: #f00;
	}
	.online
	{
		color: #0a0;
	}
</style>

<?php
	if (strpos($streamingurl, "none") === FALSE)
	{
		$streamingurl = "http://www.mountpleasantchurch.com/stream/stream";
?>
		<p><strong>Listen Live is currently: <span class="online">Online</span></strong></p>
        <p>Click the play button below to start listening. To use an external player so that you 
        	can continue browsing while you listen, click the link underneath.</p>
		<script src="jwplayer/jwplayer.js"></script>
        <noscript><p class="error">The Listen Live feature requires JavaScript in your browser.</p></noscript>
        <p><div id="matplayer"><script>document.write("Loading...");</script></div></p>
		<script>
			jwplayer("matplayer").setup({
				flashplayer: "/jwplayer/player.swf",
				file: "<?=$streamingurl?>",
				provider: "sound",
				controlbar: "bottom",
				height: 24,
				width: 300,
				bufferlength: "2",
				streamer: ""
			});
		</script>
		
	<p><a href="http://www.mountpleasantchurch.com/stream.m3u">Play in an external player</a> | <a href="http://www.mountpleasantchurch.com/stream/stream">Stream for mobile devices</a></p>
<?php
	}
	else
	{
?>
		<p><strong>Listen Live is currently: <span class="offline">Offline</span></strong></p>
        <p>&nbsp;</p>
        
<?php
	}
?>
