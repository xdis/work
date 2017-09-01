@echo off
echo       
echo   1.启动PHP服务器软件 PhpStudy.exe
start "启动程序" "C:\Users\Administrator\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\phpStudy.lnk"
echo   
echo   2.启动数据库软件 SQLyog.exe
start "" /b "C:\Users\Administrator\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\SQLyog.lnk"
echo    
echo   3.启动谷歌浏览器 Chrome.exe
start "" /b "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" http://www.github.com http://www.stackoverflow.com http://www.csdn.net http://www.baidu.com 
Ping -l 1 -n 1 -w 6000 1.1.1.1 -4 1>nul 2>&1
echo   
echo   4.启动接口调试软件 Postman.exe
start "" /b "C:\Users\Administrator\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Chrome 应用\Postman.lnk" 
echo    
echo   5.启动PHP集成开发环境 PhpStorm.exe
start "" /b "E:\安装软件\IntelliJIDEALicenseServer(0.0.0.0_41017)\IntelliJIDEALicenseServer_windows_386.exe"
start "" /b "C:\Program Files\JetBrains\PhpStorm 2017.1.3\bin\phpstorm64.exe"
echo   等待程序 PhpStorm.exe 启动
Ping -l 1 -n 1 -w 18000 1.1.1.1 -4 1>nul 2>&1
echo   
echo   6.启动命令行工具 Cmder.exe
start "" /b "E:\cmder_mini\Cmder.exe"
echo   
echo   7.发送git命令
start "" /b "C:\Users\Administrator\Desktop\git.build.vbs"