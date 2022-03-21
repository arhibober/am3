<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
?>
<style type="text/css">
	span.cf_debug {
		background:#8BC0FF url(<?php echo JURI::Base().'components/com_chronoforms/css/'; ?>images/debug.png) no-repeat scroll 10px 50%;
		border:1px solid #fff;
		color:#000;
		display:block;
		margin:15px 0pt;
		padding:8px 10px 8px 36px;
	}
</style>
<?php
	echo '<span class="cf_debug"><ol>'.$MyForm->formdebug.'</ol></span>';
?>