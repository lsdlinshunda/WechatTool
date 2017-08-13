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
MoneyRunner是安卓自动测试工具，用来在真机上进行模拟操作。
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
> - 注意证书类型选择的是VPN和应用，不是选wifi。之前使用安卓手机时证书类型选的是wifi，结果查询过程会一直弹安全警示。但是改成VPN和应用之后从推送页面点返回，历史消息页面不会再重新发起请求，而是取缓存的页面，导致会重复跳转同一条推送，所以现在改变了这种点返回的方式，改成点左上角的X返回之前的页面，再重新点击“查看历史消息”。这么做同时解决了加载过慢可能会退出应用的问题，自动查询的稳定性大大提升。
#### (3)设置代理
- 打开手机wifi的设置，代理设置为手动，服务器地址为AnyProxy终端里显示的地址，代理服务器端口默认为8001
#### (4)查看抓取信息
- 配置完成后，打开微信，点击到任意一个公众号历史消息或文章中，在浏览器的web界面中应该能看到滚动的请求信息
#### (5)替换配置文件
- mac系统中配置文件的位置在/usr/local/lib/node_modules/anyproxy/lib/；windows中的位置是C:\Users\baixing\AppData\Roaming\npm\node_modules\anyproxy\lib。用WechatTool文件夹中的``` AnyProxy\rule_default.js ```进行替换
### 2.MonkeyRunner配置
-  连接手机，命令行输入adb devices查看手机的标识号，将对应的标识号写入``` config.json ```

## 四、使用说明
1. 连接手机
2. 第一次使用前先运行``` runPHP.bat ``` 和``` runAnyProxy.bat ``` 这两个文件开启本地PHP服务器和代理服务器，两个窗口不要关闭就行
3. 在``` accountID.txt ```中添加要查询的公众号ID
4. 保证手机处于微信首页，运行``` start.bat ```
5. 查询结束后在``` result.csv ```中查看结果

## 五、注意事项
- ``` start.bat ```可能出现与手机连接不成功的问题，如果选完选项看到窗口中滚动出很多错误信息，关闭窗口再运行一次。一般第二次就好了
- 微信号没有搜索结果会提示在窗口里
- 查询过程中不要开着``` result.csv ```，会导致查询结果无法写入。如果中途需要查看查询结果，可以先看 ``` temp\record.txt ```里的记录 
- 查询过程中手机可能会随机出现一些安全警示，会导致流程中止，看到了需要手动点击。如果因为这个或者其它什么因素导致查询过程崩溃，关掉重新运行就行，脚本会接着没查询的公众号和推送继续查询，不过可能会丢失一两条推送的信息
- 查询结束后重新运行一次确保每个公众号都查询到
- 已经查询过的公众号之后再查询就会自动跳过，如果需要重新查询，清空``` temp\accountInfo.txt ```里的内容

## 参考资料
- MonkeyRunner环境搭建 http://www.cnblogs.com/lynn-li/p/5885001.html

- 【Python】通过截图匹配原图中的位置（opencv）
http://blog.csdn.net/ns2250225/article/details/60334176
- 【Python+OpenCV】Windows+Python3.6.0（Anaconda3）+OpenCV3.2.0安装配置 http://blog.csdn.net/lwplwf/article/details/61616493
- 微信公众号文章批量采集系统的构建 https://zhuanlan.zhihu.com/p/24302048