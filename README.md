# jmovie2
<h1>JMovie: No Ads No Viruses, J Movie</h1>
The new and improved version of JMovie: the no ad, no virus video streamer. In addition to directly retrieving the m3u8 links from the player, I also used <a href="https://github.com/iptv-org">IPTV</a> to include live TV stations! The previous version scraped URLs from popular streaming websites' players (which would either move or contain viruses themselves). This is a better, safer version- despite the time cost of actually loading the page on the back-end and delivering the source to you.  

This project uses a low-cost DigitalOcean droplet running main.py as well as a <a href="https://github.com/Rob--W/cors-anywherehttps://github.com/Rob--W/cors-anywhere">cors-anywhere proxy.</a> The server is configured to serve only a specific origin- which is my layer of security for this project. JMovie is not HTTPS, which is definitely an area I can spend some time on to get working, but that really just came down to monetary cost. 

DCMA? Never heard of her! All of my content is sourced from <a href="https://vidlink.pro">vidlink.pro</a> so take it up with them :)
