<?php
/**
 * @package XpertScroller
 * @version 2.2
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
 
// no direct access
defined('_JEXEC') or die('Restricted accessd');


// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
//set moduleid
$moduleId = ($params->get('auto_module_id',0)==1) ? 'xs_'.$module->id : $params->get('module_unique_id');

$items = modXpertScrollerHelper::getList($params);
modXpertScrollerHelper::load_script($params,$module);
modXpertScrollerHelper::load_style($params,$module);

//compare total items with max amount to show. assign the lower value to maxItems variable.
$totalItems = count($items);
if( (int)$params->get('count') <= $totalItems ) $maxItems = (int)$params->get('count');
else $maxItems = $totalItems;

//number of pan.
$totalPane = $maxItems / (int)$params->get('col_amount');

//get this image position name and ues it as class
$imagePosition = $params->get('image_position');

//loop through full content object and prepare it according article settings
foreach($items as $item){
    //check image link
    if ( $params->get('image_link') == '1' ){
        //check browser navigation
        $item->image = ($params->get('browser_nav') === 'parent') ? "<a href=\"$item->link\"><img class=\"$imagePosition\" src=\"$item->image\" alt=\"$item->title\"></a>" : "<a target=\"_blank\" href=\"$item->link\"><img class=\"$imagePosition\" src=\"$item->image\" alt=\"$item->title\"></a>";
    }
    else  $item->image = "<img class=\"$imagePosition\" src=\"$item->image\" alt=\"$item->title\">";
    //check title publish
    if ( $params->get('article_title') =='1' ){
        //if title publish then check is it linkable or not if linkable then check browser nav status.
        if ( $params->get('title_link') == '1' )
            $item->title = ($params->get('browser_nav') == 'parent') ? '<a href="'.$item->link.'">'.$item->title.'</a>' : '<a target="_blank" href="'.$item->link.'">'.$item->title.'</a>' ;
    }
}

require JModuleHelper::getLayoutPath('mod_xpertscroller', $params->get('layout', 'default'));
