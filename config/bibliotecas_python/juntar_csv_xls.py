#!/usr/bin/env python

#apt-get install python-pip
#sudo python -m pip install xlwt-1.0.0-py2.py3-none-any.whl

import os
import glob
import csv
import xlwt

#print("starting new workbook")
wb = xlwt.Workbook()

for csvfile in glob.glob(os.path.join('/tmp/', '*.csv')):
        fpath = csvfile.split("/")
	fname = fpath[-1].split(".", 1) ## fname[0] should be our worksheet name
	sheetname = fname[0][-30:]
	print("adding sheet " + sheetname)
	ws = wb.add_sheet(sheetname)
	with open(csvfile, 'rb') as f:
		reader = csv.reader(f, delimiter=';')
		for r, row in enumerate(reader):
			for c, col in enumerate(row):
				ws.write(r, c, col)

print("saving workbook")
wb.save('/tmp/output.xls')
print('/tmp/output.xls')
