@asar
math pri on ; why the fuck isn't this the default

!FinalLevel = $000A;Set this to the level that triggers the credits scene. This level will repeat forever. Defaults to Front Door.
;To disable (if you, for example, want to leave some extra content after beating the final boss), set it to 0000.

!Freeram = $1F12;this patch creates a lot of freerom and freeram, and uses a bit of it.
;the used rom isn't configurable, but you can move this if you want. by default, it's luigi's current submap.
;this patch isn't compatible with two player games, nor are submaps used anymore, so this is freeram and can be used as such.

!FinalLevel13BF = !FinalLevel/$100*$24+!FinalLevel&$FF;this might look ugly, but it works

;sram map (unlike the original SMW, the three files are interlaced):
;$700000: "file exists" flag - 69 is true, anything else is false
;$700003: level number, copied to $13BF
;$700006: levels beaten, shown on intro screen. it is safe to create multiple paths with custom blocks to store to $13BF
;$700009, $70000C, $70000F, $700012: switch palace flags
;$700015 (15 bytes): death counter bytes (interlaced)

incsrc "sa1detect.asm"
if !sa1 == 1
    !Freeram := !Freeram|!addr
endif

org $009BC9;save game routine - never used though
db $00

; actually, lets just use freespace like normal people
freecode
Mymainc:
if !FinalLevel13BF-1
    LDA $13BF|!addr
    CMP.b #!FinalLevel13BF
    BEQ .Return
endif

INC !Freeram
PHX
LDX $010A|!addr
INC $13BF|!addr
LDA $13BF|!addr
STA !sram+3,x
LDA !sram+6,x
INC A
STA !sram+6,x

macro svswitch(id)
    LDA.w ($1F27|!addr)+<id>
    STA.l <id>*3+!sram+9,x
endmacro
%svswitch(0)
%svswitch(1)
%svswitch(2)
%svswitch(3)

; randomdude999: save the death counter manually
macro save_death(b)
    LDA.l !deathcounter+<b>
    STA.l !sram+$15+3*<b>,x
endmacro
%save_death(0)
%save_death(1)
%save_death(2)
%save_death(3)
%save_death(4)
; end my stuff


PLX
.Return
RTL
; warnpc $009C13
load_deathcounter:
macro ldswitch(id)
    LDA.l <id>*3+!sram+9,x
    STA.w ($1FCE|!addr)+<id>;I'd use $1F27, but for some reason that's overwritten, so I'll send it to the place where it's saved to.
endmacro
%ldswitch(0)
%ldswitch(1)
%ldswitch(2)
%ldswitch(3)
; randomdude999: load the death counter manually
macro load_death(b)
    LDA.l !sram+$15+3*<b>,x
    STA.l !deathcounter+<b>
endmacro
%load_death(0)
%load_death(1)
%load_death(2)
%load_death(3)
%load_death(4)
RTL

InitSRAM:
macro init_death(b)
    STA !sram+$15+3*<b>,x
    STA !deathcounter+<b>
endmacro
LDA #$00
%init_death(0)
%init_death(1)
%init_death(2)
%init_death(3)
%init_death(4)
LDA #$69
STA !sram,x
LDA #$01
STA !sram+3,x
STZ $13BF|!addr
DEC A
STA !sram+6,x
LDA $0109|!addr
RTL
; end my stuff


org $009DB5;check if file is clear routine. Z=0 = file=OK. also loads X with the pointer to the data, input value of X is the file ID.
LDA !sram,x
CMP #$69
RTS

Mymain3:
LDX #$08
LDA $13BF|!addr
DEC A
RTL

Mymain:
STA $0DD5|!addr
autoclean JSL Mymainc
LDA $13C6|!addr
JML $00CA04|!bank

Mymain4:
LDY #$0B
LDA $0DBE|!addr
BPL +
LDY #$02
+
JML $009C8B|!bank

FixMusic:
LDA #$3F
STA $0DDA|!addr
STZ $2143
PLP
JML $0080E7|!bank

warnpc $009DFA;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

org $009D66
LDA !sram+6,x;levels beaten

org $05D9C6
BRA $01
org $05D9D4;fix midways
LDA $13CE|!addr
BRA $03

org $009CF7;load game routine
STZ $0109|!addr
LDA !sram+3,x
STA $13BF|!addr

JSL load_deathcounter
JMP.w $009D22

warnpc $009D22;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

org $00A099
BEQ $00;make the OW never appear, load the level directly

org $00C9FE         
JML Mymain;level beaten (both goal tape and keyhole)

org $00CA06
JSL Mymain3
NOP

org $0CCFF1
JSL Mymain2;castle destruction sequence
RTS

org $05B150;switch palace
STZ $13D2|!addr
JML Mymain2

org $009B48
TYX
LDA #$00
STA !sram,x;erase file routine
JMP.w $009B67

org $009E65;go to title screen, not overworld, on game over
JML Mymain4

org $0080E3;This fixes a bug where the music stops when Mario dies (and probably other conditions too).
JML FixMusic;The game thinks the music is still playing since the currently playing music address ($0DDA) is equal to what it should be,
;but it recently uploaded the level music bank so nothing is playing. This resets that address and forces it to replay the music.

org $05D83E
LoadLevel:
LDA !Freeram
BEQ +
STZ !Freeram
STZ $13CE|!addr;this fixes some various stuff, like midways carrying over between levels
STZ $13C6|!addr;and Mario stopping moving during the victory marsh after a boss
STZ $13D2|!addr
+
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
JSL InitSRAM
LDA #$01
STA $13BF|!addr
+
JMP.w LoadEnd

.Init
JSL InitSRAM
JMP.w LoadEnd3

;using this as freespace again
Mymain2:
LDA #$0B
STA $0100|!addr
JML Mymainc

warnpc $05D89F;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
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


org $05D8AE
BRA $07;nuke something with checking the current submap. useless