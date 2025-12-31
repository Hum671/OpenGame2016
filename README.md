> 2026版本

# 环境安装
```
1. docker build -t kaijiang .
2. docker tag kaijiang kj/kaijiang:1.6
3. docker save -o kaijiang.tar kj/kaijiang:1.6
4. tar -czvf kaijiang.tar.gz kaijiang.tar
```

# 远程使用
```
1. tar -xzvf kaijiang.tar.gz
2. docker load -i kaijiang.tar
3. docker run -p 8888:80 -d -v /home/uploads:/var/www/html/public/uploads -v /home/config/app.php:/var/www/html/config/app.php -v /home/config/database.php:/var/www/html/config/database.php -v /home/config/cache.php:/var/www/html/config/cache.php --name kj kj/kaijiang:1.6
```

* * * * * curl http://172.17.0.2/index/index/draw >> /home/logs/test.log 2>&1
