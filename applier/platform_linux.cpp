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
		out.push_back(ent->d_name);
	}
	return out;
}
