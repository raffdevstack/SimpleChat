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
8. ~~put parameter 'e' (in case we need that event), call get_data from get_contacts~~
9. add the 'case' for contacts data_type in switch
10. target the panel to display the contact, and inject the message there using innerHTML
#### Debugging
1. make sure to use same empty objects to return in api_contacts