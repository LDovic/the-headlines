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

def bbc():
	bbc_headlines_array = []
	url = "http://www.bbc.co.uk/news"
	soup = soupifier(url)
	headlines_total = searchifier({
		"soup": soup,
		"tag": 'a',
		"attribute": 'class',
		"names": 'gs-c-promo-heading',
		"find": 0
	})
	headlines = tenifier(headlines_total)
	for headline in headlines:
		link = linkifier('http://www.bbc.co.uk/', headline)	
		soup = soupifier(link)
		body_soup = searchifier({
			"soup": soup,
			"tag": 'div',
			"attribute": 'class',
			"names": ['story-body__inner','vxp-media__body','story-body sp-story-body gel-body-copy'],
			"find": 0
		})
		body = bodifier(body_soup)
		appendifier(bbc_headlines_array, headline, link, url, body)
	return bbc_headlines_array
