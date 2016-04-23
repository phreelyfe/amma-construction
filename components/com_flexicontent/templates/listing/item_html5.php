<?php
/**
 * HTML5 Template
 * @version 1.5 stable $Id: item_html5.php 0001 2012-09-23 14:00:28Z Rehne $
 * @package Joomla
 * @subpackage FLEXIcontent
 * @copyright (C) 2009 Emmanuel Danan - www.vistamedia.fr
 * @license GNU/GPL v2
 * 
 * FLEXIcontent is a derivative work of the excellent QuickFAQ component
 * @copyright (C) 2008 Christoph Lukes
 * see www.schlu.net for more information
 *
 * FLEXIcontent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
// first define the template name
$tmpl = $this->tmpl;
$item = $this->item;
$menu = JFactory::getApplication()->getMenu()->getActive();

// Create description field if not already created
FlexicontentFields::getFieldDisplay($item, 'text', $values=null, $method='display');

// Find if description is placed via template position
$_text_via_pos = false;
if (isset($item->positions) && is_array($item->positions)) {
	foreach ($item->positions as $posName => $posFields) {
		if ($posName == 'renderonly') continue;
		foreach($posFields as $field) if ($field->name=='text') { $_text_via_pos = true; break; }
	}
}

// Prepend toc (Table of contents) before item's description (toc will usually float right)
// By prepend toc to description we make sure that it get's displayed at an appropriate place
if (isset($item->toc)) {
	$item->fields['text']->display = $item->toc . $item->fields['text']->display;
}

// Set the class for controlling number of columns in custom field blocks
switch ($this->params->get( 'columnmode', 2 )) {
	case 0: $columnmode = 'singlecol'; break;
	case 1: $columnmode = 'doublecol'; break;
	default: $columnmode = 'variablecol'; break;
}
// ***********
// DECIDE TAGS 
// ***********
$page_heading_shown =
	$this->params->get( 'show_page_heading', 1 ) &&
	$this->params->get('page_heading') != $item->title &&
	$this->params->get('show_title', 1);

// Main container
$mainAreaTag = $page_heading_shown ? 'section' : 'article';

// SEO, header level of title tag
$itemTitleHeaderLevel = $page_heading_shown ? '2' : '1'; 

$page_classes  = 'flexicontent';
$page_classes .= $this->pageclass_sfx ? ' page'.$this->pageclass_sfx : '';
$page_classes .= ' fcitems fcitem'.$item->id;
$page_classes .= ' fctype'.$item->type_id;
$page_classes .= ' fcmaincat'.$item->catid;
if ($menu) $page_classes .= ' menuitem'.$menu->id; 

// SEO
$microdata_itemtype = $this->params->get( 'microdata_itemtype');
$microdata_itemtype_code = $microdata_itemtype ? 'itemscope itemtype="http://schema.org/'.$microdata_itemtype.'"' : '';
?>

<?php echo '<'.$mainAreaTag; ?> id="flexicontent" class="<?php echo $page_classes; ?>" <?php echo $microdata_itemtype_code; ?> >
	
	<?php echo ( ($mainAreaTag == 'section') ? '<header>' : ''); ?>
	
  <?php if ($item->event->beforeDisplayContent) : ?>
		<!-- BOF beforeDisplayContent -->
		<?php echo ( ($mainAreaTag == 'section') ? '<aside' : '<div'); ?> class="fc_beforeDisplayContent group">
			<?php echo $item->event->beforeDisplayContent; ?>
		<?php echo ( ($mainAreaTag == 'section') ? '</aside>' : '</div>'); ?>
		<!-- EOF beforeDisplayContent -->
	<?php endif; ?>
	
	<?php if (JRequest::getCmd('print')) : ?>
		<!-- BOF Print handling -->
		<?php if ($this->params->get('print_behaviour', 'auto') == 'auto') : ?>
			<script type="text/javascript">jQuery(document).ready(function(){ window.print(); });</script>
		<?php	elseif ($this->params->get('print_behaviour') == 'button') : ?>
			<input type='button' id='printBtn' name='printBtn' value='<?php echo JText::_('Print');?>' class='btn btn-info' onclick='this.style.display="none"; window.print(); return false;'>
		<?php endif; ?>
		<!-- EOF Print handling -->
		
	<?php else : ?>
	
		<?php
		$pdfbutton = flexicontent_html::pdfbutton( $item, $this->params );
		$mailbutton = flexicontent_html::mailbutton( FLEXI_ITEMVIEW, $this->params, $item->categoryslug, $item->slug, 0, $item );
		$printbutton = flexicontent_html::printbutton( $this->print_link, $this->params );
		$editbutton = flexicontent_html::editbutton( $item, $this->params );
		$statebutton = flexicontent_html::statebutton( $item, $this->params );
		$approvalbutton = flexicontent_html::approvalbutton( $item, $this->params );
		?>
		
		<?php if ($pdfbutton || $mailbutton || $printbutton || $editbutton || $statebutton || $approvalbutton) : ?>
		
			<!-- BOF buttons -->
			<?php if ($this->params->get('btn_grp_dropdown')) : ?>
			
			<div class="buttons btn-group">
			  <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
			    <span class="<?php echo $this->params->get('btn_grp_dropdown_class', 'icon-options'); ?>"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu">
			    <?php echo $pdfbutton    ? '<li>'.$pdfbutton.'</li>' : ''; ?>
			    <?php echo $mailbutton   ? '<li>'.$mailbutton.'</li>' : ''; ?>
			    <?php echo $printbutton  ? '<li>'.$printbutton.'</li>' : ''; ?>
			    <?php echo $editbutton   ? '<li>'.$editbutton.'</li>' : ''; ?>
			    <?php echo $approvalbutton  ? '<li>'.$approvalbutton.'</li>' : ''; ?>
			  </ul>
		    <?php echo $statebutton; ?>
			</div>

			<?php else : ?>
			<div class="buttons">
				<?php echo $pdfbutton; ?>
				<?php echo $mailbutton; ?>
				<?php echo $printbutton; ?>
				<?php echo $editbutton; ?>
				<?php echo $statebutton; ?>
				<?php echo $approvalbutton; ?>
			</div>
			<?php endif; ?>
			<!-- EOF buttons -->
			
		<?php endif; ?>
	<?php endif; ?>
	
	<?php if ( $page_heading_shown ) : ?>
		<!-- BOF page heading -->
		<header>
		<h1 class="componentheading">
			<?php echo $this->params->get('page_heading'); ?>
		</h1>
		</header>
		<!-- EOF page heading -->
	<?php endif; ?>

	<?php echo ( ($mainAreaTag == 'section') ? '</header>' : ''); ?>
	
	<?php echo ( ($mainAreaTag == 'section') ? '<article>' : ''); ?>
	
	<?php if ($this->params->get('show_title', 1)) : /*open header container of title*/ ?>
	<header class="group">
	<?php endif; ?>
	
	<?php if ($this->params->get('show_title', 1)) : ?>
		<!-- BOF item title -->
		<?php echo '<h'.$itemTitleHeaderLevel; ?> class="contentheading">
			<span class="fc_item_title html5" itemprop="name">
			<?php
				echo ( mb_strlen($item->title, 'utf-8') > $this->params->get('title_cut_text',200) ) ?
					mb_substr ($item->title, 0, $this->params->get('title_cut_text',200), 'utf-8') . ' ...'  :  $item->title;
			?>
			</span>
		<?php echo '</h'.$itemTitleHeaderLevel; ?>>
		<!-- EOF item title -->
	<?php endif; ?>
	
  <?php if ($item->event->afterDisplayTitle) : ?>
		<!-- BOF afterDisplayTitle -->
		<div class="fc_afterDisplayTitle group">
			<?php echo $item->event->afterDisplayTitle; ?>
		</div>
		<!-- EOF afterDisplayTitle -->
	<?php endif; ?>

	<?php if ((intval($item->modified) !=0 && $this->params->get('show_modify_date')) || ($this->params->get('show_author') && ($item->creator != "")) || ($this->params->get('show_create_date')) || (($this->params->get('show_modifier')) && (intval($item->modified) !=0))) : ?>
	<!-- BOF item basic/core info -->
	<div class="iteminfo group">
		
		<?php if (($this->params->get('show_author')) && ($item->creator != "")) : ?>
		<span class="createdline">
			<span class="createdby">
				<?php FlexicontentFields::getFieldDisplay($item, 'created_by', $values=null, $method='display'); ?>
				<?php echo JText::sprintf('FLEXI_WRITTEN_BY', $this->fields['created_by']->display); ?>
			</span>
			<?php endif; ?>
			
			<?php if (($this->params->get('show_author')) && ($item->creator != "") && ($this->params->get('show_create_date'))) : ?>
			::
			<?php endif; ?>
	
			<?php if ($this->params->get('show_create_date')) : ?>
			<span class="created">
				<?php FlexicontentFields::getFieldDisplay($item, 'created', $values=null, $method='display'); ?>
				<?php echo '['.JHTML::_('date', $this->fields['created']->value[0], JText::_('DATE_FORMAT_LC2')).']'; ?>		
			</span>
			<?php endif; ?>
		</span>
		
		<span class="modifiedline">
			<?php if (($this->params->get('show_modifier')) && ($item->modifier != "")) : ?>
			<span class="modifiedby">
				<?php FlexicontentFields::getFieldDisplay($item, 'modified_by', $values=null, $method='display'); ?>
				<?php echo JText::_('FLEXI_LAST_UPDATED').' '.JText::sprintf('FLEXI_BY', $this->fields['modified_by']->display); ?>
			</span>
			<?php endif; ?>
	
			<?php if (($this->params->get('show_modifier')) && ($item->modifier != "") && ($this->params->get('show_modify_date'))) : ?>
			::
			<?php endif; ?>
			
			<?php if (intval($item->modified) !=0 && $this->params->get('show_modify_date')) : ?>
				<span class="modified">
				<?php FlexicontentFields::getFieldDisplay($item, 'modified', $values=null, $method='display'); ?>
				<?php echo '['.JHTML::_('date', $this->fields['modified']->value[0], JText::_('DATE_FORMAT_LC2')).']'; ?>
				</span>
			<?php endif; ?>
		</span>
	</div>
	<!-- EOF item basic/core info -->
	<?php endif; ?>
	
	<?php if ($this->params->get('show_title', 1)) : /*close header container of title*/ ?>
	</header>
	<?php endif; ?>

	<?php if (($this->params->get('show_vote', 1)) || ($this->params->get('show_favs', 1)))  : ?>
	<!-- BOF item rating, favourites -->
	<aside class="itemactions group">
		<?php if ($this->params->get('show_vote', 1)) : ?>
		<div class="voting">
		<?php FlexicontentFields::getFieldDisplay($item, 'voting', $values=null, $method='display'); ?>
		<?php echo $this->fields['voting']->display; ?>
		</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_favs', 1)) : ?>
		<span class="favourites">
			<?php FlexicontentFields::getFieldDisplay($item, 'favourites', $values=null, $method='display'); ?>
			<?php echo $this->fields['favourites']->display; ?>
		</span>
		<?php endif; ?>
	</aside>
	<!-- EOF item rating, favourites -->
	<?php endif; ?>
	
	
	<?php if (isset($item->positions['beforedescription'])) : ?>
	<!-- BOF beforedescription block -->
	<div class="customblock beforedescription group">
		<?php foreach ($item->positions['beforedescription'] as $field) : ?>
		<div class="flexi element field_<?php echo $field->name; ?> <?php echo $columnmode; ?>">
			<?php if ($field->label) : ?>
			<span class="flexi label field_<?php echo $field->name; ?>"><?php echo $field->label; ?></span>
			<?php endif; ?>
			<div class="flexi value field_<?php echo $field->name.' '.(!$field->label ? ' nolabel ' : ''); ?>">
				<?php echo $field->display; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<!-- EOF beforedescription block -->
	<?php endif; ?>
	
	<?php if (!$_text_via_pos): ?>
	<!-- BOF description block -->
	<div class="description group">
	<?php echo $this->fields['text']->display; ?>
	</div>
	<!-- EOF description block -->
	<?php endif; ?>

	<?php if (isset($item->positions['afterdescription'])) : ?>
	<!-- BOF afterdescription block -->
	<div class="customblock afterdescription group">
		<?php foreach ($item->positions['afterdescription'] as $field) : ?>
		<div class="flexi element field_<?php echo $field->name; ?> <?php echo $columnmode; ?>">
			<?php if ($field->label) : ?>
			<span class="flexi label field_<?php echo $field->name; ?>"><?php echo $field->label; ?></span>
			<?php endif; ?>
			<div class="flexi value field_<?php echo $field->name.' '.(!$field->label ? ' nolabel ' : ''); ?>">
				<?php echo $field->display; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<!-- EOF afterdescription block -->
	<?php endif; ?>
	
	<?php if (($this->params->get('show_tags', 1)) || ($this->params->get('show_category', 1)))  : ?>
	<!-- BOF item categories, tags -->
	<div class="itemadditionnal group">
		<?php if ($this->params->get('show_category', 1)) : ?>
		<div class="categories">
			<?php FlexicontentFields::getFieldDisplay($item, 'categories', $values=null, $method='display'); ?>
			<span class="flexi label"><?php echo $this->fields['categories']->label; ?></span>
			<div class="flexi value"><i class="icon-folder-open"></i> <?php echo $this->fields['categories']->display; ?></div>
		</div>
		<?php endif; ?>

		<?php FlexicontentFields::getFieldDisplay($item, 'tags', $values=null, $method='display'); ?>
		<?php if ($this->params->get('show_tags', 1) && $this->fields['tags']->display) : ?>
		<div class="tags">
			<span class="flexi label"><?php echo $this->fields['tags']->label; ?></span>
			<div class="flexi value"><i class="icon-tags"></i> <?php echo $this->fields['tags']->display; ?></div>
		</div>
		<?php endif; ?>
	</div>
	<!-- EOF item categories, tags  -->
	<?php endif; ?>

	<?php if ($this->params->get('comments') && !JRequest::getVar('print')) : ?>
	<!-- BOF comments -->
	<section class="comments group">
	<?php
		if ($this->params->get('comments') == 1) :
			if (file_exists(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php')) :
				require_once(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
				echo JComments::showComments($item->id, 'com_flexicontent', $this->escape($item->title));
			endif;
		endif;
	
		if ($this->params->get('comments') == 2) :
			if (file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php')) :
    			require_once(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php');
    			echo jomcomment($item->id, 'com_flexicontent');
  			endif;
  		endif;
	?>
	</section>
	<!-- EOF comments -->
	<?php endif; ?>
    
    <?php echo ( ($mainAreaTag == 'section') ? '</article>' : ''); ?>

	<?php if ($item->event->afterDisplayContent) : ?>
	<!-- BOF afterDisplayContent -->
	<?php echo ( ($mainAreaTag == 'section') ? '<footer' : '<div'); ?> class="fc_afterDisplayContent group">
		<?php echo $item->event->afterDisplayContent; ?>
	<?php echo ( ($mainAreaTag == 'section') ? '</footer>' : '</div>'); ?>
	<!-- EOF afterDisplayContent -->
	<?php endif; ?>
	
<?php echo '</'.$mainAreaTag.'>'; ?>
