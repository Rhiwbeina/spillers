
SQLdbSPEC.txt dump of SPILLERS database, structure and data but no songs data.

scanlib - 
$_POST['wipedb'] == 1 truncate (empty the songs db)

$_POST['scanroot'] == 1
get list of all music files (.mp3 .m4a as in FILETYPES) - insert a record in db.songs for each file set FULLPATH, also sets title to filename.
The function above will only run if db.settings $_POST['scanroot'] == 1 it takes many minutes to run but the browser does not time out.
The column FULLPATH is unique so re-running this will only add new files (but still takes ages )

$_POST['taguntagged'] == 1
ID3 tags
get a list of files from db.songs where ID3state = unchecked (ie no id3 tag data yet) 
use getID3-1.9.15 to get tags for each file in list, add the tags to songs db UPDATE. 
GOTCHAs 
Tags that are there but empty, no data is added to db (TITLE still NULL)

$_POST['delunusedrecords'] == 1
get a list of all files in db
check if coresponding file exists, if not then remove record from db

There is the usual AUTH.PHP at the top of this script followed by session_write_close();
this stops further calls to server from hanging.
Whilst the script is running it pushes realtime info to the browser.
Info is also saved to status.sts and status.log
status.sts shows the current running state and ends up with the word Finished
This value 'Finished' in status.sts is used to check if it is safe to launch scanlib.
If scanlib.php was aborted before finishing scanlib will not restart until status.sts is older than 20 seconds.

Dave Ladd December 2017