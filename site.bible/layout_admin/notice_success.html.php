                <!-- Notification boxes -->
                <span id="__message__" class="notification n-success"><?php echo $message; ?></span>
                <script language="Javascript">
                setTimeout("_hide_message();", 70000);
                function _hide_message(){ $('#__message__').get(0).style.display='none'; }
                </script>
