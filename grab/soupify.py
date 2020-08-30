#These scripts are commonly used when scraping html

import requests
from bs4 import BeautifulSoup
import collections

def tenifier(headlines_total):
	if len(set(headlines_total)) == 10:
		return set(headlines_total)
	for y in range(10, len(headlines_total)):
		if len(set(headlines_total[:y])) == 10:
			return set(headlines_total[:y])
            
def bodifier(soup):
	if isinstance(soup, list) is False:
		soup = [soup]
	try:
		body = [p.text.strip() for p in soup[0].find_all('p')]
		return body
	except:
		return []

def linkifier(url, headline):		
	try:
		headline['href']
	except:
		return False
	if soupifier(headline['href']) is False:
		return url + headline['href']
	return headline['href']



def searchifier(searches):
	def findifier(search):
		def namesifier(search):
			names = search["names"]
			if isinstance(names, list) is False:
				names = [names]
			return names			

		find = search["find"]
		soup = search["soup"]
		if len(soup) is 1:
			soup = search["soup"][0]

		tag = search["tag"]

		if find:
			if search["names"]:
				names = namesifier(search)
				attribute = search["attribute"]
				for name in names:
					if soup.find(tag, {attribute: name}):
						return soup.find(tag, {attribute: name})
			return soup.find(tag)

		if search["names"]:
			names = namesifier(search)	
			attribute = search["attribute"]
			for name in names:
				if soup.find_all(tag, {attribute: name}):
					return soup.find_all(tag, {attribute: name})
		return soup.find_all(tag)

	if isinstance(searches, list) is False:
		searches = [searches]

	for search in searches:
		if findifier(search):
			return findifier(search)

	return False

def appendifier(headlines_array, headline, link, url, body):
	headlines_array.append({'headlines': headline.text.strip(), 'link': link, 'source': url, 'body': str(body)})
	return headlines_array

def soupifier(url):
	headers = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36'}
#	proxies = {'http': 'http://192.168.0.15', 'https': 'https://192.168.0.15'}
	while True:
		try:
			request = requests.get(url, headers=headers)
			break	
		except ValueError:	
			return False
	soup = BeautifulSoup(request.text, "html.parser")
	return soup
