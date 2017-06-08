# meld
> 官网  http://meldmerge.org  

## 配置

### window

```
[alias]
	co = checkout
	ci = commit
	st = status
	stn = status -uno
	br = branch
	cp = cherry-pick
	unstage = reset HEAD --
    dt = difftool
    dtd = difftool --dir-diff
    mt = mergetool
[diff]
	tool = meld
[difftool]
	prompt = false
	bc = trustExitCode
[difftool "meld"]
	#path = /usr/bin/meld
	#path = C:\\Program Files (x86)\\Meld\\Meld.exe
	cmd = 'C:\\Program Files (x86)\\Meld\\Meld.exe' "$LOCAL" "$REMOTE"
[merge]
	tool = meld
[mergetool]
	prompt = false
[mergetool "meld"]
	path = C:\\Program Files (x86)\\Meld\\Meld.exe
	#the first is the default
	#cmd = 'C:\\Program Files (x86)\\Meld\\Meld.exe' "$LOCAL" "$BASE" "$REMOTE" --output "$MERGED"
	#cmd = 'C:\\Program Files (x86)\\Meld\\Meld.exe' "$LOCAL" "$MERGED" "$REMOTE" --output "$MERGED"
 	keepBackup = false                                   
    	trustExitCode = false
```

### linux

```
[diff]
	tool = meld
[difftool]
	prompt = false
	bc = trustExitCode
[difftool "meld"]
	#path = /usr/bin/meld
	cmd = meld "$LOCAL" "$REMOTE"
[difftool "bc"]
	path = /usr/bin/bcompare
[difftool "vimdiff"]
	cmd = gvimdiff "$REMOTE" "$LOCAL" "$BASE"
[merge]
	tool = meld
[mergetool]
	prompt = false
[mergetool "bc"]
	path = /usr/bin/bcompare
	bc = trustExitCode
[mergetool "meld"]
keepBackup = false
	path = /usr/bin/meld
	#the first is the default
	#cmd = meld "$LOCAL" "$BASE" "$REMOTE" --output "$MERGED"
	#cmd = meld "$LOCAL" "$MERGED" "$REMOTE" --output "$MERGED"

[alias]
	co = checkout
	ci = commit
	st = status
	stn = status -uno
	br = branch
	cp = cherry-pick
	unstage = reset HEAD --
    dt = difftool
    dtd = difftool --dir-diff
    mt = mergetool
```