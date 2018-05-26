incsrc "sa1detect.asm"
!players    = $00   ;00 = 1 player, 01 = 2 player

org $009DFA
    inc $0100|!addr
    bra +
    
org $009E0B
    +
    ldx.b #!players
    
org $05B872
    db $FF
