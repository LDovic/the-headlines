import requests
from bs4 import BeautifulSoup
import collections
import json
from soupify import soupifier
from soupify import appendifier
from soupify import searchifier
from soupify import linkifier
from soupify import bodifier
from soupify import tenifier

def times():
	times_headlines_array = []
	headlines_total = []
	url = "https://www.thetimes.co.uk"
	soup = soupifier(url)
	headlines_total = searchifier({"soup": soup,"tag": 'div',"attribute": 'class', "names": 'Item-content', "find": 0})
	headlines_total = headlines_total[4:]
	headlines_total = tenifier(headlines_total)
	for headline_ in headlines_total:
		headline = headline_.find('a')
		link = linkifier(url, headline)
		soup = soupifier(link)
		body_soup = searchifier({"soup": soup, "tag": 'div', "attribute": 'class', "names": 'Article-content', "find": 0})
		body = bodifier(body_soup)
		appendifier(times_headlines_array, headline, link, url, body)
	return times_headlines_array
