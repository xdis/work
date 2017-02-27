

#git推送数据的时候，竟然触发composer升级

##查看是怎样运作？

## 升级过程中...
```

remote: Executing command (/var/www/t.vding.wang): git describe --exact-match --tags
remote: Executing command (/var/www/t.vding.wang): git log --pretty="%H" -n1 HEAD
remote: Reading /home/git/.composer/composer.json
remote: Loading config file /home/git/.composer/auth.json
remote: Loading config file /home/git/.composer/composer.json
remote: Loading config file /home/git/.composer/auth.json
remote: Reading /home/git/.composer/auth.json
remote: Reading /var/www/t.vding.wang/vendor/composer/installed.json
remote: Reading /home/git/.composer/vendor/composer/installed.json
remote: Loading plugin yii\composer\Plugin
remote: Loading plugin Fxp\Composer\AssetPlugin\FxpAssetPlugin
remote: Running 1.2.4 (2016-12-06 22:00:51) with PHP 7.0.13 on Linux / 2.6.32-504.12.2.el6.x86_64
remote: Reading ./composer.lock
remote: Loading composer repositories with package information
remote: Installing dependencies from lock file
remote: Reading ./composer.lock
remote: Resolving dependencies through SAT
remote: Dependency resolution completed in 0.017 seconds
remote: Analyzed 267 packages to resolve dependencies
remote: Analyzed 601 rules to resolve dependencies
remote:   - Removing bower-asset/blueimp-load-image (v2.11.0)
remote:   - Installing bower-asset/blueimp-load-image (v2.12.2)
remote: Downloading https://api.github.com/repos/blueimp/JavaScript-Load-Image/zipball/caf36d45d843b38b8763cb4a921d7ce51681a08c
remote:     Downloading: 100%
remote: Writing /home/git/.composer/cache/files/bower-asset/blueimp-load-image/44a4a2b09f7a0ade4e0b3f6a00708a63e4ff2146.zip into cache from /var/www/t.vding.wang/vendor/bower/blueimp-load-image/64e6c2b0c9d29f0f0de5c5c04744752f
remote:     Extracting archive
remote: Executing command (CWD): unzip '/var/www/t.vding.wang/vendor/bower/blueimp-load-image/64e6c2b0c9d29f0f0de5c5c04744752f' -d '/var/www/t.vding.wang/vendor/composer/ab041c1b' && chmod -R u+w '/var/www/t.vding.wang/vendor/composer/ab041c1b'
remote:
remote:     REASON: Required by the root package: Install command rule (install bower-asset/blueimp-load-image v2.12.2)
remote:
remote:   - Removing jpush/jpush (v3.5.11)
remote:   - Installing jpush/jpush (v3.5.12)
remote: Downloading https://api.github.com/repos/jpush/jpush-api-php-client/zipball/5be1aff7bb70ecc4cc251dc26802443c0c4320d5
remote:     Downloading: 100%
remote: Writing /home/git/.composer/cache/files/jpush/jpush/ba2f89abeb29dc15746cc9336d16671dc2b05c3a.zip into cache from /var/www/t.vding.wang/vendor/jpush/jpush/eecf15e384286a954940c45dff3b3986
remote:     Extracting archive
remote: Executing command (CWD): unzip '/var/www/t.vding.wang/vendor/jpush/jpush/eecf15e384286a954940c45dff3b3986' -d '/var/www/t.vding.wang/vendor/composer/8f40a843' && chmod -R u+w '/var/www/t.vding.wang/vendor/composer/8f40a843'
remote:
remote:     REASON: Required by the root package: Install command rule (install jpush/jpush v3.5.12)
remote:
remote:   - Removing studio-42/elfinder (2.1.21)
remote:   - Installing studio-42/elfinder (2.1.22)
remote: Downloading https://api.github.com/repos/Studio-42/elFinder/zipball/a41ea2f9640b6a7415d438568c708e9bcfc2e653
remote:     Downloading: 10%

```