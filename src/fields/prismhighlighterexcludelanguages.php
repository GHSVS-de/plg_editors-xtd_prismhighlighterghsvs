<?php
#namespace Joomla\CMS\Form\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Form Field to load a list of Languages/Aliases from my Prismjs content plugin *.json
 */
class JFormFieldPrismhighlighterexcludelanguages extends JFormFieldList
#class plgContentPrismHighlighterGhsvsFormFieldPrismhighlighterexcludelanguages extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $type = 'prismhighlighterexcludelanguages';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected static $options = array();

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.2
	 */
	protected function getOptions()
	{
		$helperFile = JPATH_SITE . '/plugins/content/prismhighlighterghsvs/Helper/PrismHighlighterGhsvsHelper.php';

		if (!is_file($helperFile))
		{
			$warning = 'Error: Helper file not found: /plugins/content/prismhighlighterghsvs/Helper/PrismHighlighterGhsvsHelper.php';
			return $warning;
		}
		\JLoader::register(
			'PrismHighlighterGhsvsHelper',
			$helperFile
		);
		
		$brushes = \PrismHighlighterGhsvsHelper::getAliasLanguageMap();
		
		if (!$brushes)
		{
			$warning = 'Error: Empty languages array from method PrismHighlighterGhsvsHelper::getAliasLanguageMap().';
			return $warning;
		}
		else
		{
			$hash = json_encode((array)$this->element);
			$hash = md5($hash);
			ksort($brushes,  SORT_NATURAL);

			if (!isset(static::$options[$hash]))
			{
				$options = array();
				
				/*if (isset($this->element['addJNONE']) && (string) $this->element['addJNONE'] === 'true')
				{
					
					// I really don't know why parent::getOptions() returns nothing
					// when <option value="">JNONE</option> in XML.
					// Answer: It was the f'ing md5 $hash calculation that returned for all fields
					// the same $hash => CHANGED.
					$do = new \stdClass;
					$do->value = '';
					$do->text = Text::_('JNONE');
					$options[] = $do;
				}*/

				foreach ($brushes as $value => $name)
				{
					$name = $name['aliasTitle'];
					
					$do = new \stdClass;
					$do->value = $value;
					$do->text = $name;
					$options[] = $do;
				}
				static::$options[$hash] = array_merge(parent::getOptions(), $options);
			}
		}

		return static::$options[$hash];
	}
}
