import pymysql
import bs4
import urllib.request
from urllib.request import urlopen
from bs4 import BeautifulSoup as soup
import mysql.connector
import configparser

config = configparser.RawConfigParser()
config.read(filenames='my.properties')
print(config.sections())

['mysql']
h = config.get('mysql', 'localhost')
u = config.get('mysql', 'root')
p = config.get('mysql', '')
db = config.get('mysql', 'koel')

koel_db = pymysql.connect(h, u, p, db)
