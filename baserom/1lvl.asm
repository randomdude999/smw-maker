
incsrc "sa1detect.asm"
namespace hexEdits
incsrc "hex_edits.asm"
namespace deathcounter
incsrc "deathcount.asm"
namespace _1lvl

; One file, one player
org $009CBB ;\ I guess this is the 1 file part?
    RTS     ;/
org $009D38
    LDX #$00         ;\one player game
    STX $0DB2|!addr  ;/
    JMP $9E10

; called on level end: trigger credits
org $00CA01
    LDX #$08         ;\ show cutscene 8 (credits)
    STX $13C6|!addr  ;/
    LDY #$18         ;\ switch to gamemode cutscene
    STY $0100|!addr  ;/
    INC $13D9|!addr  ; what is this
    LDA #$01         ;\ Write fade direction
    STA $0DAF|!addr  ;/
    RTS

; No Overworld (small part of it)
org $00A099
    BEQ $00  ; make the OW never appear, load the level directly

;fix midways
org $05D9C6
    BRA $01
org $05D9D4
    LDA $13CE|!addr
    BRA $03

org $05D83E
    LoadLevel:
    STZ $0F
    LDX $0100|!addr
    CPX #$03
    BNE .NotIntro
    LDA $0109|!addr
    JMP.w LoadEnd3
    .NotIntro
    LDX $010A|!addr
    LDA $0109|!addr
    BNE .Init
    LDA $13BF|!addr
    BNE +
    INC A
    STA $13BF|!addr
    +
    JMP.w LoadEnd

    .Init
    STZ $13BF|!addr
    LDA $0109|!addr
    JMP.w LoadEnd3

    ; using this as freespace
    FixMusic:
    LDA #$3F
    STA $0DDA|!addr
    STZ $2143|!addr
    PLP
    JML $0080E7

org $05D89F
LoadEnd:
    CMP #$25
    BCC LoadEnd2
    INC $0F
LoadEnd3:
    SEC
    SBC #$24
    BRA $00
LoadEnd2:
    STA $0E

org $0080E3 ; Fixes music not playing in first room
    JML FixMusic