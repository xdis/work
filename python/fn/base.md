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

## try/except/while

```python
#!/usr/bin/python3
print("=======欢迎进入狗狗年龄对比系统========")
while True:
    try:
        age = int(input("请输入您家狗的年龄:"))
        print("")
        age = float(age)
        if age < 0:
            print("您在逗我？")
        elif age == 1:
            print("相当于人类14岁")
            break
        elif age == 2:
            print("相当于人类22岁")
            break
        else:
            human = 22 + (age - 2) * 5
            print("相当于人类：", human)
            break
    except ValueError:
             print("输入不合法，请输入有效年龄")
###退出提示
input("点击 enter 键退出")

```

# while循环
## while_计算总和
```python
#!/usr/bin/python3
n=100
sum=0
counter =1
while counter <=n:
    sum =sum+counter
    counter+=1

print("1到 %d 之和为:%d" %(n,sum)) # 1到 100 之和为:5050
```

## rang计算总和
```python
#!/usr/bin/python3

sum=0
i=0;
for i in range(1,101): #n 范围 0-100
    sum += i

print(sum) 
```
**输出** 
```
5050
```

## 无限循环

```python
#!/usr/bin/python3

var =1
while var ==1:  # 表达式永远为 true
    num = int(input("输入一个数字 :"))
    print("你输入的数字是: ",num)

print("Good bye")

'''
输出
输入一个数字 :4
你输入的数字是:  4
输入一个数字 :5
你输入的数字是:  5
输入一个数字 :3
你输入的数字是:  3
输入一个数字 :5
你输入的数字是:  5
'''

```

## while循环使用else语句

```python
#!/usr/bin/python3

count =0
while count<5:
    print(count," 小于 5")
    count = count +1
else:
    print(count," 大于或等于 5")
```

**输出** 
```
0  小于 5
1  小于 5
2  小于 5
3  小于 5
4  小于 5
5  大于或等于 5
```

# for
## 循环输出数组

```python
#!/usr/bin/python3

language = ['a','b','c','d','e','f']
for x in language:
    print(x,end=',')
```

**输出** 
```
a,b,c,d,e,f,
```

## 循环使用break
```python
#!/usr/bin/python3
 
sites = ["Baidu", "Google","Runoob","Taobao"]
for site in sites:
    if site == "Runoob":
        print("菜鸟教程!")
        break
    print("循环数据 " + site)
else:
    print("没有循环数据!")
print("完成循环!")
```

**输出** 
```
循环数据 Baidu
循环数据 Google
菜鸟教程!
完成循环!
```

# break和continue
## break和continue语句及循环中的else子句
```python
for letter in 'Runoob':  # 第一个实例
    if letter == 'b':
        break
    print('当前字母为 :', letter)

var = 10  # 第二个实例
while var > 0:
    print('当期变量值为 :', var)
    var = var - 1
    if var == 5:
        break

print("Good bye!")
```
**输出** 
```
当前字母为 : R
当前字母为 : u
当前字母为 : n
当前字母为 : o
当前字母为 : o
当期变量值为 : 10
当期变量值为 : 9
当期变量值为 : 8
当期变量值为 : 7
当期变量值为 : 6
Good bye!
```
## continue语句
>continue语句被用来告诉Python跳过当前循环块中的剩余语句，然后继续进行下一轮循环。  

```python
#!/usr/bin/python3

for letter in 'Runoob':  # 第一个实例
    if letter == 'o':  # 字母为 o 时跳过输出
        continue
    print('当前字母 :', letter)

var = 10  # 第二个实例
while var > 0:
    var = var - 1
    if var == 5:  # 变量为 5 时跳过输出
        continue
    print('当前变量值 :', var)
print("Good bye!")
```

**输出** 

```
当前字母 : R
当前字母 : u
当前字母 : n
当前字母 : b
当前变量值 : 9
当前变量值 : 8
当前变量值 : 7
当前变量值 : 6
当前变量值 : 4
当前变量值 : 3
当前变量值 : 2
当前变量值 : 1
当前变量值 : 0
Good bye!
```

# 函数
# range
## 生成数列
```python
#!/usr/bin/python3

for i in range(5):
    print(i)
```
**输出** 
```
0
1
2
3
4
```
## 指定区间的值
```python
#!/usr/bin/python3

for i in range(5,9): #范围在5-8
    print(i)
```
**输出** 
```
5
6
7
8
```
## 步长
```python
#!/usr/bin/python3

for i in range(5,9,2):
    print(i)
```
**输出** 
```
5
7
```
## 结合range()和len()函数以遍历一个序列的索引
```python
#!/usr/bin/python3

a = ['Google', 'Baidu', 'Runoob', 'Taobao', 'QQ']
for i in range(len(a)):
    print(i, a[i])
    
```
**输出** 
```
0 Google
1 Baidu
2 Runoob
3 Taobao
4 QQ
```

# enumerate
## for i,j
```python
#!/usr/bin/python3

arr = [12,14,20,25,28,30,40]

for i,j in enumerate(arr):
    print(i,j)
```
**输出** 
```
0 12
1 14
2 20
3 25
4 28
5 30
6 40
```

# 迭代器
>访问集合元素的一种方式  
>迭代器是一个可以记住遍历的位置的对象。  
>迭代器对象从集合的第一个元素开始访问，直到所有的元素被访问完结束。迭代器只能往前不会后退。  
>迭代器有两个基本的方法：iter() 和 next()。  
>字符串，列表或元组对象都可用于创建迭代器：  


## 逐条输出
```python
#!/usr/bin/python3

list=[1,2,3,4]
it = iter(list)
print(next(it)) # 输出 1

print(next(it)) # 输出 2

```

## for遍历
```python
#!/usr/bin/python3

list=[1,2,3,4]
it=iter(list)
for i in it:
    print(i)
```
**输出** 
```
1
2
3
4
```
## 使用sys模块,使用next()
```python
#!/usr/bin/python3
import sys
list=[1,2,3,4]
it=iter(list)

while True:
    try:
        print(next(it))
    except StopIteration:
        sys.exit()
```
**输出** 
```
1
2
3
4
```

# 生成器
## yield实现斐波那契数列
```python
#!/usr/bin/python3
import sys

def fibonacci(n): # 生成器函数 - 斐波那契
    a, b, counter = 0, 1, 0
    while True:
        if (counter > n):
            return
        yield a
        a, b = b, a + b
        counter += 1
f = fibonacci(10) # f 是一个迭代器，由生成器返回生成

while True:
    try:
        print (next(f), end=" ")
    except StopIteration:
        sys.exit()
```
**输出** 
```
0 1 1 2 3 5 8 13 21 34 55 
```

# 匿名函数
## lambda创建匿名函数
```python
#!/usr/bin/python3

# 可写函数说明
sum = lambda s1,s2:s1+s2;

# 调用sum函数
print("相加后的值为: ",sum(10,20))
print("相加后的值为: ",sum(20,20))
```
**输出** 
```
相加后的值为:  30
相加后的值为:  40
```
# 变量作用域

## global_demo
```python
num = 1
def fun1():
    global num  # 需要使用 global 关键字声明
    print(num)
    num = 123
    print(num)
fun1()
```
**输出** 
```
1
123
```

## nonlocal_demo
>如果要修改嵌套作用域（enclosing 作用域，外层非全局作用域）中的变量则需要 nonlocal 关键字了，如下实例：  

```python
#!/usr/bin/python3

def outer():
    num = 10
    def inner():
        nonlocal num   # nonlocal关键字声明
        num = 100
        print(num)
    inner()
    print(num)
outer()
```
**输出** 
```
100
100
```

# 类对象
## 访问类的属性和方法
```python
#!/usr/bin/python3

class Father:
    i=12345
    def f(self):
        return 'hello world'

# 实例化类
x = Father()

# 访问类的属性和方法
print("访问类的属性i " ,x.i)
print("访问类的方法f ",x.f() )
```
**输出** 
```
访问类的属性i  12345
访问类的方法f  hello world
```

## 构造函数__init__
> 类方法必须包含参数 self, 且为第一个参数，self 代表的是类的实例。  

```python
#!/usr/bin/python3

class Student:
    def __init__(self,realpart,imagpart):
        self.r = realpart
        self.i = imagpart

x=Student(4,5)
print(x.r,x.i)
```
**输出** 
```
4 5

```

## self代表类的实例_而非类
>类方法必须包含参数 self, 且为第一个参数，self 代表的是类的实例。  

```python
#!/usr/bin/python3

class Student:
    def prt(self):
        print(self)
        print(self.__class__)

x=Student()
x.prt()
```
**输出** 
```
<test.Student object at 0x034C2CF0>
<class 'test.Student'>
```
### 或者将self换成别的名字
```python
#!/usr/bin/python3

class Student:
    def prt(pooo):
        print(pooo)
        print(pooo.__class__)

x=Student()
x.prt()
```
**输出** 
```
<test.Student object at 0x03092A70>
<class 'test.Student'>
```

## 类的方法_def和self
> 在类地内部，使用 def 关键字来定义一个方法，与一般函数定义不同，类方法必须包含参数 self, 且为第一个参数，self 代表的是类的实例。  

```python
#!/usr/bin/python3

#类定义
class people:
    # 定义基本属性
    name =''
    age =0
    # 定义私有属性,私有属性在类外部无法直接进行访问
    __weight =0
    # 定义构造方法
    def __init__(self,_name,_age,_weight):
        self.name = _name
        self.age = _age
        self.__weight = _weight

    def speak(self):
        print("%s 说: 我 %d 岁."%(self.name,self.age))

# 实例化类
p=people('peter',10,30)
p.speak()
print(p.name)
```
**输出** 
```
peter 说: 我 10 岁.
peter
```