# php-crontab
PHP计划任务工具与服务器crontab一样的命令方式,无需重启执行任务。
解决win下无法直接使用crontab和增强linux crontab无秒级别执行控制。


PHP crontab 使用方法：
   
1.在config.ini中配置要执行的计划任务

    配置示例 
            run_time = */2 */1 * * * *  (执行周期： 秒 分 时 日 月 周)
     
            cd_dir = /root/test/ (脚本所在目录，建议绝对路径)
             
            common = php demo.php (待执行的命令)
             
            log_dir = /root/log/demo.log (脚本输出日志文件，建议绝对路径)
             
2.在php-cli窗口执行run.php

3.常用计划任务周期示例:待补
