#!/bin/bash
screen -S starmade -p 0 -X stuff "$1$(printf \\r)"