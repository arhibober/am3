<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
	$mainframe = JFactory::getApplication();
	$primary = '';
	foreach($table_fields as $table_field => $field_data){
		if($field_data->Key == 'PRI'){
			$primary = $table_field;
		}
	}
?>
<form action="index.php?option=com_chronoforms" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
<thead>
	<th width="1%" class='title'>#</th>
	<?php if(!empty($primary)): ?>
	<th width="2%" class='title' style="text-align: left;">ID</th>	
	<th width="1%" class='title' style="text-align: left;"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($table_data); ?>);" /></th>
	<?php endif; ?>
	<th width="95%" align="left" class='title' style="text-align: left;">Record</th>
</thead>
<?php if(!empty($table_data)): ?>
	<?php $i = 0; ?>
	<?php foreach($table_data as $row): ?>
		<tr>
			<td width="1%" class='title'><?php echo $i + 1;?></td>
			<?php if(!empty($primary)): ?>
			<td width="2%" class='title'><?php echo $row->$primary; ?></td>			
			<td width="1%" class='title'>
				<input type="checkbox" id="cb<?php echo $i;?>" name="cb[]" value="<?php echo $row->$primary; ?>" onclick="isChecked(this.checked);" />
			</td>
			<?php endif; ?>
			<td width="95%" align="left" class='title'><a href="#show_data" onclick="return listItemTask('cb<?php echo $i;?>','show_data')">Record #<?php echo $i + 1 + $pageNav->limitstart; ?></a></td>
		</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
<?php endif; ?>
<tr><td colspan="4" style="white-space:nowrap;" height="20px"><?php echo $pageNav->getListFooter(); ?></td></tr>
</table>
<input type="hidden" name="table_name" value="<?php echo $table_name; ?>" />
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="task" value="list_data" />
<input type="hidden" name="option" value="com_chronoforms" />
</form>