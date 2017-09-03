## MonkeyRunner使用介绍
1. MonkeyRunner在使用前，必须先打开模拟器或连接上手机设备。
2. 连接成功后， cmd输入```monkeyrunner```进入MonkeyRunner的shell命令交互模式。进入shell命令交互模式后，首要一件事就是导入monkeyrunner所要使用的模块。直接在shell命令下输入命令：```from com.android.monkeyrunner import MonkeyRunner,MonkeyDevice ```。这步完成后就可以利用monkeyrunner进行测试工作了。
3. MonkeyRunner也可以直接运行脚本，命令行格式为```monkeyrunner xxx.py```
***
### 常用API
```
#需要引入的模块
from com.android.monkeyrunner import MonkeyRunner as mr
from com.android.monkeyrunner import MonkeyDevice as md
from com.android.monkeyrunner import MonkeyImage as mi

#等待设备连接，30秒超时，后面是设备名称，该名称可以通过执行命令行`adb devices`获得
device = mr.waitForConnection(30,'123123135002735')

#安装apk包
device.installPackage('d:/有道词典V4.0.3.apk'.decode('utf-8'))

#卸载应用程序
device.removePackage('com.youdao.dict')

#启动应用程序
device.startActivity(component='com.youdao.dict/.activity.DictSplashActivity')

#等待程序加载,5秒
mr.sleep(5)

#拖动操作，四个参数，前两个是初始点、结束点坐标，0.5是持续时间，1是步数
device.drag((550,500),(100,500), 0.5, 1)

#触摸操作,三个参数，X坐标、Y坐标，触摸类型
device.touch(80, 1050, "DOWN_AND_UP")

#截图并保存,注意如果名字中有中文，需要进行utf-8编码，否则乱码
now = time.strftime("%Y-%m-%d-%H-%M-%S")
mainPageImage = device.takeSnapshot()
mainPageImage.writeToFile("d:/"+"主页面截图".decode("utf-8")+now+".png", "png")

#点击后退键,键盘码详情可以去查sdk帮助文档，路径：android-sdk-windows/docs/reference/android/view/KeyEvent.html
device.press("KEYCODE_BACK", "DOWN_AND_UP")

#将日志输出到外部文件,在python中使用中文，需要在文件开头将编码设置为utf-8,否则乱码
log = open('d:/monkenyLog.txt', 'w')
log.write("等待手机连接...、\n")
log.close()

#截图比较，sameAs()第二个参数表示相似度，0表示完全不相似，1表示完全相同
imageTrue = mr.loadImageFromFile('d:/shot/true.png')
if(imageTrue.sameAs(mainPageImage, 0.75)):
    log.write('截图比较成功\n')
else:
    log.write('截图比较失败\n')
```
### 录制脚本
MonkeyRunner自身提供脚本录制功能，即MonkeyRecorder。
``` 
from com.android.monkeyrunner import MonkeyRunner as mr 
from com.android.monkeyrunner.recorder import MonkeyRecorder as recorder 
device = mr.waitForConnection() 
recorder.start(device)
```
录制产生的结果, ```myRecorder.mr```：
```
TOUCH|{'x':92,'y':936,'type':'downAndUp',}
TOUCH|{'x':357,'y':688,'type':'downAndUp',}
TOUCH|{'x':285,'y':82,'type':'downAndUp',}
TYPE|{'message':'hello',}
TOUCH|{'x':679,'y':82,'type':'downAndUp',}
```
想要录制回放，还要借助```monkey_playback.py```文件,代码如下：
```
import sys
from com.android.monkeyrunner import MonkeyRunner

CMD_MAP = {  
    "TOUCH": lambda dev, arg: dev.touch(**arg),  
    "DRAG": lambda dev, arg: dev.drag(**arg),  
    "PRESS": lambda dev, arg: dev.press(**arg),  
    "TYPE": lambda dev, arg: dev.type(**arg),  
    "WAIT": lambda dev, arg: MonkeyRunner.sleep(**arg)  
    }  
  
#Process a single file for the specified device.  
def process_file(fp, device):  
    for line in fp:  
        (cmd, rest) = line.split("|")  
        try:  
            rest = eval(rest)  
        except:  
            print ("unable to parse options")  
            continue  
  
        if cmd not in CMD_MAP:  
            print ("unknown command: " + cmd) 
            continue  
  
        CMD_MAP[cmd](device, rest)  
  
  
def main():  
    file = sys.argv[1]  
    fp = open(file, "r")  
  
    device = MonkeyRunner.waitForConnection()  
      
    process_file(fp, device)  
    fp.close();  
  
if __name__ == "__main__":  
    main()

```
在cmd命令行中输入命令：```monkeyrunner monkey_playback.py myRecorder.mr```，运行
***
### 参考资料
- [monkeyrunner之环境搭建及实例](http://www.cnblogs.com/lynn-li/p/5885001.html)
- [monkeyrunner的使用和自动测试微信 ](http://blog.csdn.net/streen_gong/article/details/21398127)