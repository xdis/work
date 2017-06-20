# 基础

# 条件控制

## if/elif
```python
#!/usr/bin/python3
age = int(input("请输入你的年龄"))
print("")
if age < 0:
    print("你是在逗我吧!")
elif age ==1:
    print("相当于14岁的人")
elif age == 2:
    print("相当于22岁的人")
elif age > 2:
    human  = 22 + (age -2)*5
    print("对应人类年龄:",human)

input("点击 enter 键退出")
```