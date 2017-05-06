# 调试

## beginProfile  代码区间性能检测

```php
    /**
     * http://ysk.dev/admin/demo-cache/profile
     * 检查区间的性能,如耗时多少秒
     * @author cmk
     */
    public function actionProfile() {
        \Yii::beginProfile('profile1');
        echo 'aaaabbbbccc';
        sleep(1);
        \Yii::endProfile('profile1');
        exit;
    }
````
**debug查看**

![](debug/profile.png)
