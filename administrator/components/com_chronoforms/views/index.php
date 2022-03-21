<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
	$mainframe = JFactory::getApplication();
	$uri = JFactory::getURI();
	preg_match('/http(s)*:\/\/(.*?)\//i', $uri->root(), $matches);
	$domain = $matches[2];
?>
<?php if($params === false): ?>
<style type="text/css">
	span.cf_alert {
		background:#FFD5D5 url(<?php echo $uri->root().'components/com_chronoforms/css/images/'; ?>alert.png) no-repeat scroll 10px 50%;
		border:1px solid #FFACAD;
		color:#CF3738;
		display:block;
		margin:15px 0pt;
		padding:8px 10px 8px 36px;
	}
</style>
<span class="cf_alert">Your Chronoforms install at <?php echo $domain; ?> is <strong>NOT Validated</strong>, No limited features but for a small fee you get link free forms and help us continue the development and support</span>
<?php else: ?>
<style type="text/css">
	span.cf_alert {
		background:#B5F64D;
		border:1px solid #00CC00;
		color:#009900;
		display:block;
		margin:15px 0pt;
		padding:8px 10px 8px 36px;
	}
</style>
<span class="cf_alert">Your Chronoforms install at <?php echo $domain; ?> is <strong>Validated</strong></span>
<?php endif; ?>
<form action="index.php?option=com_chronoforms" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
<thead>
	<th width="1%" class='title'>#</th>
	<th width="5%" class='title' style="text-align: left;">Form ID</th>
	<th width="1%" class='title' style="text-align: left;"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($forms); ?>);" /></th>
	<th width="10%" align="left" class='title' style="text-align: left;">Name</th>
	<th width="10%" align="left" class='title' style="text-align: left;">Wizard</th>
	<th width="20%" align="left" class='title' style="text-align: left;">Link</th>
	<th width="38%" align="left" class='title' style="text-align: left;">Tables Connected</th>
	<th width="2%" align="left" class='title' style="text-align: left;">Published</th>
</thead>
<?php if(!empty($forms)): ?>
	<?php $i = 0; ?>
	<?php foreach($forms as $form): ?>
		<tr>
			<td width="1%" class='title'><?php echo $i + 1;?></td>
			<td width="5%" class='title'><?php echo $form->id; ?></td>
			<td width="1%" class='title'>
				<input type="checkbox" id="cb<?php echo $i;?>" name="cb[]" value="<?php echo $form->id; ?>" onclick="isChecked(this.checked);" />
			</td>
			<td width="10%" align="left" class='title'><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')"><?php echo $form->name; ?></a></td>
			<td width="10%" align="left" class='title'><a href="<?php echo JURI::Base().'index.php?option=com_chronoforms&task=form_wizard&form_id='.$form->id; ?>">Wizard edit</a></td>
			<td width="20%" align="left" class='title'><a href="<?php echo $uri->root().'index.php?option=com_chronoforms&chronoform='.$form->name; ?>" target="_blank">Frontend view</a></td>
			<td width="38%" align="left" class='title'>
				<?php
					$tables = array();
					if(!empty($form->form_actions)){
						foreach($form->form_actions as $action){
							if($action->type == 'db_save'){
								$action_params = new JParameter($action->params);
								$table_name = $action_params->get('table_name', '');
								if(!empty($table_name)){
									$tables[] = $table_name;
								}
							}
						}
					}
				?>
				<?php if(!empty($tables)): ?>
					<select name="table_name[<?php echo $form->id; ?>]">
					<option value=""> - </option>
					<?php foreach($tables as $table): ?>
						<option value="<?php echo $table; ?>"><?php echo $table; ?></option>
					<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</td>
			<td width="2%" align="left" class='title'>
			<?php $image_path = $uri->base().'templates/'.$mainframe->getTemplate().'/images/admin/'; ?>
			<?php if((int)$form->published == 1): ?>
				<a href="#unpublish" onclick="return listItemTask('cb<?php echo $i;?>','unpublish')"><img border="0" alt="Published" src="<?php echo $image_path; ?>tick.png"/></a>
			<?php else: ?>
				<a href="#publish" onclick="return listItemTask('cb<?php echo $i;?>','publish')"><img border="0" alt="Unpublished" src="<?php echo $image_path; ?>publish_x.png"/></a>
			<?php endif; ?>
			</td>
		</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
<?php endif; ?>
<tr><td colspan="8" style="white-space:nowrap;" height="20px"><?php echo $pageNav->getListFooter(); ?></td></tr>
</table>
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_chronoforms" />
</form>