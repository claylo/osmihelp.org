#!/usr/bin/env bash
gulp watch &
SLIM_MODE=development php -S 0.0.0.0:9999 -t ./public
