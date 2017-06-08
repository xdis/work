# shell

## 让你的git pull之后自动 运行 yarn run build

### 调用函数代码

![](shell/git_push_after.png)
```
#! /bin/bash
#
# post-checkout hook that checks for changes to composer.lock, and fires a composer install if required.
# Copyright (C) 2017 HuangYeWuDeng <hacklog@80x86>
#
# Distributed under terms of the MIT license.
#
# git hook to run a command after `git pull` if a specified file was changed
# Run `chmod +x post-merge` to make it executable then put it into `.git/hooks/`.

changed_files="$(git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD)"

check_run() {
	echo "$changed_files" | grep --quiet "$1" && eval "$2"
}

# you can change composer path and parameters as you need.
check_run vfet "cd vfet && yarn run build"

```