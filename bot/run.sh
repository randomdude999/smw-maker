#!/bin/sh

cd "$(dirname "$0")"
venv/bin/python main.py >>output.log 2>>error.log
