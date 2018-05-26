
copy clean_2mb.smc 10lvl_base.smc
asar sa1/sa1.asm 10lvl_base.smc
asar-extremely-new --symbols=wla 10lvl.asm 10lvl_base.smc
pause