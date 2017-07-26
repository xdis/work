# b70刷机

# 备份

```
dd if=/dev/mtd7 of=/tmp/bdinfo.bin
dd if=/dev/mtd11 of=/tmp/oem.bin
```

## 删除备份文件 

```
rm -f /tmp/*.bin
rm -f /www/*.bin

```

