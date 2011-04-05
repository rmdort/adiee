<?php

// We also have to reference the EE super object in our view file.
// Any outbound link from the CP needs to be masked so that the CP URL
// doesn't show up as a referral link.
// We use the cp->masked_url() function to create this masked link.

// See http://expressionengine.com/public_beta/docs/development/usage/cp.html#masked_url

$this->EE =& get_instance();

?>

<ul class="top">
   <li>Website: <a href="<?=$this->EE->cp->masked_url('http://artminister.com');?>">Artminister</a></li>
   <li>Email: <a href="mailto:hi@artminister.com">hi@artminister.com</a></li>
</ul>
