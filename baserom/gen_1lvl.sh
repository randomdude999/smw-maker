#!/bin/bash
cp clean_sa1.smc 1lvl_base.smc
./asar 1lvl.asm 1lvl_base.smc
cp 1lvl_base.smc ../smw_maker_base_1lvl.smc
