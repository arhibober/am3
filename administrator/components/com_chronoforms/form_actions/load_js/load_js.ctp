<div class="dragable" id="cfaction_load_js">Load JS</div>
<!--start_element_code-->
<div class="element_code" id="cfaction_load_js_element">
	<label class="action_label" style="display: block; float:none!important;">Load JS</label>
	<textarea name="chronoaction[{n}][action_load_js_{n}_content1]" id="action_load_js_{n}_content1" style="display:none"><?php echo $action_params['content1']; ?></textarea>
    
	<input type="hidden" id="chronoaction_id_{n}" name="chronoaction_id[{n}]" value="{n}" />
	<input type="hidden" name="chronoaction[{n}][type]" value="load_js" />
</div>
<!--end_element_code-->
<div class="element_config" id="cfaction_load_js_element_config">
	<?php echo $PluginTabsHelper->Header(array('settings' => 'Settings', 'help' => 'Help'), 'load_js_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('settings'); ?>
		<?php echo $HtmlHelper->input('action_load_js_{n}_content1_config', array('type' => 'textarea', 'label' => "Code", 'rows' => 20, 'cols' => 70, 'smalldesc' => 'JavaScript code withOUT script tags.')); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('help'); ?>
		<p>
			<ul>
				<li>JavaScript code withOUT script tags.</li>
				<li>You may use PHP code with php tags.</li>
			</ul>
		</p>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>