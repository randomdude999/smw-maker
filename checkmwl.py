#!/usr/bin/env python3
import sys
import struct

sections = [
	"level",
	"layer1",
	"layer2",
	"sprite",
	"palette",
	"entrance",
	"exanim",
	"bypass"
]

with open(sys.argv[1], 'rb') as f:
	data = f.read()

if len(data) < 0x40:
	print("Invalid MWL header")
	sys.exit(1)

if data[0:2] != b"LM":
	print("Invalid MWL header")
	sys.exit(1)

(data_ptr_start, data_ptr_len) = struct.unpack("<ii", data[4:12])
if data_ptr_start+data_ptr_len > len(data):
	print("Invalid MWL header")
	sys.exit(1)

for i in range(0, data_ptr_len, 8):
	sec = sections[i//8]
	(secptr, seclen) = struct.unpack("<ii", data[data_ptr_start + i : data_ptr_start+i+8])
	if secptr+seclen > len(data):
		print(f"Invalid MWL pointer table")
		sys.exit(1)

# at this point we shouldn't get segfaults while inserting levels, but they could still crash the ROM
