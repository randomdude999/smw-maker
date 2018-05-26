#include <ctime>
#include <cstdlib>
#include <cstdio>
#include <io.h>
#include "gen_rom.h"
#include "asardll.h"
#include "utils.h"
#include "platform.h"
#ifdef _WIN32
#include <fcntl.h>
#endif

// thanks to Vitor Vilela and Alcaro

int main(int argc, char** argv) {
	std::string rom;
	srand((unsigned int)time(nullptr));
	asar_init();
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
		fprintf(stderr, "Error: invalid number of arguments. usage: %s [lvlid]", argv[0]);
		return 1;
	}
	if(_isatty(_fileno(stdout))) {
		fprintf(stderr, "Won't write binary garbage to terminal.");
	} else {
#ifdef _WIN32
		// fucking windows... this should make stdout be binary not text
		_setmode(_fileno(stdout), O_BINARY);
#endif
		fwrite(rom.data(), 1, rom.size(), stdout);
	}
	return 0;
}