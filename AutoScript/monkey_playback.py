# -*- coding:UTF-8 -*-
import sys
import os
from com.android.monkeyrunner import MonkeyRunner

#deviceID = "621QECPP2APWG"  #魅族
#deviceID = "621QECPP2APWG"  #oppo
resetButton = (96,139)      #手机左上角取消按钮坐标

#用到的文件路径
workPath = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))  #获取工作目录
configPath = workPath + "\\config.json"
recordFile = workPath + "\\AutoScript\\script\\Meizu_demo4"                #录制的脚本文件
idFilePath = workPath + "\\accountID.txt"                                  #待查询的公众号ID
hisButtonPath = workPath + "\\AutoScript\\obj\\hisButton.png"              #截取的"获取历史消息"按钮
noResultPath = workPath + "\\AutoScript\\obj\\noResult.png"                #用于比对的无搜索结果的截图
judgeScript = workPath+"\\AutoScript\\judge.py"                            #图像定位脚本
urlFile = workPath + "\\temp\\url.txt"                                     #历史推送页面抓取到的url
accountInfoPath = workPath+"\\temp\\accountInfo.txt"                      #已查询公众号列表

CMD_MAP = {  
    "TOUCH": lambda dev, arg: dev.touch(**arg),  
    "DRAG": lambda dev, arg: dev.drag(**arg),  
    "PRESS": lambda dev, arg: dev.press(**arg),  
    "TYPE": lambda dev, arg: dev.type(arg),  
    "WAIT": lambda dev, arg: MonkeyRunner.sleep(**arg)
    }

#通过图像识别，定位查看历史消息按钮
def touchByImage(dev, srcPath, objPath, type):
    snapshot = dev.takeSnapshot()
    snapshot.writeToFile(srcPath)
    command = "python " + judgeScript + " " + srcPath + " " + objPath
    output = os.popen(command)
    pos = eval(output.read())
    print pos
    dev.touch(pos[0],pos[1], type)
    return pos

#重复点击左上角的取消键回退到初始界面
def reset(dev, n = 5):
    for i in range(n):
        dev.touch(resetButton[0], resetButton[1], "downAndUp")
        MonkeyRunner.sleep(0.2)

#判断是否有查询结果
def noResult(dev):
    searchResult = dev.takeSnapshot()
    noResultImg = MonkeyRunner.loadImageFromFile(noResultPath)
    return searchResult.sameAs(noResultImg, 0.9)

#点击可能出现的安全警告
def avoidSafeNotice(dev):
    for i in range(7):
        dev.touch(745, 1376, "downAndUp")
        MonkeyRunner.sleep(0.1)

#Process a single file for the specified device.  
def process_file(fp, fid, device):
    index = 1
    for accountID in fid:
        print ("---------------"+str(index)+"--------------")
        index = index + 1
        accountID = accountID.replace("\n","")
        accountID = accountID.replace("\r","")
        try:
            faccountInfo = open(accountInfoPath, "r")
            accountInfo = faccountInfo.read()
            if accountInfo.lower().find(accountID.lower()) != -1:
                print (accountID + " has already been searched.")
                continue
        except:
            pass
        print ("Search: "+accountID)
        fp.seek(0, 0)
        for line in fp:
            (cmd, rest) = line.split("|")  
            try:  
                rest = eval(rest)
            except:  
                print ("unable to parse options")  
                continue  

            if cmd == "TYPE":      #输入的公众号id查询
                rest = accountID
                CMD_MAP[cmd](device, rest)
                MonkeyRunner.sleep(0.5)
                device.press("KEYCODE_ENTER", "downAndUp")
                #device.touch(996, 1849, "downAndUp")
                MonkeyRunner.sleep(1.0)
                avoidSafeNotice(device)
                MonkeyRunner.sleep(1.5)
                if noResult(device):
                    print ("No search result for "+accountID)
                    reset(device, 2)
                    break
                continue

            if cmd == "HIS":       #点击查看历史消息按钮
                savePath =  workPath + "\\AutoScript\\snapshot\\"+accountID+".png"
                try:
                    pos = touchByImage(device, savePath, hisButtonPath, "downAndUp")
                except:
                    print "[ERROR] NOT FIND HISTORY BUTTON"
                    reset(device)
                    break
                MonkeyRunner.sleep(7)
                urlcount = len(open(urlFile,'rU').readlines())     #读取url文件获取需要跳转的次数
                print (str(urlcount) + " article left")
                while (urlcount>0):
                    device.touch(resetButton[0], resetButton[1], "downAndUp")
                    MonkeyRunner.sleep(0.5)
                    device.touch(pos[0], pos[1], "downAndUp")
                    MonkeyRunner.sleep(6)
                    urlcount = urlcount - 1
                    print (str(urlcount) + " article left")
                continue

            if cmd not in CMD_MAP:  
                print ("unknown command: " + cmd) 
                continue  
  
            CMD_MAP[cmd](device, rest)  
        reset(device)
        MonkeyRunner.sleep(1.0)
  
def main():
    fconfig = open(configPath, "r")
    config  = eval(fconfig.read())
    fconfig.close()
    deviceID = config["deviceID"]
    #device = MonkeyRunner.waitForConnection(5, deviceID)
    device = MonkeyRunner.waitForConnection(5)
    config["max_url_num"] = int(MonkeyRunner.input(u"请输入每个公众号抓取的推送数：", str(config["max_url_num"])))
    fconfig = open(configPath, "w")
    fconfig.write(str(config))
    fconfig.close()
    frecord = open(recordFile, "r")
    choices = [1, 2]
    choice = MonkeyRunner.choice(u"1:文件批量查询   2：单个公众号查询",choices, u"选择查询方式")
    if choice == 1:
        id = MonkeyRunner.input(u"请输入公众号ID：")
        process_file(frecord, [id], device)
    elif choice == 0:
        fid = open(idFilePath, "r")
        process_file(frecord, fid, device)
        fid.close()
    frecord.close()

if __name__ == "__main__":
    main()
