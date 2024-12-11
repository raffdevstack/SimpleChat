### Bugs report
1. also deleted thread on the other side.
2. persistent new_message alert.

---

### Delete Thread
#### Steps
1. ~~button "delete thread"~~
2. ~~repurpose delete_message function on index.php~~
3. ~~handle with file in api~~
4. ~~copy-paste delete_message file~~
5. ~~make new function, repurpose chatFinder~~

---
### Delete Message

#### Steps
1. ~~create a button, absolute position~~
2. ~~create an onclick~~
   * ~~pass the message id~~ 
3. ~~define in onclick function index~~
   * ~~add a confirmation~~
   * ~~use get_data with delete_message data type~~
   * ~~refresh the page with get_data->chat_refresh~~
4. ~~define in api_chats~~
5. ~~create a file~~
   * ~~get the message id~~
   * ~~get the message from message_id~~
   * ~~determine if you are the sender/receiver~~
   * ~~update messages and set deleted___ column to 1~~
   * ~~add limit 1 to the query above~~
6. ~~update chats query to not include deleted by whom~~
#### Other issue
~~1. optimize update to received in chat contacts refresh~~ 


---

### ~~Chat section~~
#### ~~Function~~
1. ~~Send message to db~~
#### Steps
1. ~~copy api chats~~ 
2. ~~default values on db~~ 
3. send to db 
4. ~~add convo_id, varchar 60, add index~~ 
5. ~~if a convo exist, use it, if not, generate a new one~~ 
6. ~~make function random string id generator~~

---
### Chat section
#### Functions
1. ~~Display userid on chats~~
2. ~~Get the use from userid and display name on chats~~
#### Debugging
1. 
#### Steps
1. ~~Don't include yourself from the db result, modify the query~~
2. ~~give hover to each image, make it bigger~~
3. ~~activate the chat page (through the radio), create a function and call onclick, on the function call get_chats~~
4. ~~on the contact container, add a property 'info' and put the userid of the that user~~
5. ~~get the userid id from the function in index.php, assign that to a global var at the top of the script~~
6. ~~use get_data() and pass the user as an object, pass the userid~~
7. ~~handle in api_chats, get the user_id~~

---
### ~~Save settings~~
#### New way (my way - applied debugging and testing every phase)
1. ~~collect settings data~~
2. ~~send it to server~~
3. ~~handle result~~
4. ~~send new settings to database~~
5. ~~handle error in index~~
#### Main code
1. ~~reuse code js from signup.php and use it in 
index.php for sending data to db~~
2. edit the js to adapt to index.php
3. redirect in api.php
4. create file api_save_settings.php in includes based
on api_signup
5. edit api_save_settings
6. put onclick on the save settings because it is in 
the server, don't forget to pass an event
7. put save_settings in the handle result, just alert
the message
8. put redirect to save_settings in api.php
9. copy api_signup.php and paste as api_save_settings
10. edit api_save_settings
11. change query
12. sanity check in api_save_settings, 
echo json_encode($DATA_OBJ);
13. refresh the data, call get_data after the alert
call get_settings(true) also, true because we 
don't have an event
14. kaya na ni i debug oi hahahhha

---

### ~~Display settings page~~
#### Main code
1. ~~get markup from signup page and copy to 
api_settings~~
2. ~~use db read up to and supply the query and 
['userid'=>id], 
where id is the userid from session~~
3. ~~check if data (result from db, call it result) 
is_array,~~
4. ~~inside the if, put the first result in the array
to a var that you will access to get the indiv data~~
5. ~~suppy the query, and id~~
6. ~~suppy the html inside the if~~
7. ~~supply the data to the markup~~

#### Debugging
1. ~~query = "select * from users where userid = 
:userid limit 1"~~

---
### ~~Get contacts from db~~
#### Main code
1. ~~convert the html markup in api_contact, with a 
loop concatenated markup.~~
2. ~~test the loop with 10 iterations~~
3. ~~use $DB->read($sql, []), put it in users var~~
4. ~~put the for loop inside an if, if 
(is_array($users));~~
5. ~~use foreach to loop through all users~~
6. ~~provide query, "select * from users limit 10~~"
7. ~~supply actual data to each iteration, double quote
on the outside and single on the inside~~

#### Debugging
1. 

---
### ~~Display the contacts~~
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
