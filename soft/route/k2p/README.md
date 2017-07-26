# k2p刷机

# 备份

## 编程器固件备份（所有分区）

```
#将固件保存到内存
dd if=/dev/mtd0 of=/tmp/all.bin

#将内存固件映射到http目录
cd /www
touch all.bin
mount --bind /tmp/all.bin /www/all.bin

# 下载备份固件到计算机
http://192.168.2.1/all.bin
```

## 纯固件备份（firmware分区）

```
dd if=/dev/mtd5 of=/tmp/fs.bin
cd /www
touch fs.bin
mount --bind /tmp/fs.bin /www/fs.bin

# 下载备份固件到计算机
http://192.168.2.1/fs.bin
```

## EEPROM备份（出厂分区Factory）

```
dd if=/dev/mtd3 of=/tmp/eeprom.bin
cd /www
touch eeprom.bin
mount --bind /tmp/eeprom.bin /www/eeprom.bin

# 下载备份固件到计算机
http://192.168.2.1/eeprom.bin
```

## 删除备份文件 

```
rm -f /tmp/*.bin
rm -f /www/*.bin

```