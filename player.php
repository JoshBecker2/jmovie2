<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Player | JMovie</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="No Ads, No Virus, J Movie.">
	<meta name="keywords" content="free movies, movie, tv, free, joshbdev, no ads, ad free, free tv, live tv">
	<meta name="author" content="joshbdev.com">
	<link rel="stylesheet" type="text/css" href="index.css">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@600&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Oxygen&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Ubuntu&display=swap" rel="stylesheet">
</head>
<body>
	<div id="whiteoutContainer">
		<script type="text/javascript">
			setTimeout(function effect() {
				document.getElementById("whiteoutContainer").style.backgroundColor = "transparent";
				document.getElementById("whiteoutContainer").style.zIndex = "-1";
			}, 250);
		</script>
	</div>
	<div id="headerContainer">
		<h1 id="header" onclick="location.href='index.php'"><b style="color: #DA0037;">j</b>movie</h1>
		<form action="search.php" method="GET" id="resultForm">
			<input id="resultSearchBar" type="text" name="sq" placeholder="New search...">
		</form>
	</div>
	<div id="playerContainer">
		<div id="player">
			<div id="loader">
				<h1 id="mainMessage">Loading your <i>ad-free</i> experience...</h1>
				<h2 id="smallMessage">Please be patient!</h2>
			</div>
			<video style="display:none;" id="video" width="0px" height="0px" autoplay="true" controls onplay="addHistory()"></video>
		</div>
		<div id="videoData">
			<h2>Now playing: <i id="playerDispTitle"></i></h2>
			<h2 id="season"><y>Season - <x><input type="text" id="seasonSelect" value="1" /></x></y></h2>
			<h2 id="episode"><y>Episode - <x><input type="text" id="episodeSelect" value="1" /></x></y></h2>
			<button type="button" id="videoLoader" onclick="changeVid()">Play!</button>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
		<script>
			const urlParams = new URLSearchParams(document.location.search);
			document.getElementById("playerDispTitle").innerHTML = urlParams.get("title");
			document.getElementById("resultSearchBar").value = urlParams.get("title");
			if (urlParams.get("type") != "tv") {
				document.getElementById("season").remove();
				document.getElementById("episode").remove();
				document.getElementById("videoLoader").remove();
				loadVid(urlParams.get("type"), 0, 0);
			} else {
				loadVid(urlParams.get("type"), 1, 1);
			}
			
			async function changeVid() {
				loadVid(urlParams.get("type"), document.getElementById("seasonSelect").value,
				document.getElementById("episodeSelect").value);
			}

			async function loadVid(type, s, e) {
				document.getElementById('loader').style.display = "block";
				document.getElementById('loader').style.boxShadow = "";
				document.getElementById('video').style.display = "none";
				if (Hls.isSupported()) {
					var video = document.getElementById('video');
					var hls = new Hls();
					const tmdbid = urlParams.get("id");
					const m3u8 = await getm3u8(tmdbid, type, s, e);
					hls.loadSource(m3u8);
					document.getElementById('loader').style.display = "none";
					video.style.display = "inline";
					hls.attachMedia(video);
					hls.on(Hls.Events.MANIFEST_PARSED, function() {
						video.play();
					});
				} else {
					document.getElementById('mainMessage').innerHTML = "HLS Media Support Not Found!";
					document.getElementById('smallMessage').innerHTML = "Try again with a browser that supports the necessary scripts.<br><br>Sorry!";
					document.getElementById('loader').style.boxShadow = "0px 0px 15px 8px var(--red)";
					document.getElementById('video').remove();
				}
			}

			async function getm3u8(tmdbid, type, s, e) {
				if (type == "live") // dont you fucking dare. i will find you.
					return "http://serverip:8080/" + urlParams.get("url");
				else if (type == "movie")
					ENDPOINT = "http://serverip:5000/api/movie/" + tmdbid;
				else if (type == "tv")
					ENDPOINT = "http://serverip:5000/api/show/" + s + "/" + e +"/"+tmdbid;
				try {
					const call = await fetch(ENDPOINT);
					if (!call.ok) {
						document.getElementById('mainMessage').innerHTML = "Failed to get the video!";
						document.getElementById('smallMessage').innerHTML = "Usually refreshing works, if not, try coming back again later.<br><br>Sorry!";
						document.getElementById('loader').style.boxShadow = "0px 0px 15px 8px var(--red)";
						document.getElementById('video').remove();
						console.error("Fetch returned with an error or: " + call.error);
					} else {   
						const response = await call.json();
						return response;
					}
				} catch (err) {
					document.getElementById('mainMessage').innerHTML = "Failed to get the video!";
					document.getElementById('smallMessage').innerHTML = "Usually refreshing works, if not, try coming back again later.<br><br>Sorry!";
					document.getElementById('loader').style.boxShadow = "0px 0px 15px 8px var(--red)";
					document.getElementById('video').remove();
					console.error("Fetch returned with an error of: " + err.message);
				}
			}

			function addHistory() {
				type = urlParams.get("type");
				if (type == "live")
					id = urlParams.get("url");
				else
					id = urlParams.get("id");

				newView = [urlParams.get("title"),urlParams.get("poster"),urlParams.get("type"),id];
				viewed = localStorage.getItem("viewed");
				if (viewed == null)
					localStorage.setItem("viewed", JSON.stringify([newView]));
				else {
					his = JSON.parse(viewed);
					seen = false;
					his.forEach(item => {
						if (item[2] == newView[2] && item[3] == newView[3]) {
							his.splice(his.indexOf(item), 1);
						}
					});
					if (his.length == 10) {
						his.reverse();
						his.pop();
						his.reverse();
					}
					his.push(newView);
					localStorage.setItem("viewed", JSON.stringify(his));
				}
			}
		</script>
	</div>
</body>
</html>