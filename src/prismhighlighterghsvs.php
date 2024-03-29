<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

class plgButtonPrismhighlighterGhsvs extends CMSPlugin
{
	protected $autoloadLanguage = true;
	protected $app;

	function onDisplay($editorname, $asset, $author)
	{
		if (!$this->app->isClient('administrator'))
		{
			return false;
		}

		$user = Factory::getUser();

		$extension = $this->app->input->get('option');

		if ($extension === 'com_categories')
		{
			$parts = explode('.', $this->app->input->get('extension', 'com_content'));
			$extension = $parts[0];
		}

		$asset = $asset !== '' ? $asset : $extension;

		if (
			!(
				$user->authorise('core.edit', $asset)
				|| $user->authorise('core.create', $asset)
				|| (count($user->getAuthorisedCategories($asset, 'core.create')) > 0)
				|| ($user->authorise('core.edit.own', $asset) && $author === $user->id)
				|| (count($user->getAuthorisedCategories($extension, 'core.edit')) > 0)
				|| (count($user->getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author === $user->id)
			)
		){
			return false;
		}

		$brushOptions = '<option value="">' . Text::_('JNONE') . '</option>';
		$warning = '';

		$helperFile = JPATH_SITE . '/plugins/content/prismhighlighterghsvs/Helper/PrismHighlighterGhsvsHelper.php';

		if (!is_file($helperFile))
		{
			$warning = 'PLG_XTD_PRISMHIGHLIGHTERGHSVS_CONTENT_PLUGIN_HELPER_NOT_FOUND';
		}

		if (!$warning)
		{
			\JLoader::register('PrismHighlighterGhsvs', $helperFile);

			// Can we load aliasLanguageMap.json and convert to array?
			if (($brushes = \PrismHighlighterGhsvs::getAliasLanguageMap()) === false)
			{
				$warning = 'PLG_XTD_PRISMHIGHLIGHTERGHSVS_BRUSHES_NOT_FOUND';
			}
			else
			{
				/* An array of prism languages that user has already selected for
					inclusion or exclusion in plugin configuration. */
				$includeExcludeLang = array_flip($this->params->get(
					'includeExcludeLang', []));

				// Include them or exclude them?
				$includeExclude = $this->params->get('includeExclude', 'include');

				// A single default language that shall be pre-selected in button popup.
				$defaultLang = $this->params->get('defaultLang', '');

				/* Filter languages (brushes) that shall be displayed in editor button
					popup.*/
				foreach ($brushes as $alias => $infos)
				{
					if ($includeExcludeLang)
					{
						if ($includeExclude === 'include'
							&& !isset($includeExcludeLang[$alias]))
						{
							unset($brushes[$alias]);
						}
						elseif ($includeExclude === 'exclude'
							&& isset($includeExcludeLang[$alias]))
						{
							unset($brushes[$alias]);
						}
					}
				}

				// Sort by language name.
				ksort($brushes);

				// Build select box for 'snippet language' in popup form.
				foreach ($brushes as $alias => $infos)
				{
					$selected = '';

					if ($alias === $defaultLang)
					{
						$selected = ' selected="selected"';
					}
					$brushOptions .= '<option value="' . $infos['alias'] . '"' . $selected . '>'
						. $infos['aliasTitle'] . '</option>';
				}
			}
		}

		$popupDir = 'media/plg_editors-xtd_prismhighlighterghsvs/html/';

		$popupTmpl = file_get_contents(JPATH_SITE . '/' . $popupDir
			.  'insertcode_tmpl.html');

		$replaceWith = array(
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_ADD_JURI'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_ADD_JURI'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_FILEHIGHLIGHT'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_FILEHIGHLIGHT'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_HEADLINE'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_HEADLINE'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_SELECTTAG'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_SELECTTAG'),
			'WARNING' =>
				$warning ? '<p><strong style="color:red">' . Text::_($warning)
					. '</strong></p>' : '',
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_SELECTBRUSH'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_SELECTBRUSH'),
			'BRUSHOPTIONS'
				=> $brushOptions,
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_CSSCLASSES'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_CSSCLASSES'),
			'CSS_CLASSES'
				=> $this->params->get('cssClasses', 'line-numbers'),
			'TAGOPTIONS'
				=> '<option value="pre-code">&lt;pre&gt;&lt;code&gt;</option>'
					. '<option value="code">&lt;code&gt;</option>'
					. '<option value="pre">&lt;pre&gt;</option>',
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_CODEINPUT'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_CODEINPUT'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_TEXTLINES'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_TEXTLINES'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_FIRSTLINE'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_FIRSTLINE'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_INSERTCODE'
				=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_INSERTCODE'),
			'PLG_XTD_PRISMHIGHLIGHTERGHSVS_MINIFIED_JS' => JDEBUG ? '' : '.min',
			'ADDJURIOPTIONS'
				=> '<option value="0">' . Text::_('JNO') . '</option>'
					. '<option value="1">' . Text::_('JYES') . '</option>',
			'[VERSION]' => '?' . time(),
		);

		// Fill popup form template.
		foreach ($replaceWith as $replace => $with)
		{
			$popupTmpl = str_replace($replace, $with, $popupTmpl);
		}

		$lang = Factory::getLanguage();
		$popupFile = $popupDir . 'insertcode_popup.' . $lang->getTag() . '.html';

		file_put_contents(JPATH_SITE . '/' . $popupFile, $popupTmpl);

		HTMLHelper::_('behavior.core');
		Factory::getDocument()->addScriptOptions('xtd-prismhighlighterghsvs',
			[
				'editor' => $editorname,
				'JUri' => Uri::root(),
				'shortcodesWarning' => (int) $this->params->get('shortcodesWarning', 1),
				'shortcodesWarningWarning'
					=> Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_SHORTCODESWARNING_WARNING'),
			]
		);

		$root = '';

		// Editors prepend JUri::base() to $link. Whyever.
		if ($this->app->isClient('administrator'))
		{
			$root = '../';
		}

		// Build editor button.
		$button = new CMSObject;
		$button->set('class', 'btn');
		$button->modal = true;
		$button->link = $root . $popupFile . '?editor=' . urlencode($editorname);
		$button->set('text', Text::_('PLG_XTD_PRISMHIGHLIGHTERGHSVS_BUTTON'));
		// BC break in Joomla 4.2.7. Use unique name.
		// $button->name = 'file-add'; // icon class without 'icon-'
		$button->name = $this->_type . '_' . $this->_name;
		$button->options = "{handler: 'iframe', size: {x: 800, y: 550}}";
		return $button;
	}
}
