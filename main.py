from flask import Flask, jsonify, request
from playwright.sync_api import sync_playwright
from time import time, sleep
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# return True if the referrer is allowed
def checkReferrer(ref):
        allowedDomains = ["http://hvghgvbnk.42web.io/"]
        if ref not in allowedDomains:
                return False
        return True

def getSource(url):
        m3u8 = ""
        with sync_playwright() as pw:
                browser = pw.firefox.launch()
                page = browser.new_page()
                page.set_default_timeout(5000)
                page.goto(url)
                sleep(4)
                try:
                        content = page.content()
                        start = content.index("<source src=\"") + len("<source src=\"")
                        end = content.index("\"", start + 1)
                        m3u8 = content[start:end]
                except Exception as e:
                        page.close()
                        browser.close()
                        return None
                page.close()
                browser.close()
        return m3u8

@app.route("/api/movie/<tmdbid>", methods=["GET"])
def movie(tmdbid):
        if not checkReferrer(request.referrer):
                return jsonify("Forbidden!"), 200
        url = getSource("https://vidlink.pro/movie/" + str(tmdbid) + "?autoplay=true")
        if url is not None:
                return jsonify(url), 200
        return jsonify("Not found"), 404

@app.route("/api/show/<season>/<episode>/<tmdbid>", methods=["GET"])
def show(season, episode, tmdbid):
        if not checkReferrer(request.referrer):
                return jsonify("Forbidden!"), 200
        url = getSource("https://vidlink.pro/tv/" + tmdbid  + "/" + season + "/" + episode + "?autoplay=true")
        if url is not None:
                return jsonify(url), 200
        return jsonify("Not found"), 404

if __name__ == "__main__":
        app.run(host='0.0.0.0')
