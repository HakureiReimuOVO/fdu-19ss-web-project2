# Project2
> 19302010009 钱麒丹  
> GitHub地址: https://github.com/HakureiReimuOVO/fdu-19ss-web-project2/  
> 服务器地址: http://cymqqdwebfundamental.ink/
## Project2目录
/fdu-19ss-web-project2  
.../src  
....../CSS  
........./项目使用到的CSS  
....../项目使用到的PHP  
....../除主页外的其他网页  
.../img  
....../necessary-images  
........./项目使用到的图片    
....../travel-images  
........./用户上传的图片(medium,small,tiny三种尺寸)      
.../index.php  
.../travels.sql  
.../readme.md  

+ index.php为网站主页。
+ login.php为登录页面。
+ register.php为注册页面。
+ browser.php为筛选图片的浏览页。
+ search.php为搜索图片的搜索页。
+ my_photo.php为用户照片的页面。
+ my_favor.php为用户收藏照片的页面。
+ upload.php为用户上传照片的页面。
+ modify.php为用户修改上传照片的页面。
+ details.php为照片的详情页面。
## Project2完成情况
已完成Project2中的基本要求，通过js以及php在各页面上都实现了相对应的功能，在Project1的页面布局基础上实现了可发布网站的基本逻辑。
## Bonus完成情况
完成了此次Project2中的Bonus1和3。
### Bonus1：密码加盐
我的实现方法是通过当前时间戳time()，再进行md5摘要算法生成该账户的Salt，最终将Password和Salt串联进行md5加密运算，并将结果保存于服务器端。在登录时通过服务器端保存的Salt复现以上步骤实现登录逻辑。
### Bonus2：前端框架
选择自己一砖一瓦搭建网站，故放弃该Bonus。(其实是因为没能力)
### Bonus3：服务器部署
主要通过远程ssh命令对服务器运行环境进行部署，scp命令进行文件对接，实现服务器可访问。再通过网站备案以及域名实名，将域名解析地址转到服务器公网IP上，做到通过域名展示Project2。
## 对Project2和本门课程的意见和建议
非常满意!(一人血书中文课本)