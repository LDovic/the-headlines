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

def telegraph():
	telegraph_headlines_array = []
	url = "https://www.telegraph.co.uk"
	soup = soupifier(url)
	headlines_total = searchifier({
        "soup": soup,
        "tag": 'h3',
        "attribute": 'class',
        "names": ['list-of-entities__item-body-headline','list-headline'],
        "find": 0
	})
	headlines_total = tenifier(headlines_total)	
	for headline in headlines_total:		
		headline_ = searchifier([{
                "soup": headline,
                "tag": 'a',
                "names": None,
                "find": 1
            },
	        {
                "soup": headline,
                "tag": 'span',
                "attribute": 'class',
                "names": 'list-of-entities__item-headline-text',
                "find": 1
            }])
		link = linkifier(url, headline_)
		if link is False:
			headline_ = headline.parent
			link = linkifier(url, headline_)
		soup = soupifier(link)
		body_soup = searchifier([{
		"soup": soup,
		"tag": 'article',
		"names": None,
		"find": 0
		},
		{
		"soup": soup,
		"tag": 'div',
		"attribute": 'class',
		"names": 'js-article-inner',
		"find": 0
		}])
		body = bodifier(body_soup)	
		appendifier(telegraph_headlines_array, headline, link, url, body)
	return telegraph_headlines_array
