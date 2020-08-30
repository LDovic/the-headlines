import logging
import json

file = open("xxxxxxxx/sources.txt","r")

source = file.read()

def logger(source):
	try:
		module = __import__(source)
		method_to_call = getattr(module, source)
		method = method_to_call()
	except Exception as e:
            logging.basicConfig(filename='xxxxxxxx/error.log', level=logging.ERROR)
            logging.error(e)
	else:
            print(json.dumps(method))

logger(source)
