#配置

##常用别名
```
[alias]
	lg1 = log --graph --abbrev-commit --decorate --date-order --date=relative --format=format:'%C(bold blue)%h%C(reset) - %C(bold green)(%ar)%C(reset) %C(white)%s%C(reset) %C(dim white)- %an%C(reset)%C(bold yellow)%d%C(reset)' --all
	lg2 = log --graph --abbrev-commit --decorate --date-order --format=format:'%C(bold blue)%h%C(reset) - %C(bold cyan)%aD%C(reset) %C(bold green)(%ar)%C(reset)%C(bold yellow)%d%C(reset)%n''          %C(white)%s%C(reset) %C(dim white)- %an%C(reset)' --all
	st = status
	ci = commit
	df = diff
	br = branch
	co = checkout
	cp = cherry-pick
```

##安装之后配置

###客户端安装git 必须的操作 基础配置
```
git config --global user.mail "user@mail.com"
git config --global user.name "user_name"
git config --global core.autocrlf false   //解决跨平台 符号的问题
git config --global pull.rebase true  //采用rebase模式，单项目建议采用，这样进行就像一条线一样，多项目的话，采用merget ,
git config --global gui.encoding utf-8 //遇乱码的设置
```
---

##全局ignore配置
```
[core]
	autocrlf = false
	quotepath = false
	pager = diff-so-fancy | less --tabs=4 -RFX
	excludesfile = C:/Users/cmk/.gitignore_global
```

