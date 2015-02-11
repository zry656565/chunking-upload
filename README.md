# 分片测试报告

使用DigitalOcean位于旧金山的服务器，所以访问速度比较慢，请不要尝试太大的文件。

- 测试环境：上海交通大学教育网 - DigitalOcean 旧金山机房
- 操作系统：Ubuntu 14.04.1 LTS
- 服务器：Apache/2.4.7(ubuntu)
- PHP：5.5.9-1ubuntu4.5
- 测试时间：2015.2.11 23:40:00 - 2015.2.12 00:30:00

测试文件 | 文件大小 | 分片大小 | 分片数 | 上传总花费时间
----|------|----|-----|-----|----
Cyberduck-4.6.2.zip | 57.9MB  | 2MB | 28 | 136.486s
Cyberduck-4.6.2.zip | 57.9MB  | 4MB | 14 | 136.194s
Cyberduck-4.6.2.zip | 57.9MB  | 8MB | 7 | 152.207s
android-studio-ide-1641136.dmg | 245.7MB | 2MB | 118 | 567.218s
android-studio-ide-1641136.dmg | 245.7MB | 4MB | 59 | 571.467s
android-studio-ide-1641136.dmg | 245.7MB | 8MB | 31 | 524.878s

目前还没找到什么规律可循，有时间需要做更多测试。
