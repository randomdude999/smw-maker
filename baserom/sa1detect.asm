!dp = $0000
!addr = $0000
!sram = $700000
!bank = $800000
!sa1 = 0
!deathcounter = $7F9D00 ; 5 bytes, sticking this here because why not
if read1($00FFD5) == $23
    sa1rom
    !dp = $3000
    !addr = $6000
    !sram = $41C000
    !bank = $000000
    !sa1 = 1
    !deathcounter = $4000F0
endif