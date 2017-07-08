# core

- 高并发处理
    - redis
        - [redis的List类型实现秒杀_例子](../php/fn/redis/miaosha.md#redis的List类型实现秒杀_例子)
            - LPUSH/LPUSHX :将值插入到（/存在的）列表头部
            - RPUSH/RPUSHX :将值插入到（/存在的）列表尾部	
            - LPOP :移出并获取列表的第一个元素
            - RPOP :移出并获取列表的最后一个元素
            - LTRIM :保留指定区间内的元素
            - LLEN  :获取列表长度
            - LSET  :通过索引设置列表元素的值
            - UNDEX :通过索引获取列表中的元素
            - LRANGE :获取列表指定范围内的元素
    - [RabbitMQ](../php/fn/RabbitMQ/README.md)  更专业的消息系统实现方案  @WiconWang