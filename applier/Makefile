LDLIBS += -lstdc++ -ldl
CFLAGS += -O3
CXXFLAGS += -O3

MWLApplier: gen_rom.o main.o mwllib.o platform_linux.o utils.o asardll.o
	$(CC) $(LDFLAGS) -o $@ $^ $(LOADLIBES) $(LDLIBS)

all: MWLApplier

clean:
	rm *.o
	rm MWLApplier

