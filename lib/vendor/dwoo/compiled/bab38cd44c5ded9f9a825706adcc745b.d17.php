<?php
/* template head */
/* end template head */ ob_start(); /* template body */ ?>Hello, <?php echo $this->scope["name"];
 /* end template body */
return $this->buffer . ob_get_clean();
?>