function createElement(tagName, className, innerValue) {
    var new_input=document.createElement(tagName);
    new_input.setAttribute('class', className);
    new_input.innerHTML=innerValue;
    return new_input;

}

function chimg()
{
    var obj;
    obj=document.getElementById("pictureSmile");
    obj.src="image/smile-3.gif";
} 

function sendMessage() {
    var userName=document.getElementById('user-name');
    var userMessage=document.getElementById('user-message');

    if ((userName.value=='')||(userMessage.value=='')) {
        alert('Enter the name/text messages!');
        return;
    }
    var req = getXmlHttp();
    req.onreadystatechange = function() {
        if (req.readyState == 4){
            clearTimeout(timeout);
            if (req.status == 200) {

                updateList();
            } else {
                handleError(req.statusText);
            }
        }
    }
    req.open('POST','?cntr=chat&action=insert',true);
    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    dataReq = 'chat[user]='+encodeURIComponent(userName.value)+'&chat[message]='+encodeURIComponent(userMessage.value);
    req.send(dataReq);
    var timeout = setTimeout( function(){
        req.abort();
        handleError("Time over")
    }, 10000);
    userMessage.value='';
}    

function handleError(message) {
    alert("Error: "+message);
}

function updateList() {

    var req = getXmlHttp();
    req.open('GET','?cntr=chat&action=getJSON&lastId='+id,true);
    req.onreadystatechange = function() {
        if (req.readyState == 4){
            clearTimeout(timeout);
            if (req.status == 200) {
                //   console.log("response="+req.responseText);
                var responsObject=eval('('+req.responseText+')');
                for (var i=0; i<responsObject.length; i++) {
                    document.getElementById('chat-content').appendChild(createElement('div','chat-user',responsObject[i].user));
                    document.getElementById('chat-content').appendChild(createElement('div','chat-date',responsObject[i].date));
                    document.getElementById('chat-content').appendChild(createElement('div','clear',''));
                    document.getElementById('chat-content').appendChild(createElement('div','chat-message',responsObject[i].message));
                    id=responsObject[i].id;
                }

            } else {
                handleError(req.statusText);
            }
        }
    }

    req.send(null);
    var timeout = setTimeout( function(){
        req.abort();
        handleError("Time over")
    }, 10000);
}

function getXmlHttp(){
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } 
    catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } 
        catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

