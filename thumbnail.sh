#########################################################################
# File Name: thumbnail.sh
# Author: Netcan
# Blog: http://www.netcan666.com
# Mail: 1469709759@qq.com
# Created Time: 2017-05-27 Sat 20:22:03 CST
#########################################################################
#!/bin/bash

cd live
while :;
do
	for f in *.m3u8
	do
		# echo ${f%.*}.jpg
		ffmpeg -loglevel panic -y -i $f -f mjpeg -vframes 1 -vf scale=320:240 ${f%.*}.jpg &> /dev/null
	done
	sleep 15;
done
