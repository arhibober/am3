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
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');

abstract class modXpertScrollerHelper{
    
    public $moduleId = 0; //hold module unique id
    public $content = ''; //hold all content
    
    public static function getList(&$params){
        // Get the dbo
        $db = JFactory::getDbo();

        // Get an instance of the generic articles model
        $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('count', 5));
        $model->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        $model->setState('filter.category_id', $params->get('catid', array()));

        // User filter
        $userId = JFactory::getUser()->get('id');
        switch ($params->get('user_id'))
        {
                case 'by_me':
                        $model->setState('filter.author_id', (int) $userId);
                        break;
                case 'not_me':
                        $model->setState('filter.author_id', $userId);
                        $model->setState('filter.author_id.include', false);
                        break;

                case '0':
                        break;

                default:
                        $model->setState('filter.author_id', (int) $params->get('user_id'));
                        break;
        }
        // Filter by language
        $model->setState('filter.language',$app->getLanguageFilter());

        //  Featured switch
        switch ($params->get('show_featured'))
        {
                case '1':
                        $model->setState('filter.featured', 'only');
                        break;
                case '0':
                        $model->setState('filter.featured', 'hide');
                        break;
                default:
                        $model->setState('filter.featured', 'show');
                        break;
        }

        // Set ordering
        $order_map = array(
                'm_dsc' => 'a.modified DESC, a.created',
                'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
                'c_dsc' => 'a.created',
                'p_dsc' => 'a.publish_up',
        );
        $ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
        $dir = 'DESC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);

        $items = $model->getItems();
        foreach ($items as &$item) {
            $item->slug = $item->id.':'.$item->alias;
            $item->catslug = $item->catid.':'.$item->category_alias;

            if ($access || in_array($item->access, $authorised))
            {
                    // We know that user has the privilege to view the article
                    $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            }
            else {
                    $item->link = JRoute::_('index.php?option=com_user&view=login');
            }

            $item->introtext = JHtml::_('content.prepare', $item->introtext);
            $item->image = self::get_image($item->introtext);

            $item->introtext = self::prepare_text($item->introtext,$params->get('intro_text_limit'));
        }
        return $items;
    }

    function prepare_text ($text, $num_charecter){
        $text = strip_tags($text,'<p><a>');

        if(strlen($text)>$num_charecter && $num_charecter!= '0'){
            $text1 = substr ($text, 0, $num_charecter) . "....";
            return $text1;
        }
        else return $text;
    }
    public static function get_image($text) {

        preg_match("/\<img.+?src=\"(.+?)\".+?\/>/", $text, $matches);

        $image = NULL;

        $paths = array();

        if (isset($matches[1])) {
            $image_path = $matches[1];

            //joomla 1.6 only
            $full_url = JURI::root(true);

            //remove any protocol/site info from the image path
//            $parsed_url = parse_url($full_url);
//            $paths[] = $full_url;
//            if (isset($parsed_url['path']) && $parsed_url['path'] != "/") $paths[] = $parsed_url['path'];
//
//            foreach ($paths as $path) {
//                if (strpos($image_path,$path) !== false) {
//                    $image_path = substr($image_path,strpos($image_path, $path)+strlen($path));
//                }
//            }
//
//            // remove any / that begins the path
//            if (substr($image_path, 0 , 1) == '/') $image_path = substr($image_path, 1);
//
//            //if after removing the uri, still has protocol then the image
//            //is remote and we don't support thumbs for external images
//            if (strpos($image_path,'http://') !== false ||
//                strpos($image_path,'https://') !== false) {
//                return false;
//            }

            $image = JURI::Root(True)."/".$image_path;
        }
        return $image;
    }
        
    
    /*
    * Load add script settings 
    * and push it to document head
    */
   public function load_script(&$params,$module){
        $doc =& JFactory::getDocument();
        //set moduleid
        $moduleId = ($params->get('auto_module_id',0)==1) ? 'xs_'.$module->id : $params->get('module_unique_id');

        //Load jQuery
        modXpertScrollerHelper::load_jquery($params);
        //modXpertScrollerHelper::load_scroller();
        
        $animationMode = ($params->get('animation_style') == 'animation_h') ? 'false' : 'true';
        $speed = (int)$params->get('animation_speed');
        $repeat = ( (int)$params->get('repeat') ) ? 'true' : 'false';
        $keyboardNav = ( (int)$params->get('keyboard_navigation') ) ? 'true' : 'false';
        
        //auto scroll plugin config
        $autoScroll = '';
        if ( (int)$params->get('auto_play') ){
            $autoPlay   = ( (int)$params->get('auto_play') ) ? 'true' : 'false';
            $interval   = (int)$params->get('interval');
            $autoPause  = ( (int)$params->get('auto_pause') ) ? 'true' : 'false';
            
            $autoScroll = ".autoscroll({ autoplay: {$autoPlay} , interval: {$interval}, autopause:{$autoPause} })";
        }
        //navigator plugin config
        $navigator='';
        if($params->get('navigator')) $navigator = ".navigator()";

        $js = "
            jQuery(document).ready(function(){
                jQuery('#{$moduleId}').scrollable({
                    vertical: {$animationMode},
                    speed: {$speed},
                    circular: {$repeat},
                    keyboard: {$keyboardNav}
                }){$autoScroll}{$navigator};
            });
        ";
        $doc->addScriptDeclaration($js);
    }
    
    /*Load jQuery with scroller js engine*/
    protected function load_jquery(&$params){
        $doc =& JFactory::getDocument();
        $app =& JFactory::getApplication();
        
        static $jqLoaded;
        
        if ($jqLoaded) {
            return;
        }   
   
        if($params->get('load_jquery') AND !$app->get('jQuery')){
            //get the cdn
            $cdn = $params->get('jquery_source');
            switch ($cdn){
                case 'google_cdn':
                    $file = 'https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js';
                    break;
                case 'local':
                    $file = JURI::root(true).'/modules/mod_xpertscroller/interface/js/jquery-1.4.4.js';
                    break;
            }
            $app->set('jQuery',1);
            $doc->addScript($file);
            $doc->addScriptDeclaration("jQuery.noConflict();");
            $jqLoaded = TRUE;
        }
        
        if(!defined('XPERT_SCROLLER')){
            //add scroller js file
            $doc->addScript(JURI::root(true).'/modules/mod_xpertscroller/interface/js/xpertscroller.js');
            define('XPERT_SCROLLER',1);
        }
    }
        
    /*
    * Load necesery style.
    * take all css settings and push it on document head
    */
    
    public function load_style(&$params,$module){
        $doc =& JFactory::getDocument();
        //set moduleid
        $moduleId = ($params->get('auto_module_id',0)==1) ? 'xs_'.$module->id : $params->get('module_unique_id');
        $moduleId = '#'.$moduleId;
        
        $scrollerLayout = $params->get('scroller_layout');
        
        /*
        * module unique id will only assign on horizontl style. 
        * this unique class will only use for navigation arrow styling
        * vertical style will auto adjuct arrow position to middle using css file.
        */
        $selectorClass = ($scrollerLayout == 'basic_h') ? '.' . $moduleId : '';
        
        //scroller wrapper widtha nd height. this width and height will effect on .pane class also.
        $moduleWidth = $paneWidth = (int)$params->get('module_width');
        $moduleHeight = (int)$params->get('module_height');
        
        /*
        * In horizontal style item width will calculated by persentage value
        * In vertical style item height will calculate on module height and num of columns
        */
        if($scrollerLayout == 'basic_h') $itemDimensions = 'width:'. 100 / (int)$params->get('col_amount') . '%';
        else $itemDimensions = 'width: 100%; height:' . $moduleHeight / $params->get('col_amount') .'px' ;
        
        $controlMargin = $params->get('control_margin');
        
        //items div always higher value thats way we will check animatin style and determine the proper css property
        $animationStyle = ($params->get('animation_style') == 'animation_h') ? 'width' : 'height';
        
        //resize image forcefully when image resize settings is turn on. later version will add auto thums generator engine.
        $imgWidth = NULL;
        $imgHeight = NULL;
        if ( $params->get('image_resize') ){
            $imgWidth   = (int)$params->get('image_width');
            $imgHeight  = (int)$params->get('image_height');
        }
        //preaper all css settings
        $css = "
            {$moduleId} {width: {$moduleWidth}px; height: {$moduleHeight}px;}
            {$moduleId} .pane {width: {$moduleWidth}px }
            {$moduleId} .items { {$animationStyle}:20000em; }
            {$moduleId} .pane .item{{$itemDimensions}; overflow:hidden; }
            {$moduleId} .item img{width: {$imgWidth}px; height: {$imgHeight}px;}
             a.browse{ margin:{$controlMargin}; }
            
        ";
        //push this css on document head
        $doc->addStyleDeclaration($css);
        
        //load stylesheet
        modXpertScrollerHelper::load_stylesheet($params);
        
    }
    /*
    * Load Stylesheet
    * 
    */    
    public function load_stylesheet(&$params){
        $app = &JApplication::getInstance('site', array(), 'J');
        $template = $app->getTemplate();
        $doc =& JFactory::getDocument();
        static $loadedBasicStyle;
        
        if ($loadedBasicStyle){
            return;
        }
        
        if($params->get('scroller_layout') == 'basic_h' || $params->get('scroller_layout') == 'basic_v'){
            if (file_exists(JPATH_SITE.DS.'templates'.DS.$template.'/css/xpertscroller-basic.css')) {
               $doc->addStyleSheet(JURI::root(true).'/templates/'.$template.'/css/xpertscroller-basic.css');
            }    
            else {
                $doc->addStyleSheet(JURI::root(true).'/modules/mod_xpertscroller/interface/css/xpertscroller-basic.css');
            }
            
            $loadedBasicStyle = TRUE;
        }
    }
    
}