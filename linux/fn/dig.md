# dig

## dig使用

### 测试该域名能否正常解析

#### 解析成功

```
$ dig @8.8.8.8 vding.wang                                               
                                                                        
; <<>> DiG 9.9.8 <<>> @8.8.8.8 vding.wang                               
; (1 server found)                                                      
;; global options: +cmd                                                 
;; Got answer:                                                          
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 48836               
;; flags: qr rd ra; QUERY: 1, ANSWER: 1, AUTHORITY: 0, ADDITIONAL: 1    
                                                                        
;; OPT PSEUDOSECTION:                                                   
; EDNS: version: 0, flags:; udp: 512                                    
;; QUESTION SECTION:                                                    
;vding.wang.                    IN      A                               
                                                                        
;; ANSWER SECTION:                                                      
vding.wang.             599     IN      A       202.104.102.4           
                                                                        
;; Query time: 565 msec                                                 
;; SERVER: 8.8.8.8#53(8.8.8.8)                                          
;; WHEN: Thu May 11 17:03:36 ?D1ú±ê×?ê±?? 2017                          
;; MSG SIZE  rcvd: 55                                                   
                                                                        
```

#### 解析失败
![](dig/dig_fail.png)


### 查询所有DNS记录

#### vding为例

```
$ dig -t ANY vding.wang +answer                                                                                                
                                                                                                                               
; <<>> DiG 9.9.8 <<>> -t ANY vding.wang +answer                                                                                
;; global options: +cmd                                                                                                        
;; Got answer:                                                                                                                 
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 52258                                                                      
;; flags: qr rd ra; QUERY: 1, ANSWER: 7, AUTHORITY: 0, ADDITIONAL: 13                                                          
                                                                                                                               
;; OPT PSEUDOSECTION:                                                                                                          
; EDNS: version: 0, flags:; udp: 4000                                                                                          
;; QUESTION SECTION:                                                                                                           
;vding.wang.                    IN      ANY                                                                                    
                                                                                                                               
;; ANSWER SECTION:                                                                                                             
vding.wang.             600     IN      TXT     "v=spf1 include:spf.mxhichina.com -all"                                        
vding.wang.             600     IN      SOA     f1g1ns1.dnspod.net. freednsadmin.dnspod.com. 1493802758 3600 180 1209600 180   
vding.wang.             86400   IN      NS      f1g1ns2.dnspod.net.                                                            
vding.wang.             86400   IN      NS      f1g1ns1.dnspod.net.                                                            
vding.wang.             600     IN      MX      10 mxw.mxhichina.com.                                                          
vding.wang.             600     IN      MX      5 mxn.mxhichina.com.                                                           
vding.wang.             600     IN      A       202.104.102.4                                                                  
                                                                                                                               
;; ADDITIONAL SECTION:                                                                                                         
f1g1ns1.dnspod.net.     93714   IN      A       180.163.19.15                                                                  
f1g1ns1.dnspod.net.     93714   IN      A       182.140.167.166                                                                
f1g1ns1.dnspod.net.     93714   IN      A       14.215.150.17                                                                  
f1g1ns1.dnspod.net.     93714   IN      A       115.236.151.191                                                                
f1g1ns1.dnspod.net.     93714   IN      A       58.247.212.36                                                                  
f1g1ns1.dnspod.net.     93714   IN      A       125.39.208.193                                                                 
f1g1ns2.dnspod.net.     93704   IN      A       101.226.30.224                                                                 
f1g1ns2.dnspod.net.     93704   IN      A       182.140.167.188                                                                
f1g1ns2.dnspod.net.     93704   IN      A       101.226.220.16                                                                 
f1g1ns2.dnspod.net.     93704   IN      A       52.220.136.67                                                                  
f1g1ns2.dnspod.net.     93704   IN      A       61.129.8.159                                                                   
f1g1ns2.dnspod.net.     93704   IN      A       121.51.128.164                                                                 
                                                                                                                               
;; Query time: 250 msec                                                                                                        
;; SERVER: 202.96.134.33#53(202.96.134.33)                                                                                     
;; WHEN: Thu May 11 16:56:28 ?D1ú±ê×?ê±?? 2017                                                                                 
;; MSG SIZE  rcvd: 460                                                                                                         

```

#### baidajob为例

```
$ dig -t ANY baidajob.com +answer                                                                   
                                                                                                    
; <<>> DiG 9.9.8 <<>> -t ANY baidajob.com +answer                                                   
;; global options: +cmd                                                                             
;; Got answer:                                                                                      
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 45151                                           
;; flags: qr rd ra; QUERY: 1, ANSWER: 6, AUTHORITY: 0, ADDITIONAL: 30                               
                                                                                                    
;; OPT PSEUDOSECTION:                                                                               
; EDNS: version: 0, flags:; udp: 4000                                                               
;; QUESTION SECTION:                                                                                
;baidajob.com.                  IN      ANY                                                         
                                                                                                    
;; ANSWER SECTION:                                                                                  
baidajob.com.           3600    IN      TXT     "v=spf1 include:spf.163.com -all"                   
baidajob.com.           86400   IN      NS      vip2.alidns.com.                                    
baidajob.com.           86400   IN      NS      vip1.alidns.com.                                    
baidajob.com.           3600    IN      MX      10 qiye163mx02.mxmail.netease.com.                  
baidajob.com.           3600    IN      MX      5 qiye163mx01.mxmail.netease.com.                   
baidajob.com.           3600    IN      A       119.147.213.164                                     
                                                                                                    
;; ADDITIONAL SECTION:                                                                              
vip1.alidns.com.        99      IN      A       140.205.228.51                                      
vip1.alidns.com.        99      IN      A       140.205.81.53                                       
vip1.alidns.com.        99      IN      A       180.97.161.227                                      
vip1.alidns.com.        99      IN      A       140.205.228.53                                      
vip1.alidns.com.        99      IN      A       140.205.81.51                                       
vip1.alidns.com.        99      IN      A       218.60.112.225                                      
vip1.alidns.com.        99      IN      A       14.1.112.13                                         
vip1.alidns.com.        99      IN      A       47.88.44.151                                        
vip1.alidns.com.        99      IN      A       180.97.161.225                                      
vip1.alidns.com.        99      IN      A       218.60.112.227                                      
vip1.alidns.com.        99      IN      A       47.88.44.153                                        
vip1.alidns.com.        99      IN      A       14.1.112.11                                         
vip2.alidns.com.        175     IN      A       140.205.228.54                                      
vip2.alidns.com.        175     IN      A       47.88.44.152                                        
vip2.alidns.com.        175     IN      A       218.60.112.226                                      
vip2.alidns.com.        175     IN      A       14.1.112.12                                         
vip2.alidns.com.        175     IN      A       180.97.161.226                                      
vip2.alidns.com.        175     IN      A       180.97.161.224                                      
vip2.alidns.com.        175     IN      A       140.205.228.52                                      
vip2.alidns.com.        175     IN      A       140.205.81.54                                       
vip2.alidns.com.        175     IN      A       47.88.44.154                                        
vip2.alidns.com.        175     IN      A       14.1.112.14                                         
vip2.alidns.com.        175     IN      A       140.205.81.52                                       
vip2.alidns.com.        175     IN      A       218.60.112.224                                      
qiye163mx02.mxmail.netease.com. 56 IN   A       123.125.50.217                                      
qiye163mx02.mxmail.netease.com. 56 IN   A       123.125.50.220                                      
qiye163mx02.mxmail.netease.com. 56 IN   A       123.125.50.213                                      
qiye163mx02.mxmail.netease.com. 56 IN   A       123.125.50.214                                      
qiye163mx02.mxmail.netease.com. 56 IN   A       123.125.50.219                                      
                                                                                                    
;; Query time: 100 msec                                                                             
;; SERVER: 202.96.134.33#53(202.96.134.33)                                                          
;; WHEN: Thu May 11 16:57:25 ?D1ú±ê×?ê±?? 2017                                                      
;; MSG SIZE  rcvd: 681                                                                              
                                                                                                    

```
