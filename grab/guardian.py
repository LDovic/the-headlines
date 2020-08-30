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

def guardian():
	guardianuk_headlines_array = []
	url = "https://www.theguardian.com/uk"
	soup = soupifier(url)
	section = searchifier({"soup": soup, "tag": 'section', "attribute": 'id', "names": 'headlines', "find": 0})
	headlines_total = searchifier({"soup": section, "tag": 'a', "attribute": 'class', "names": 'u-faux-block-link__overlay js-headline-text', "find": 0})
	headlines_total = tenifier(headlines_total)
	for headline in headlines_total:
		link = linkifier('https://www.theguardian.com/', headline)
		soup = soupifier(link)
		body_soup = searchifier({"soup": soup, "tag": 'div', "attribute": 'class', "names": 'content__article-body from-content-api js-article__body', "find": 0})
		body = bodifier(body_soup)
		appendifier(guardianuk_headlines_array, headline, link, url, body)			
	return guardianuk_headlines_array
