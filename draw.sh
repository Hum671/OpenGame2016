#!/bin/bash
. /etc/profile
basepath=$(cd `dirname $0`; pwd|awk -F'sh' '{print $1}')
cd $basepath
php think draw
