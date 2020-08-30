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

def mail():
	mail_headlines_array = []
	url = "http://www.dailymail.co.uk"
	soup = soupifier(url)
	headlines_total = searchifier({"soup": soup, "tag": 'h2', "attribute": 'class', "names": 'linkro-darkred', "find": 0})
	headlines_total = tenifier(headlines_total)
	for headline in headlines_total:	
		headline = searchifier({"soup": headline, "tag": 'a', "names": None, "find": 0})[0]
		link = linkifier(url, headline)	
		soup = soupifier(link)
		body_soup = searchifier({"soup": soup, "tag": 'div', "attribute": 'itemprop', "names": 'articleBody', "find": 1})	
		body = bodifier(body_soup)
		appendifier(mail_headlines_array, headline, link, url, body)
	return mail_headlines_array
