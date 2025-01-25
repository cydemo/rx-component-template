<?php
/* Copyright (C) cydemo <https://www.xetown.com> */
/**
 * @class  ap_template
 * @author cydemo (cydemo@gmail.com)
 * @brief Template Component
 */
class ap_template extends EditorHandler
{
	// Properties
	public $editor_sequence = 0;
	public $component_path = '';
	public $template_path = '';

	/**
	 * @brief editor_sequence and components out of the path
	 */
	public function __construct($editor_sequence, $component_path)
	{
		$this->editor_sequence = $editor_sequence;
		$this->component_path = $component_path;
		$this->template_path = sprintf('%s%s', preg_replace('/^\.\//i', '', $this->component_path), 'templates');
	}

	/**
	 * @brief popup window to display in popup window request is to add content
	 */
	public function getPopupContent()
	{
		// Bringing a list of templates directory
		$template_dir = FileHandler::readDir($this->template_path);
		$template_list = array();
		foreach ( $template_dir as $key => $template )
		{
			$xml = FileHandler::readFile($this->template_path . '/' . $template . '/info.xml');
			$oXmlParser = new XeXmlParser();
			$xml_info = $oXmlParser->parse($xml)->template;

			if ( ( Context::get('logged_info')->is_admin !== 'Y' &&  $xml_info->admin->body === 'Y' )
				|| ( Context::get('type') === 'full_page' && $xml_info->type->full_page->body !== 'Y' )
				|| ( Context::get('type') === 'header' && $xml_info->type->header->body !== 'Y' )
				|| ( Context::get('type') === 'section' && $xml_info->type->section->body !== 'Y' )
				|| ( Context::get('type') === 'footer' && $xml_info->type->footer->body !== 'Y' ) )
			{
				continue;
			}

			$template_list[$key] = new stdClass();
			$template_list[$key]->dir = $template;
			$template_list[$key]->title = $xml_info->title->body;
			$template_list[$key]->desc = $xml_info->description->body;
			$template_list[$key]->version = $xml_info->version->body;
			$template_list[$key]->date = $xml_info->date->body;
			$template_list[$key]->author = $xml_info->author->name->body;
			$template_list[$key]->email = $xml_info->author->attrs->email_address;
			$template_list[$key]->link = $xml_info->author->attrs->link;
			$template_list[$key]->admin = $xml_info->admin;
			$template_list[$key]->full_page = $xml_info->type->full_page->body;
			$template_list[$key]->header = $xml_info->type->header->body;
			$template_list[$key]->section = $xml_info->type->section->body;
			$template_list[$key]->footer = $xml_info->type->footer->body;
			$template_list[$key]->thumb = $this->template_path . '/' . $template . '/thumb.jpg';
		}
		
		// Set Page Navigation
		$list_count = 10;
		$total_count = count($template_list);
		$total_page = ceil($total_count / $list_count);
		$page = Context::get('page') ? abs(Context::get('page')) : 1;
		Context::set('page', $page);
		if ( $page > $total_page )
		{
			$page = $total_page;
		}
		if ( $total_page > 1 )
		{
			$page_navigation = new PageHandler($total_page, $total_page, $page, $page_count = 5);
		}
		else
		{
			$page_navigation = false;
		}
		Context::set('page_navigation', $page_navigation);
		
		// Slice of Template List
		$template_list = array_slice($template_list, $list_count*($page-1), $list_count, true);
		Context::set('template_list', $template_list);

		// Bringing editor skin information in the current module
		$module_info = ModuleModel::getModuleInfoByMid(Context::get('mid'));
		$editor_config = EditorModel::getEditorConfig($module_info->module_srl);
		Context::set('editor_skin', $editor_config->editor_skin);

		// Pre-compiled source code to compile template return to
		$tpl_path = $this->component_path.'tpl';
		$tpl_file = 'popup.html';

		$oTemplate = TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);
	}

	public function getTemplate()
	{
		$template = Context::get('template');

		$template_path = sprintf('%s/%s', $this->template_path, $template);
		$template_file = 'index.html';

		$oTemplate = TemplateHandler::getInstance();
		$this->add('template', $oTemplate->compile($template_path, $template_file));
	}
}
/* End of file ap_template.class.php */
/* Location: ./modules/editor/components/ap_template/ap_template.class.php */