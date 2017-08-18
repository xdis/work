# 安装pathogen
> 官网 https://github.com/tpope/vim-pathogen

## 安装

```
mkdir -p ~/.vim/autoload ~/.vim/bundle && \
curl -LSso ~/.vim/autoload/pathogen.vim https://tpo.pe/pathogen.vim
```

## 编辑vimrc
**vim ~/.vimrc**
```
execute pathogen#infect()

如果文件为空

execute pathogen#infect()
syntax on
filetype plugin indent on

```

