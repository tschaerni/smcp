#!/bin/bash
screen -S starmade -p 0 -X stuff "/chat $1$(printf \\r)"