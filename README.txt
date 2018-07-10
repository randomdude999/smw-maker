Building everything:
1. Go to applier/ and build it (on Linux, use make, on Windows, do whatever, probably mingw).
2. Copy flips here (and make it executable), and also libasar.so (or asar.dll) (don't put it in the applier/ folder, leave it here!).
3. Go to baserom/ and build that. There's a README there with instructions.

Setting up webserver:
1. Get MySQL and PHP.
2. Build everything.
3. In your webserver's root directory, create a symlink to the webserver folder here.
4. Make the levels/ folder writable by www-data.
5. In MySQL, create a database named smwmaker, and run db_schema.sql in there. Also, create the file webserver/instance_specific.php which defines the constants mysql_username and mysql_password. Also update bot/main.py with the MySQL username/password.
6. Make bot/run.sh run at system startup (via cron, systemd or whatever you like)
