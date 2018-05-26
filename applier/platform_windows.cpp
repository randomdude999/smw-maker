#include <vector>
#include <string>
#include <windows.h>
#include "platform.h"

std::vector<std::string> list_files_in_dir(std::string dir) {
	std::vector<std::string> out;
	dir += "\\*";

	WIN32_FIND_DATA find_data;
	HANDLE findHandle = FindFirstFile(dir.c_str(), &find_data);
	if(findHandle == INVALID_HANDLE_VALUE) {
		return out; // return empty vector, specifying "no files found"
	}
	do {
		out.push_back(find_data.cFileName);
	} while(FindNextFile(findHandle, &find_data));

	FindClose(findHandle);
	return out;
}