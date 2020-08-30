import requests
from bs4 import BeautifulSoup
import collections
import json
from soupify import soupifier
from appendify import appendifier

def sun():
	the_sun_headlines_array = []
	url = "https://www.thesun.co.uk"
	soup = soupifier(url)
	for item in soup.find_all('a', {'class': 'text-anchor-wrap'}, limit=10):
		headline = item.find('p', {'class': 'teaser__subdeck'})
		link = item['href']
		soup2 = soupifier(link)
		div2 = soup2.find_all('div', {'class': 'article__content'})
		if div2:
			ps = div2[0].find_all('p')
			body = [p.text.strip() for p in ps]
		else:
			body = []
		appendifier(the_sun_headlines_array, headline, link, url, body)	
	return the_sun_headlines_array
