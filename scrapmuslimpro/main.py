import mysql.connector
import time
import os
import datetime
from selenium import webdriver
from selenium.common import exceptions
from selenium.common.exceptions import NoSuchElementException
# import psutil


def main():
    # if not is_running("main.py"):
    #   logger("Kill before RUN")
    #   os.system("sudo pkill chromedriver")
    #   time.sleep(1)
    #   execute()
    # else:
    #  logger("f Echo already please wait")
    os.system("sudo pkill chromedriver")
    os.system("sudo pkill chromium-browser")
    time.sleep(2)
    execute()

    option = webdriver.ChromeOptions()

    option.add_argument('headless')
    option.add_argument('--disable-infobars')
    option.add_argument('--disable-dev-shm-usage')
    option.add_argument('--no-sandbox')
    browser = webdriver.Chrome(executable_path='D:/xampp/htdocs/koel/scrapmuslimpro/driver/chromedriver',
                               options=option)
# browser.get("https://www.muslimpro.com/id/locate?country_code=ID&country_name=Indonesia&city_name=undefined&coordinates=-6.2064052,107.0007987")
    browser.get("https://www.muslimpro.com/locate?country_code=ID&country_name=Indonesia&city_name=South%20Jakarta&coordinates=-6.2614927,106.8105998")
# browser.get("https://www.muslimpro.com/id/locate?country_code=ID&country_name=Indonesia&city_name=Bekasi%20Regency&coordinates=-6.366722999999999,107.1735638")
    time.sleep(2)

    logger("START GET Data Adzan")


datanya = []

while True:
    try:
        subuh = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[1]/div/p[2]")
        time.sleep(2)
        syuruq = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[2]/div/p[2]")
        time.sleep(2)
        dzuhur = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[3]/div/p[2]")
        time.sleep(2)
        ashar = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[4]/div/p[2]")
        time.sleep(2)
        maghrib = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[5]/div/p[2]")
        # maghrib = browser.find_element_by_xpath("/html/body/div[1]/div[2]/div/div[5]/div[5]/div/p[2]")
        # maghrib = browser.find_element_by_css_selector('body > div.main-container > div.home-prayertime.main-time-banner > div > div:nth-child(5) > div:nth-child(4) > div > p.praytime')
        isya = browser.find_element_by_xpath(
            "/html/body/div[1]/div[2]/div/div[5]/div[6]/div/p[2]")
        time.sleep(2)
        logger("SUBUH => "+subuh.text)
        logger("SYURUK => "+syuruq.text)
        logger("DZHUHUR => "+dzuhur.text)
        logger("ASHAR =>"+ashar.text)
        logger("MAGRHIB =>"+maghrib.text)
        logger("ISYA => "+isya.text)
        logger("Mendapat jadwal solat dari muslimpro")
        # time.sleep(2)
        cek = ceksolat()
        # =========== menentukan waktu tahajud
        subsplit = subuh.text.split(":")
        kurang = int(subsplit[0])-1
        sat = '%02d' % (kurang,)
        tahajud = sat+":"+subsplit[1]
        logger(tahajud)
        # ===================================
        # ============ menentukan waktu duha
        dusplit = syuruq.text.split(":")
        kurangdu = int(dusplit[0])+1
        satdu = '%02d' % (kurangdu,)
        duha = satdu+":"+dusplit[1]
        logger(duha)
        # ===================================
        try:
            dtime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        except:
            dtime = '0000-00-00 00:00:00'
        if subuh.text == "":
            subuh_val = cek[4]
        else:
            subuh_val = subuh.text

        if syuruq.text == "":
            syuruq_val = cek[5]
        else:
            syuruq_val = syuruq.text

        if dzuhur.text == "":
            dzuhur_val = cek[7]
        else:
            dzuhur_val = dzuhur.text

        if ashar.text == "":
            ashar_val = cek[8]
        else:
            ashar_val = ashar.text

        if maghrib.text == "":
            maghrib_val = cek[9]
        else:
            maghrib_val = maghrib.text

        if isya.text == "":
            isya_val = cek[10]
        else:
            isya_val = isya.text

        # data_solat = (str(tahajud), subuh.text, syuruq.text, str(duha), dzuhur.text, ashar.text, maghrib.text, isya.text, dtime, cek[0])
        data_solat = (str(tahajud), subuh_val, syuruq_val, str(
            duha), dzuhur_val, ashar_val, maghrib_val, isya_val, dtime, cek[0])
        print(data_solat)
        update_solat(data_solat)
        time.sleep(1)
        logger("Selesai ...")
        time.sleep(1)
        break
    except NoSuchElementException:
        print("Tidak ditemukan elemennya/offline/web maintenance")

browser.quit()


def connect():
    try:
        mydb = mysql.connector.connect(
            host="localhost",
            user="root",
            passwd="",
            database="koel",
            auth_plugin='mysql_native_password'
        )
        return mydb
    except:
        logger("Error " + e)

    return None


def ceksolat():
    conn = connect()
    cursor = conn.cursor(buffered=True)
    while True:
        sql_select_query = """select * from times where id <> 0"""
        cursor.execute(sql_select_query,)
        record = cursor.fetchone()
        return record


def update_solat(data):
    conn = connect()
    mycursor = conn.cursor()
    try:
        sql = "UPDATE times SET tahajud=%s,subuh=%s,syurooq=%s,duha=%s,dhuhur=%s,ashar=%s,maghrib=%s,isya=%s,updated_at=%s WHERE id=%s"
        val = (data)
        mycursor.execute(sql, val)
        conn.commit()
        print("waktu terupdate")
    except:
        print("tidak update data")
        pass
    time.sleep(1)


def logger(msg, prn=True):
    if prn:
        print(msg)

    logfile = open("D:/xampp/htdocs/koel/scrapmuslimpro/log-koel.txt", "a")
    logfile.write(
        "[" + datetime.datetime.now().strftime("%y-%m-%d %H:%M") + "] " + msg + "\n")
    logfile.close()

# def is_running(script):
#    for q in psutil.process_iter():
#        if q.name().startswith('python3'):
#            if len(q.cmdline())>1 and script in q.cmdline()[1] and q.pid !=os.getpid():
#                print("'{}' Process is already running".format(script))
#                return True
#
#    return False


if __name__ == '__main__':
    main()
