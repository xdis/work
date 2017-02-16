


## 基本使用

```php
        <?php echo $form->field($model, 'to_at', ['options' => ['class' => 'col-lg-2'],])->widget(
            DateTimeWidget::className(), [
                'phpMomentMapping' => ["yyyy-MM-dd HH:mm" => 'YYYY-MM-DD HH:mm',],
                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
                'locale' => 'zh-CN',

            ]
        ) ?>
```