import re
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

def independent():
	independent_headlines_array = []
	headlines_array = []
	url = "https://www.independent.co.uk"
	soup = soupifier(url)
	section_content = searchifier({"soup": soup, "tag": 'section', "attribute": 'class', "names": 'section-content', "find": 1})
	splash_row = searchifier({"soup": section_content, "tag": 'div', "attribute": 'class', "names": 'splash-row', "find": 1})
	headlines_total = searchifier({"soup": splash_row, "tag": 'div', "attribute": 'class', "names": 'content', "find": 0})
	for headline in headlines_total:
		top_two = searchifier({"soup": headline, "tag": 'h2', "names": None, "find": 1})
		if top_two:
			link = searchifier({"soup": headline, "tag": 'a', "names": None, "find": 1})
			link = linkifier(url, link)
			body_soup = soupifier(link)
			body_soup = searchifier({"soup": body_soup, "tag": 'div', "attribute": 'class', "names": 'body-content', "find": 1})
			body = bodifier(body_soup)	
			appendifier(independent_headlines_array, headline, link, url, body)
	eight_articles_dmpu = searchifier({"soup": soup, "tag": 'div', "attribute": 'class', "names": 'eight-articles-dmpu position-left', "find": 1})
	top_eight = searchifier({"soup": eight_articles_dmpu, "tag": 'div', "attribute": 'class', "names": 'content', "find": 0})
	for headline in top_eight:
		headline = searchifier({"soup": headline, "tag": 'a', "names": None, "find": 0})[1:]
		for element in headline:
			link = linkifier(url, element)
			headline = searchifier({"soup": element, "tag": 'div', "attribute": 'class', "names": 'headline', "find": 1})
			body_soup = soupifier(link)
			body_soup = searchifier({"soup": body_soup, "tag": 'div', "attribute": 'class', "names": 'body-content', "find": 1})
			body = bodifier(body_soup)
			appendifier(independent_headlines_array, headline, link, url, body)
	return independent_headlines_array
