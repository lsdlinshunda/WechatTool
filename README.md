## 一、依赖清单
- jdk
- android sdk
- python 3.6
- opencv 3.2
- aircv
- numpy
- nodejs
- anyproxy
- php 7.1
## 二、环境搭建
### 1.MonkeyRunner环境搭建
MonkeyRunner是安卓自动测试工具，用来在真机上进行模拟操作。
MonkeyRunner的环境搭建，需要安装以下工具：jdk、android sdk、python编译器。
#### （1）jdk的安装与配置

- jdk下载地址：http://www.oracle.com/technetwork/java/javase/downloads/index.html
，下载完成后，默认安装即可
- 安装完成后，将jdk安装目录和安装目录中的jre\bin添加至环境变量中的Path变量
- 运行cmd，输入java -version，若正确显示版本信息，则说明安装和配置成功
#### (2) android sdk安装与配置
android sdk就是指Android专属的软件开发工具包。android sdk中我们最常用的就是tools和platform-tools文件夹中的工具。
- sdk下载：http://developer.android.com/sdk/index.html ，下载完成后解压到自己的目录，不需要安装
- 将sdk中的tools和platform-tools目录添加到环境变量中
- 运行cmd,输入adb，若正确出现版本信息，说明安装和配置成功
#### (3) Python编辑器安装与配置
python用于支持Monkeyrunner运行，使用python脚本编写用例会大大简化Monkeyrunner用例的编写，且会帮助扩展monkeyrunner的自动化功能。
- http://www.python.org/download/ 下载Python 3.6版本
- 环境变量中添加Pyhton安装路径
- 运行cmd，输入python，若显示版本信息并进入python命令行，说明安装和配置成功
### 2.图像定位的相关依赖
使用了aircv获取截图中按钮的位置
#### (1)安装opencv
- http://www.lfd.uci.edu/~gohlke/pythonlibs/ 找对应版本的opencv下载，我下载的是opencv_python‑3.2.0‑cp36‑cp36m‑win32.whl。下载后把文件复制到pyhton安装目录下的\Lib\site-packages里
- 在这个文件夹中运行cmd，执行命令
``` pip install opencv_python‑3.2.0‑cp36‑cp36m‑win32.whl ``` 
- 安装完成后输入``` python``` 进入python，输入``` import cv2``` ,不报错说明安装配置成功
#### (2)安装两个python的第三方库
    pip install numpy
    pip install aircv
### 3.AnyProxy环境搭建
代理服务器使用的是AnyProxy，这个代理服务器的特点是可以获取到https链接的内容。在2016年年初的时候微信公众号和微信文章开始使用https链接。并且Anyproxy可以通过修改rule配置实现向公众号的页面中插入脚本代码。
#### (1) 安装NodeJS
- https://nodejs.org/en/
#### (2) 安装AnyProxy
- 命令行或者终端运行 
 ``` npm install -g anyproxy ```，mac系统需要加上sudo
- 生成RootCA,https需要这个证书: ```sudo anyproxy --root ```
### 4.PHP环境搭建
PHP用于搭建本地服务器
- 下载安装PHP 7.1

## 三、配置
### 1.AnyProxy配置
#### (1)启动AnyProxy
- 运行命令：``` anyproxy -i ```；参数-i是解析HTTPS的意思
#### (2)在手机上安装证书
- 浏览器打开终端里显示的AnyProxy Gui Interface的地址，获取rootCA.crt。有两种获取方式，直接下载和扫二维码。之后在手机上安装rootCA.crt证书
> 注意安装证书类型选择的是VPN和应用，不是选wifi。之前使用安卓手机时证书类型选的是wifi，结果查询过程会一直弹安全警示。但是改成VPN和应用之后从推送页面点返回，历史消息页面不会再重新发起请求，而是取缓存的页面，导致会重复跳转同一条推送，所以现在改变了这种点返回的方式，改成点左上角的X返回之前的页面，再重新点击“查看历史消息”。这么做同时解决了加载过慢可能会退出应用的问题，自动查询的稳定性大大提升。
#### (3)设置代理
- 打开手机wifi的设置，代理设置为手动，服务器地址为AnyProxy终端里显示的地址，代理服务器端口默认为8001
#### (4)查看抓取信息
- 配置完成后，打开微信，点击到任意一个公众号历史消息或文章中，在浏览器的web界面中应该能看到滚动的请求信息
#### (5)替换配置文件
- 由于需要抓取历史消息页面和推送页面发送给本地PHP处理，得对AnyProxy的规则进行调整，主要是修改了\lib\rule_default.js里的replaceServerResDataAsync函数
- mac系统中配置文件的位置在/usr/local/lib/node_modules/anyproxy/lib/；windows中的位置是C:\Users\baixing\AppData\Roaming\npm\node_modules\anyproxy\lib。用WechatTool文件夹中的``` AnyProxy\rule_default.js ```进行替换
### 2.MonkeyRunner配置
MonkeyRunner 需要手机连接到电脑上使用，也可以通过adb远程连接的方式进行调试，这里介绍的是直接连接到电脑上的方式。使用的测试机是魅蓝Note 5(Android 6.0, 5.5英寸)
#### (1) 连接手机，开发者选项里开启USB调试
- 连接好后电脑上开启命令行输入```adb devices```，如果能看到手机的标识号说明正确连接了。有时候输入``` adb devices```会显示端口被占用，可以尝试通过```adb kill-server``` 或者在任务管理器里结束adb的进程解决
#### (2) 机型适配
- 查询公众号输入文本时，手机的中文输入法会有影响。所以需要将手机的输入法换回原生的英文输入法

- MonkeyRunner中的点击是基于坐标的，目前这些坐标一部分是通过MonkeyRunner的录制功能导出在script文件夹里，另一部分是直接hard cord在python的脚本里。所以屏幕大小可能会影响脚本的使用，如果在其它大小的手机上使用有问题，需要手动调整这些坐标

- 是否有查询结果以及“查看历史消息”按钮的位置是通过图像识别的方法判断的，需要事先准备用于比对的图片。``` AutoSript\obj\ ```里的hisButton.png和noResult.png就是准备的比对图片。不同的机型由于屏幕大小和UI的不同，可能需要根据具体的机型重新截取比对图片。具体的表现是查询的过程中脚本在公众号页面无法找到“查看历史消息”按钮的位置。需要重新截取两张比对的图片替换到``` AutoSript\obj\ ```里

## 四、使用说明
1. 连接手机
2. 使用前先运行``` runPHP.bat ``` 和``` runAnyProxy.bat ``` 这两个文件开启本地PHP服务器和代理服务器
3. 在``` accountID.txt ```中添加要查询的公众号ID
4. 保证手机处于微信首页，运行``` start.bat ```
5. 查询结束后在``` result.csv ```中查看结果

## 五、注意事项
- ``` start.bat ```可能出现与手机连接不成功的问题，如果选完选项看到窗口中滚动出很多错误信息，关闭窗口再运行一次。一般第二次就好了
- 查询第一个公众号时，在“添加朋友”里点击“公众号”可能会有卡顿，导致在查询框输入了错误的id。猜测是因为第一次查询时微信需要加载页面，所以可以在运行工具前先手动点击一次“添加朋友”里的“公众号”，查询第一个公众号就不会出现卡顿了
- 微信号没有搜索结果会提示在窗口里
- 查询过程中不要开着``` result.csv ```，会导致查询结果无法写入。如果中途需要查看查询结果，可以先看 ``` temp\record.txt ```里的记录 。若```result.csv```出现了乱码，删掉```result.csv```再重新查询一次应该就正常了（重新查询前需要先清空``` temp\accountInfo.txt ```里的内容）

- 查询结束后可以重新运行一次确保每个公众号都查询到
- 已经查询过的公众号之后再查询就会自动跳过，如果需要重新查询，清空``` temp\accountInfo.txt ```里的内容

## 参考资料
- [MonkeyRunner环境搭建]( http://www.cnblogs.com/lynn-li/p/5885001.html)
- [【Python】通过截图匹配原图中的位置(opencv)](http://blog.csdn.net/ns2250225/article/details/60334176)
- [【Python+OpenCV】Windows+Python3.6.0（Anaconda3）+OpenCV3.2.0安装配置](http://blog.csdn.net/lwplwf/article/details/61616493)
- [微信公众号文章批量采集系统的构建](https://zhuanlan.zhihu.com/p/24302048)