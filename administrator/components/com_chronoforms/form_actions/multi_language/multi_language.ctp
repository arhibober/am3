<div class="dragable" id="cfaction_multi_language">Multi Language</div>
<!--start_element_code-->
<div class="element_code" id="cfaction_multi_language_element">
	<label class="action_label" style="display: block; float:none!important;">Multi Language</label>
	<textarea name="chronoaction[{n}][action_multi_language_{n}_content1]" id="action_multi_language_{n}_content1" style="display:none"><?php echo $action_params['content1']; ?></textarea>
    <input type="hidden" name="chronoaction[{n}][action_multi_language_{n}_lang_tag]" id="action_multi_language_{n}_lang_tag" value="<?php echo $action_params['lang_tag']; ?>" />
	
	<input type="hidden" id="chronoaction_id_{n}" name="chronoaction_id[{n}]" value="{n}" />
	<input type="hidden" name="chronoaction[{n}][type]" value="multi_language" />
</div>
<!--end_element_code-->
<div class="element_config" id="cfaction_multi_language_element_config">
	<?php echo $PluginTabsHelper->Header(array('settings' => 'Settings', 'help' => 'Help'), 'multi_language_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('settings'); ?>
		<?php echo $HtmlHelper->input('action_multi_language_{n}_lang_tag_config', array('type' => 'text', 'label' => "Language Tag", 'class' => 'small_input', 'smalldesc' => "The language tag, e.g: en-US OR en-GB OR de-DE")); ?>
		<?php echo $HtmlHelper->input('action_multi_language_{n}_content1_config', array('type' => 'textarea', 'label' => "Translation strings", 'label_over' => true, 'rows' => 20, 'cols' => 70, 'smalldesc' => 'Srings to be translated with their translation in the language set above, multi line format, e.g: non_translated_string=translated_string.')); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('help'); ?>
		<p>
			<ul>
				<li>Enter your language tag.</li>
				<li>Add your desired translation strings in multi line format.</li>
				<li>This action should be placed the last thing in your main form event so that it's able to translate all strings in the form output.</li>
			</ul>
		</p>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>