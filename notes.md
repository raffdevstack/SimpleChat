### Get contacts from db
#### Main code
1. ~~convert the html markup in api_contact, with a 
loop concatenated markup.~~
2. ~~test the loop with 10 iterations~~
3. use $DB->read($sql, []) 

---
### Display the contacts
#### Pre-requisites
1. ~~Make a dynamic display element for the contacts~~
#### Main Code
1. ~~Make the contacts dynamic place it in the server 
side
   in the includes/api_contacts.php~~ <br>
2. ~~create a redirect from api.php to api_contacts.php~~
3. ~~copy the error message incoding from api_userinfo.php to api_contacts.php~~
4. ~~also copy the successful data retrieval code~~
5. ~~put the markup to a variable, enclose with ' ' 
single quotes~~
6. ~~send that var as a message in $result, the contacts
data type, then die after the statement~~
7. ~~event listener for the label_contacts, call 
get_contacts function~~
8. ~~put parameter 'e' (in case we need that event), 
call get_data from get_contacts~~
9. ~~add the 'case' for contacts data_type in switch~~
10. ~~target the panel to display the contact, and inject
the message there using innerHTML~~
11. ~~same as get_contacts, also create same functions~~
12. ~~also add their event listeners~~
13. ~~add directories for chats and settings in api.php~~
14. ~~make those files, then edit the contents of those~~
15. ~~add in the switch inside the handle_result function~~
#### Debugging
1. ~~make sure to use same empty objects to return in 
api_contacts~~
2. ~~ensure id of elements match~~
