@asar

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Single Level by randomdude999              ;
; I used parts of patches whose authors want ;
; credit so if you use this, give credit to  ;
; Alcaro and Noobish Noobsicle               ;
; (and possibly me too)                      ;
; also, uses no freespace or freeram         ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; FLAGS

!disableIntro = 1       ; Skip the intro level?
!infiniteLives = 1      ; Give player infinite lives?
!deathCounter = 1       ; add a death counter? (if you use this, set infiniteLives to 1 aswell!)
!disableFileSelect = 1  ; disable the file select menu? (it's pretty useless when there is no saving)

; SA-1 / Super FX detection
{
    !dp = $0000
    !addr = $0000
    !sa1 = 0
    !gsu = 0
    if read1($00FFD6) == $15
        sfxrom
        !dp = $6000
        !addr = !dp
        !gsu = 1
    elseif read1($00FFD5) == $23
        sa1rom
        !dp = $3000
        !addr = $6000
        !sa1 = 1
    endif
}

;;;;;;;;;;;
; My code ;
;;;;;;;;;;;
{
    ; Disables intro
    if !disableIntro
        org $009CB1
            db $00
    endif

    ; infinite lives
    if !infiniteLives
        org $00D0D8
            NOP #3
    endif

    ; called on level end: trigger credits
    org $00CA01
        LDX #$08         ;\ show cutscene 8 (credits)
        STX $13C6|!addr  ;/
        LDY #$18         ;\ switch to gamemode cutscene
        STY $0100|!addr  ;/
        INC $13D9|!addr  ; what is this
        LDA #$01         ;\ this is the fade direction. the ram map says it's the
        STA $0DAF|!addr  ;/ mosaic direction, but it applies to other fading too
        RTS
}


;;;;;;;;;;;;;;;;;;;;;;;
; "No Overworld" code ;
;;;;;;;;;;;;;;;;;;;;;;;
; Credit goes to Alcaro
; Note that I didn't include the whole patch here, just the parts i needed
{

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
}


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Death Counter from Super Dram World 2 ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; I've disabled the overworld part
if !deathCounter

    ; This ram was originally used for tracking the overworld position of Mario and Luigi,
    ; but since the overworld is disabled, it is no longer used and can be used as freeram.
    !counter = $0DC7|!addr

    org $008F3B
        JSL SBDisplay
        BRA +
    org $008F5B
        +

    org $00F614
        JSL DeathCounter

    org $008C89		; Status bar text
        db $0D,$38	; D
        db $0E,$38	; E
        db $0A,$38	; A
        db $1D,$38	; T
        db $11,$38	; H
        db $1C,$38	; S

        
    
    ; A overworld related routine that is no longer used, thus freespace
    org $04E453
    RTS     ; just in case

    SBDisplay:
        LDX #$04
      -	LDA !counter,x
        STA $0F15|!addr,x
        DEX
        BPL -
        
      - INX
        LDA !counter,x
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
      -	LDA !counter,x
        INC
        STA !counter,x
        CMP #$0A
        BCC .ret
        LDA #$00
        STA !counter,x
        DEX
        BPL -

      .ret
        LDA #$09
        STA $71
        PLX
        RTL

endif

;;;;;;;;;;;;;;;;;;;;;;;;
; One file, one player ;
;;;;;;;;;;;;;;;;;;;;;;;;
; Disables file select menu
; Credit to Noobish Noobsicle
; Apparently you really only need ~3 lines of code to do this...
; (assuming you don't need saving, of course)
; again, this isn't the whole patch
if !disableFileSelect
    org $009CBB
        RTS
    org $009D38
        LDX #$00         ;\one player game
        STX $0DB2|!addr  ;/
        JMP $9E10
endif