#include <vector>
#include <string>
#include <dirent.h>
#include "platform.h"
#include "utils.h"

std::vector<std::string> list_files_in_dir(std::string dirname) {
	std::vector<std::string> out;
	dirent* ent;
	DIR* dir = opendir(dirname.c_str());
	if(dir == nullptr) return out;
	while((ent = readdir(dir)) != nullptr) {
		if(ent->d_type != DT_REG) continue;
		std::string fullpath;
		fullpath += dirname;
		if(!ends_with(dirname, "/")) {
			fullpath += "/";
		}
		fullpath += ent->d_name;
		out.push_back(fullpath);
	}
}
