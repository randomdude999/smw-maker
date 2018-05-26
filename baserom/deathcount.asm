
incsrc "sa1detect.asm"


org $00D0D8 ;\ Infinite lives
    NOP #3  ;/

org $008F3B
    autoclean JSL SBDisplay
    BRA +
org $008F5B
    +

org $00F614
    JSL DeathCounter
 
org $008C89     ; Status bar text
    db $0D,$38  ; D
    db $0E,$38  ; E
    db $0A,$38  ; A
    db $1D,$38  ; T
    db $11,$38  ; H
    db $1C,$38  ; S

freecode
SBDisplay:
print "SBDisplay ",pc
    LDX #$04
  - LDA !deathcounter,x
    STA $0F15|!addr,x
    DEX
    BPL -

  - INX
    LDA !deathcounter,x
    BNE .ret
    CPX #$04
    BEQ .ret
    LDA #$FC
    STA $0F15|!addr,x
    BRA -

  .ret
    RTL



DeathCounter:
    PHX
    LDX #$04
  - LDA !deathcounter,x
    INC
    STA !deathcounter,x
    CMP #$0A
    BCC .ret
    LDA #$00
    STA !deathcounter,x
    DEX
    BPL -

  .ret
    LDA #$09
    STA $71
    PLX
    RTL
 