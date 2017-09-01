@echo off
echo   ***********关闭编程软件***********  
echo       
echo   1.关闭PHP服务器软件 PhpStudy.exe
taskkill /f /im "phpStudy.exe"
echo   
echo   2.关闭数据库软件 SQLyog.exe
taskkill /f /im "SQLyog.exe"
echo   
echo   3.关闭命令行工具 Cmder.exe
taskkill /f /im "ConEmu64.exe"
taskkill /f /im "ConEmuC64.exe"
echo    
echo   4.关闭谷歌浏览器 Chrome.exe
taskkill /f /im "chrome.exe"
echo   
echo   5.关闭接口调试软件 Postman.exe
taskkill /f /im "chrome.exe" 
echo    
echo   6.关闭PHP集成开发环境 PhpStorm.exe
taskkill /f /im "IntelliJIDEALicenseServer_windows_386.exe"
taskkill /f /im "phpstorm64.exe"
echo   ***********关闭系统软件***********
echo 
echo   7.关闭有道笔记
taskkill /f /im "YoudaoNote.exe"
taskkill /f /im "YNoteCefRender.exe"
echo 
echo   8.关闭中信银行
taskkill /f /im "D4Ser_CITIC.exe"
taskkill /f /im "citic_certd.exe"
taskkill /f /im "citic_certd_gd.exe"
taskkill /f /im "CNCBUK2WDAdmin.exe"
taskkill /f /im "CNCBUK2WDMon.exe"
echo   
echo   9.关闭叮叮
rem taskkill /f /im "DingTalk.exe"
rem taskkill /f /im "DingTalkHelpler.exe"
rem taskkill /f /im "DocToPDF.exe"
echo   10.关闭Notepad++
taskkill /f /im "notepad++.exe"
echo   11.关闭任务管理器
taskkill /f /im "taskmgr.exe"
taskkill /f /im "taskhostw.exe"
echo   12.关闭360浏览器
taskkill /f /im "360chrome.exe"
echo   13.关闭命令行
taskkill /f /im "cmd.exe"
taskkill /f /im "conhost.exe"