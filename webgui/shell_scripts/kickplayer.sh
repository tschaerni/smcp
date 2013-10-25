#!/bin/bash
screen -S starmade -p 0 -X stuff "/kick $1$(printf \\r)"