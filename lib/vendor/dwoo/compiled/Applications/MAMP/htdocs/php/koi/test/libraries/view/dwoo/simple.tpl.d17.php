<?php
/* template head */
/* end template head */ ob_start(); /* template body */ ?>Hello, world!<?php  /* end template body */
return $this->buffer . ob_get_clean();
?>