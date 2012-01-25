
<script></script>
<div id="chat-content">
</div>

<div>
    <label for="user-name">Name</label><br />
    <input type="text" id="user-name" name="chat[user]" value="" />
</div>

<div>
    <label for="user-message">Chat</label><br />
    <textarea id="user-message" cols="100" rows="2" name="chat[message]"></textarea>
</div>        

<div>
    <input type="button" name="btn-add-post" value="Send" onclick="sendMessage()"/>
</div>


<script type="text/javascript">
    var id='0';
    window.onload = function() {
        updateList();
        setInterval('updateList()', 4000) ;
    }    
</script>

