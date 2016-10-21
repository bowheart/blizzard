#!/bin/bash
nodemon -w modules -w nodes -e scss -e js -x php -f start.php uri=core/cache render=false
