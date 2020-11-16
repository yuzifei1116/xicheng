@title MySQL backup start
@echo off
setlocal enabledelayedexpansion
@color 0a
:: --------------------参数设置------------------------
set DATABASES=1912_huiyuan 2001_yuanshi 2004_zhongchuhui3 2005_boyiwang 2005_ridegas 2006_auction 2006_numbet 2006_zijie
set BACKUP_PATH=D:\
set FILE=www\backup_mysql\
set "Ymd=%date:~0,4%%date:~5,2%%date:~8,2%%time:~0,2%%time:~3,2%%time:~6,2%"
set USERNAME=root
set PASSWORD=root
set MYSQL=D:\phpstudy_pro\Extensions\MySQL5.7.26\bin\
set DT=30

:: --------------------开始备份------------------------
for %%D in (%DATABASES%) do (
     if exist %BACKUP_PATH%%FILE%%%D (
        echo 目录%BACKUP_PATH%%FILE%%%D已存在，无需创建
    ) else (
        echo 创建%BACKUP_PATH%%FILE%%%D        
        md %BACKUP_PATH%%FILE%%%D
    )
    :: 删除
    forfiles /p "%BACKUP_PATH%%FILE%%%D" /m %%D_*.sql -d -%DT% /c "cmd /c del /f @path"
    pushd %MYSQL%
    :: 备份
    ::mysqldump --opt --single-transaction=TRUE --user=%USERNAME% --password=%PASSWORD% --host=%HOST% --protocol=tcp --port=%PROT% --default-character-set=utf8 --single-transaction=TRUE --routines --events "%%D" > %BACKUP_PATH%%FILE%%%D\%%D_%Ymd%.sql
	::echo --user=%USERNAME% --password=%PASSWORD%  "%%D" > %BACKUP_PATH%%FILE%%%D\%%D_%Ymd%.sql
	mysqldump  "%%D" -u%USERNAME% -p%PASSWORD% > %BACKUP_PATH%%FILE%%%D\%%D_%Ymd%.sql
)
:: --------------------结束备份------------------------
@echo on