#include <ctime>
#include <cstdlib>
#include <cstdio>
#include "gen_rom.h"
#include "asardll.h"
#include "utils.h"
#include "platform.h"
#ifdef _WIN32
#include <fcntl.h>
#include <io.h>
#define fileno _fileno
#define isatty _isatty
#else
#include <unistd.h>
#endif

// thanks to:
// Vitor Vilela and Alcaro for misc coding help
// Baserom patches:
// Vitor Vilela (SA-1)
// kaizoman/Thomas (death counter)
// Noobish Noobsicle (one file, one player)
// p4plus2 (1 or 2 players only)
// Alcaro (no overworld)
// Also Alcaro for writing Flips and Asar

int main(int argc, char** argv) {
	clock_t start_time = clock();
	std::string rom;
	srand((unsigned int)time(nullptr));
	if(!asar_init()) {
		fprintf(stderr, "Could not load Asar DLL");
		return 1;
	}
	if (argc == 1) {
		log("Generating 10lvl rom");
		try {
			rom = generate_10lvl_rom();
		} catch(std::string err) {
			fprintf(stderr, "%s", err.c_str());
			return 1;
		}
	}
	else if (argc == 2) {
		log("Generating 1lvl rom %s", argv[1]);
		try {
			rom = generate_1lvl_rom(argv[1]);
		} catch(std::string err) {
			fprintf(stderr, "%s", err.c_str());
			return 1;
		}
	}
	else {
		fprintf(stderr, "Error: invalid number of arguments. usage: %s [lvlid]\n", argv[0]);
		return 1;
	}
	log("Total: took %f seconds.", timeDiff(start_time, clock()));
	if(isatty(fileno(stdout))) {
		fprintf(stderr, "Won't write binary garbage to terminal.");
	} else {
#ifdef _WIN32
		// fucking windows... this should make stdout be binary not text
		_setmode(fileno(stdout), O_BINARY);
#endif
		fwrite(rom.data(), 1, rom.size(), stdout);
	}
	return 0;
}
