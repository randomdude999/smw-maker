
incsrc "sa1detect.asm"
namespace hexEdits
incsrc "hex_edits.asm"
namespace noow
incsrc "noow.asm"
namespace deathcounter
incsrc "deathcount.asm"
namespace _1or2player
incsrc "1or2player.asm"
namespace _10lvl

org $008CCD ; write "LVL" text
    db $15,$38 ; L
    db $1F,$38 ; V
    db $15,$38 ; L

org $008F98
    ; overwrite the part of the statusbar code that writes bonus stars, replace it with writing the levelnum
    LDA $13BF|!addr
    JSR HexDec
    STX $0F1E|!addr
    STA $0F1F|!addr
    JMP $8FC5
; Leaves ones in A and tens in X.
HexDec:
    LDX #$00
-   CMP #$0A
    BCC +
    SBC #$0A
    INX
    BRA -
+   RTS
    warnpc $8FC5
