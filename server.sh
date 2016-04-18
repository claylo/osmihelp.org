#!/usr/bin/env bash
gulp watch &
php -S 0.0.0.0:9999 -t ./public
