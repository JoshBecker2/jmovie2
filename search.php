<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Results | JMovie</title>
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
<body onload="loadHome()">
	<div id="whiteoutContainer"></div>
	<div id="resultHeader">
		<h1 onclick="location.href='index.php'"><b style="color: #DA0037;">j</b>movie</h1>
		<form action="search.php" method="GET" id="resultForm">
			<input id="resultSearchBar" type="text" name="sq" placeholder="New search...">
			<br><button type="button" id="liveTV" onclick="toggleLiveTV()">Live TV</button>
		</form>
	</div>
	<div id="resultContainer">
		<div id="searchLoader">
			<h1>Loading titles...</h1>
		</div>
		<script>
				const urlParams = new URLSearchParams(document.location.search);
				const container = document.getElementById("resultContainer");

				query = urlParams.get("sq").trim();
				document.getElementById('resultSearchBar').value = query;
				query = query.toLowerCase();
				
				if (sessionStorage.getItem("liveTV") == "false") {
					fetch('http://server_ip:8080/https://www.themoviedb.org/search?query=' + query + '&language=en-us')
					.then(response => {
						return response.text();
					}).then(html => {
						const parser = new DOMParser();
						const doc = parser.parseFromString(html, "text/html");
						const cards = doc.getElementsByClassName('card v4 tight');
						results = [];
						for (i = 0; i < cards.length; i++) {
							try {
								link = cards.item(i).getElementsByClassName('result')[0].href;
								base = document.location.origin + "/";
								type = link.substring(base.length, link.indexOf("/", base.length + 1));
								tmdbid = link.substring(base.concat(type).length + 1, link.indexOf("-"));
								title = cards.item(i).getElementsByTagName("img")[0].alt;
								poster = cards.item(i).getElementsByTagName("img")[0].src;
								if (checkTitle(title, query))
									results.push([title, tmdbid, type, poster]);								
							} catch {
								console.error("Failed to load metadata for title");
							}
						}
						
						for (let i = 0; i < results.length; i++)
							for (let j = 0; j < results.length - 1 - i; j++)
								if (results[j][0].length > results[j + 1][0].length)
									[results[j], results[j + 1]] = [results[j + 1], results[j]];
						
						document.getElementById("searchLoader").remove();
						results.forEach(res => {
							if (res[2] == "movie")
								block = `
										<div class="movie">
											<img style="margin-bottom: 20px;" class="poster" src="` + res[3] + `" width="150px" height="225px"><br>
											<a class="movieTitle" href="player.php?id=` + res[1] + `&type=` + res[2] + `&title=` + res[0] + `&poster=` + res[3] + `"><i>` + res[0] + `</i></a>
											<br><svg style='margin-top:20px;' id='Movie_Projector_24' width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><rect width='24' height='24' stroke='none' fill='#000000' opacity='0'/><g transform="matrix(0.77 0 0 0.77 12 12)" ><path style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: rgb(237, 237, 237); fill-rule: nonzero; opacity: 1;" transform=" translate(-12.5, -13)" d="M 14 0 C 11.25 0 9 2.25 9 5 C 9 7.75 11.25 10 14 10 C 16.75 10 19 7.75 19 5 C 19 2.25 16.75 0 14 0 Z M 14 10 L 5 10 C 3.898438 10 3 10.898438 3 12 L 3 20 C 3 21.101563 3.898438 22 5 22 L 7.40625 22 L 5 26 L 7 26 L 10 22.25 L 13 26 L 15 26 L 12.59375 22 L 16 22 C 17.101563 22 18 21.101563 18 20 L 18 12 C 18 10.898438 17.101563 10 16 10 Z M 5 10 C 7.199219 10 9 8.199219 9 6 C 9 3.800781 7.199219 2 5 2 C 2.800781 2 1 3.800781 1 6 C 1 8.199219 2.800781 10 5 10 Z M 14 2 C 15.667969 2 17 3.332031 17 5 C 17 6.667969 15.667969 8 14 8 C 12.332031 8 11 6.667969 11 5 C 11 3.332031 12.332031 2 14 2 Z M 5 4 C 6.117188 4 7 4.882813 7 6 C 7 7.117188 6.117188 8 5 8 C 3.882813 8 3 7.117188 3 6 C 3 4.882813 3.882813 4 5 4 Z M 24 10 L 19 13 L 19 19 L 24 22 Z" stroke-linecap="round" /></g></svg>
										</div>
									`;	
							else if (res[2] == "tv")
								block = `
									<div class="movie">
										<img style="margin-bottom: 20px;" class="poster" src="` + res[3] + `" width="150px" height="225px"><br>
										<a class="movieTitle" href="player.php?id=` + res[1] + `&type=` + res[2] + `&title=` + res[0] + `&poster=` + res[3] + `"><i>` + res[0] + `</i></a>
										<br><svg style="margin-top:20px;" id='TV_Show_24' width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><rect width='24' height='24' stroke='none' fill='#000000' opacity='0'/><g transform="matrix(0.83 0 0 0.83 12 12)" ><path style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: rgb(237, 237, 237); fill-rule: nonzero; opacity: 1;" transform=" translate(-15, -16)" d="M 5 6 C 3.895 6 3 6.895 3 8 L 3 20 C 3 21.105 3.895 22 5 22 L 25 22 C 26.105 22 27 21.105 27 20 L 27 8 C 27 6.895 26.105 6 25 6 L 5 6 z M 13 10 C 13.204 10 13.383016 10.076641 13.541016 10.181641 L 18.546875 13.185547 C 18.813875 13.365547 19 13.654 19 14 C 19 14.346 18.813875 14.634453 18.546875 14.814453 L 13.541016 17.818359 C 13.383016 17.923359 13.204 18 13 18 C 12.448 18 12 17.552 12 17 L 12 11 C 12 10.448 12.448 10 13 10 z M 10 24 C 9.63936408342243 23.994899710454515 9.303918635428394 24.184375296169332 9.122112278513482 24.49587284971433 C 8.940305921598572 24.80737040325933 8.940305921598572 25.192629596740673 9.122112278513484 25.50412715028567 C 9.303918635428394 25.815624703830668 9.639364083422432 26.005100289545485 10 26 L 20 26 C 20.360635916577568 26.005100289545485 20.696081364571608 25.815624703830668 20.877887721486516 25.50412715028567 C 21.059694078401428 25.19262959674067 21.059694078401428 24.80737040325933 20.877887721486516 24.49587284971433 C 20.696081364571608 24.184375296169332 20.360635916577568 23.994899710454515 20 24 L 10 24 z" stroke-linecap="round" /></g></svg>
									</div>
								`;
							container.innerHTML += block;	
						});
					})
					.catch(error => {
						console.error(error);
					});
				} else {
					fetch('https://iptv-org.github.io/api/channels.json')
					.then(response => {
						return response.text();
					}).then(html => {
						results = [];
						channelIDs = [];
						response = JSON.parse(html);
						response.forEach(show => {
							if (show['name'].toLowerCase().includes(query.toLowerCase())) {
								results.push(show);
								channelIDs.push(show['id']);
							}
						});
						fetch('https://iptv-org.github.io/api/streams.json')
						.then(response => {
							return response.text();
						}).then(html => {
							output = []
							response = JSON.parse(html);
							response.forEach(show => {
								if (channelIDs.includes(show['channel']) && show['url'].includes(".m3u8")) {
									res = results[channelIDs.indexOf(show['channel'])];
									if (res['city'] == null)
										res['city'] = "Not Found!";

									output.push([res['name'], show['channel'], show['url'],
												res['city'], res['logo'], "live"]);
								}
							});

							output.forEach(res => {
								block = `
									<div class="movie">
										<img class="poster" src="` + res[4] + `" width="150px" height="150px" alt="Station Image Failed to Load!" style="padding-bottom: 30px;"><br>
										<a class="movieTitle" href="player.php?url=` + res[2] + `&type=` + res[5] + `&title=` + res[0] + `&poster=` + res[4] + `"><i>` + res[0] + `</i></a>
										<h4 style="margin-bottom: -15px"><svg id='Location_24' width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><rect width='24' height='24' stroke='none' fill='#000000' opacity='0'/><g transform="matrix(0.91 0 0 0.91 12 12)" ><path style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: rgb(237, 237, 237); fill-rule: nonzero; opacity: 1;" transform=" translate(-13, -11)" d="M 20 0 C 17.791 0 16 1.791 16 4 C 16 6.857 20 11 20 11 C 20 11 24 6.857 24 4 C 24 1.791 22.209 0 20 0 z M 12 2 C 6.486 2 2 6.486 2 12 C 2 17.514 6.486 22 12 22 C 17.514 22 22 17.514 22 12 C 22 11.93 21.991234 11.861969 21.990234 11.792969 C 21.753234 12.058969 21.557453 12.267672 21.439453 12.388672 L 19.650391 14.304688 C 19.448391 14.965687 19.169453 15.591922 18.814453 16.169922 C 18.498453 15.481922 17.806 15 17 15 L 16 15 L 16 13 C 16 12.447 15.552 12 15 12 L 9 12 L 9 10 L 10 10 C 10.552 10 11 9.553 11 9 L 11 7.0234375 L 13.015625 7.0078125 C 13.598625 7.0038125 14.117469 6.7428906 14.480469 6.3378906 C 14.314469 5.8938906 14.187516 5.445 14.103516 5 L 9.9921875 5.03125 C 9.4431875 5.03525 9 5.48225 9 6.03125 L 9 8 L 8 8 C 7.448 8 7 8.447 7 9 L 7 10.185547 L 4.9804688 8.1679688 C 6.3404688 5.6869688 8.977 4 12 4 L 14 4 C 14 3.397 14.090812 2.8165781 14.257812 2.2675781 C 13.530813 2.0985781 12.777 2 12 2 z M 20 2.5703125 C 20.789 2.5703125 21.429688 3.211 21.429688 4 C 21.429688 4.789 20.789 5.4296875 20 5.4296875 C 19.211 5.4296875 18.570312 4.789 18.570312 4 C 18.570312 3.211 19.211 2.5703125 20 2.5703125 z M 4.2070312 10.220703 L 9 15.013672 L 9 16 C 9 17.103 9.897 18 11 18 L 11 19.931641 C 7.06 19.436641 4 16.072 4 12 C 4 11.388 4.0760313 10.794703 4.2070312 10.220703 z M 10.779297 14 L 14 14 L 14 16 C 14 16.553 14.448 17 15 17 L 17 17 L 17 18.234375 C 15.875 19.138375 14.502 19.741688 13 19.929688 L 13 17 C 13 16.447 12.552 16 12 16 L 11 16 L 11 14.599609 C 11 14.377609 10.912297 14.174 10.779297 14 z" stroke-linecap="round" /></g></svg>
										` + res[3] + `<br><svg style="margin-top: 20px;" id='Youtube_Live_24' width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><rect width='24' height='24' stroke='none' fill='#000000' opacity='0'/><g transform="matrix(0.91 0 0 0.91 12 12)" ><path style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: rgb(237, 237, 237); fill-rule: nonzero; opacity: 1;" transform=" translate(-12, -12)" d="M 4.2089844 4.6796875 C 3.9353594 4.6946875 3.6670156 4.8217344 3.4785156 5.0527344 C 1.9305156 6.9487344 1 9.367 1 12 C 1 14.633 1.9305156 17.051266 3.4785156 18.947266 C 3.8565156 19.409266 4.5506562 19.451297 4.9726562 19.029297 C 5.3316562 18.670297 5.3669219 18.097078 5.0449219 17.705078 C 3.7679219 16.150078 3 14.163 3 12 C 3 9.837 3.7679219 7.850875 5.0449219 6.296875 C 5.3669219 5.904875 5.3316563 5.3316563 4.9726562 4.9726562 C 4.7616562 4.7616562 4.4826094 4.6646875 4.2089844 4.6796875 z M 19.791016 4.6796875 C 19.517391 4.6646875 19.238344 4.7616562 19.027344 4.9726562 C 18.668344 5.3316562 18.633078 5.904875 18.955078 6.296875 C 20.232078 7.850875 21 9.837 21 12 C 21 14.163 20.232078 16.149125 18.955078 17.703125 C 18.633078 18.095125 18.668344 18.668344 19.027344 19.027344 C 19.449344 19.449344 20.144484 19.410266 20.521484 18.947266 C 22.069484 17.052266 23 14.633 23 12 C 23 9.367 22.069484 6.9487344 20.521484 5.0527344 C 20.332984 4.8217344 20.064641 4.6946875 19.791016 4.6796875 z M 7.0429688 7.5234375 C 6.7655937 7.5389375 6.4968125 7.6681094 6.3203125 7.9121094 C 5.4903125 9.0631094 5 10.476 5 12 C 5 13.524 5.4903125 14.936891 6.3203125 16.087891 C 6.6733125 16.575891 7.3923594 16.607641 7.8183594 16.181641 L 7.8222656 16.177734 C 8.1732656 15.826734 8.1991094 15.281953 7.9121094 14.876953 C 7.3381094 14.062953 7 13.07 7 12 C 7 10.93 7.3381094 9.9370469 7.9121094 9.1230469 C 8.1981094 8.7170469 8.1732656 8.1732656 7.8222656 7.8222656 L 7.8183594 7.8183594 C 7.6053594 7.6053594 7.3203438 7.5079375 7.0429688 7.5234375 z M 16.957031 7.5234375 C 16.679656 7.5079375 16.394641 7.6053594 16.181641 7.8183594 L 16.177734 7.8222656 C 15.826734 8.1732656 15.800891 8.7180469 16.087891 9.1230469 C 16.661891 9.9370469 17 10.93 17 12 C 17 13.07 16.661891 14.062953 16.087891 14.876953 C 15.801891 15.282953 15.826734 15.826734 16.177734 16.177734 L 16.181641 16.181641 C 16.607641 16.607641 17.327688 16.575891 17.679688 16.087891 C 18.510687 14.937891 19 13.524 19 12 C 19 10.476 18.509688 9.0631094 17.679688 7.9121094 C 17.503188 7.6681094 17.234406 7.5389375 16.957031 7.5234375 z M 12 9 C 10.34314575050762 9 9 10.34314575050762 9 12 C 9 13.65685424949238 10.34314575050762 15 12 15 C 13.65685424949238 15 15 13.65685424949238 15 12 C 15 10.34314575050762 13.65685424949238 9 12 9 z" stroke-linecap="round" /></g></svg></h4>
									</div>
								`;
								container.innerHTML += block;	
							});

						})
						.catch(error => {
							console.error(error);
						});
					})
					.catch(error => {
						console.error(error);
					});
				}


				function checkTitle(a, b) {
					a = a.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\W/g, "").toLowerCase();
					b = b.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\W/g, "").toLowerCase();					
					return a.includes(b);
				}
		</script>
	</div>

	<script>
		function loadHome() {
			if (sessionStorage.getItem("liveTV") == "true")
				document.getElementById("liveTV").style.backgroundColor = "var(--bg2)";
			else
				document.getElementById("liveTV").style.backgroundColor = "var(--bg)";
			
			setTimeout(function effect() {
				document.getElementById("whiteoutContainer").style.backgroundColor = "transparent";
				document.getElementById("whiteoutContainer").style.zIndex = "-1";
			}, 400);
		}

		function toggleLiveTV() {
			if (sessionStorage.getItem("liveTV") == "true") {
				sessionStorage.setItem("liveTV", "false");
				document.getElementById("liveTV").style.backgroundColor = "var(--bg)";
			} else {
				sessionStorage.setItem("liveTV", "true");
				document.getElementById("liveTV").style.backgroundColor = "var(--bg2)";
			}
		}
	</script>
	<h2 style="text-align: center;">If you couldn't find what you're looking for,<br> try changing your key words.
	<span style="font-size: 15px;"><br><br><br>Help support operating costs & improvements: <a href="https://cash.app/$jmovie69" target="_blank" style="color:var(--text);">$JMOVIE69</a></span>
	</h2>
</body>
</html>