#!/bin/bash
cp clean_sa1.smc 10lvl_base.smc
./asar 10lvl.asm 10lvl_base.smc
cp 10lvl_base.smc ../smw_maker_base_10lvl.smc
