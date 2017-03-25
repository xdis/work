# 问题

## 测试推向正式服错
报错内容如下:  
```php 
//1.数据已经拉取远程
cmk@xxx MINGW64 /e/cmk/qian100/web/vding/anyang-vding (anyang)
$ git pull origin anyang
From ssh://git.vding.wang:5808/anyang-vding
 * branch            anyang     -> FETCH_HEAD
Already up-to-date.


//2.在推送到远程服务器
$ git push origin anyang
Everything up-to-date

//3.推向到正式的仓库报错
$ git push prod anyang                                                                
To ssh://git@v2.vding.wang:5804/anyang-vding.git                                      
 ! [rejected]        anyang -> anyang (non-fast-forward)                              
error: failed to push some refs to 'ssh://git@v2.vding.wang:5804/anyang-vding.git'    
hint: Updates were rejected because the tip of your current branch is behind          
hint: its remote counterpart. Integrate the remote changes (e.g.                      
hint: 'git pull ...') before pushing again.                                           
hint: See the 'Note about fast-forwards' in 'git push --help' for details. 
           
```
## 回答 
> 经过分析,根本没有使用测试/开发环境的anyang分支,直接将anyang-test的分支cp到线上分支的 anyang-test,推送,再合并到线上的anyang分支!
