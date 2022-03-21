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
$index=0;
yyy
?>
<!--ThemeXpert: XpertScroller module version 2.0 Start here-->
<div class="<?php echo $moduleId;?> <?php echo $params->get('moduleclass_sfx');?> <?php echo $params->get('scroller_layout');?>">    
    <div id="<?php echo $moduleId;?>" class="scroller">
        <div class="items">
        <?php for($i = 0; $i<$totalPane; $i++){?>
            <div class="pane">
            <?php for($col=0; $col<(int)$params->get('col_amount'); $col++, $index++) {?>
                <?php if($index>=count($items)) break;?>
                <div class="item">
                    <div class="padding clearfix">
                        <?php echo ($params->get('item_image') == '1')? $items[$index]->image : '';?>

                        <?php if($params->get('article_title') === '1'):?>
                            <h4><?php echo $items[$index]->title;?></h4>
                        <?php endif;?>
                        
                        <?php if($params->get('intro_text') === '1'):?>
                            <p class="xs_intro"><?php echo $items[$index]->introtext;?></p>
                        <?php endif;?>

                        <?php if($params->get('readmore') === '1'):?>
                            <p class="xs_readmore"><a href="<?php echo $items[$index]->link;?>">Readmore..</a> </p>
                        <?php endif;?>
                    </div>
                </div>
                <?php if($col == (int)$params->get('col_amount') ){$col=0; break;} ?>
            <?php } ?>
            </div>
        <?php }?>
        </div>
    </div>
    <?php if($params->get('navigator')):?>
    <!-- wrapper for navigator elements -->
    <div class="navi"></div>
    <?php endif;?>
	<a class="prev browse left"></a>
	<a class="next browse right"></a>
</div>
<!--ThemeXpert: XpertScroller module version 2.0 End Here-->