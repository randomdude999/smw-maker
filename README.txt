Building everything:
1. Go to applier/ and build it (on Linux, use make, on Windows, do whatever, probably mingw).
2. Go to baserom/ and build that. There's a README there with instructions.

Setting up webserver:
1. Get MySQL and PHP.
2. Build everything.
3. In your webserver's root directory, create a symlink to the webserver folder here.
4. Make the levels/ folder writable by www-data.
5. In MySQL, create a database named smwmaker, and run db_schema.sql in there. If you set a password for it, don't forget to update webserver/common_includes.php's db_connect function, and bot/main.py.
