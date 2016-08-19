#!/bin/bash
nodemon -w modules -w nodes -e scss -x curl http://blizzard/compile
