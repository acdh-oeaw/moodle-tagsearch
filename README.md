TagSearch module for moodle 3.1
Created By Norbert Czirjak

This module is extending the searching areas for the apache solr in moodle 3.1



Install steps:

1. You need to copy the tagsearch directory to the moodle/mod directory.
2. Go and log in to your admin
3. Site administration/Notification and then install the new module
4. if the installation was okay, then go to the Site administration/Plugins/Search/Manage global search
5. From Available areas for search , select the searches what you want. Please DO NOT activate the: Tagsearch - activity - do not turn on. Then press the save changes button.
6. Now you need to index your data
7. The same page click on the 4. Index Data text
8. At the bottom of the page you can find the Indexing part, here check on the Index all site contents
9. If the indexing is okay then you need to see date and time next to the selected indexing parts


Frontend:

The global search now have new searching areas, what you already allowed in the backend.



Add a new searching option:

If you want to create a new searching option, then you need to do the following steps:

go to /mod/tagsearch/classes/search/

here you need to add a new php file, you can copy the tags.php and rename it. In the file you need to change the following lines:

- The class name
- The function get_recordset_by_timestamp.  Here you need to add your own SQL.
- The function get_document. Here you need to create the searching fields based on the solr schema.
- The get_doc_url and the get_context_url are the displayed links on the search result page, here you can add the course id and the tag id, so if the user clicking on the title then he/she will reach the  searched course page.


