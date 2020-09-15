(function()
{
	"use strict";

	document.addEventListener('DOMContentLoaded', function()
	{
		var form = document.getElementById('PRISMHIGHLIGHTERGHSVS');
		var tag;
		var attributes = [];
		var classes = [];
		let tagOn, tagOff;

		document.getElementById('insertPrismhighlighterGhsvs').addEventListener('click', function()
		{
			if (!window.parent.Joomla.getOptions('xtd-prismhighlighterghsvs'))
			{
				// Something went wrong!
				if (window.parent.Joomla.Modal)
				{
					// Joomla 4
					window.parent.Joomla.Modal.getCurrent().close();
				}
				else
				{
					// Joomla 3
					window.parent.jModalClose();
				}
				return false;
			}

			var editor = window.parent.Joomla.getOptions('xtd-prismhighlighterghsvs').editor;

			var formData = {};

			for (var i = 0; i < form.elements.length; i++)
			{
				var e = form.elements[i];

				if (e.name)
				{
					formData[e.name] = e.value;
				}
			}

			formData.codeInput = formData.codeInput.replace(/&/g, '&amp;');
			formData.codeInput = formData.codeInput.replace(/\"/g, '&quot;');
			formData.codeInput = formData.codeInput.replace(/\'/g, '&#039;');
			formData.codeInput = formData.codeInput.replace(/</g, '&lt;');
			formData.codeInput = formData.codeInput.replace(/>/g, '&gt;');

			formData.ariaLabel = formData.ariaLabel.replace(/&/g, '&amp;');
			formData.ariaLabel = formData.ariaLabel.replace(/\"/g, '&quot;');
			formData.ariaLabel = formData.ariaLabel.replace(/\'/g, '&#039;');
			formData.ariaLabel = formData.ariaLabel.replace(/</g, '&lt;');
			formData.ariaLabel = formData.ariaLabel.replace(/>/g, '&gt;');
			formData.ariaLabel = formData.ariaLabel.trim();

			formData.cssClasses = formData.cssClasses.replace(/&/g, '&amp;');
			formData.cssClasses = formData.cssClasses.replace(/\"/g, '&quot;');
			formData.cssClasses = formData.cssClasses.replace(/\'/g, '&#039;');
			formData.cssClasses = formData.cssClasses.replace(/</g, '&lt;');
			formData.cssClasses = formData.cssClasses.replace(/>/g, '&gt;');
			formData.cssClasses = formData.cssClasses.trim();

			formData.fileHighlight = formData.fileHighlight.replace(/&/g, '&amp;');
			formData.fileHighlight = formData.fileHighlight.replace(/\"/g, '&quot;');
			formData.fileHighlight = formData.fileHighlight.replace(/\'/g, '&#039;');
			formData.fileHighlight = formData.fileHighlight.replace(/</g, '&lt;');
			formData.fileHighlight = formData.fileHighlight.replace(/>/g, '&gt;');
			formData.fileHighlight = formData.fileHighlight.trim();

			if (formData.selectBrush)
			{
				classes.push(`language-${formData.selectBrush}`);
			}

			formData.firstLine = formData.firstLine.trim();

			if (!formData.firstLine || isNaN(formData.firstLine))
			{
				formData.firstLine = "";
			}

			// Plugin file-highlight
			if (formData.fileHighlight)
			{
				if (formData.addJUri && formData.fileHighlight.charAt(0) !== "/")
				{
					attributes.push(`data-src-addjuri`);
				}
				attributes.push(`data-src="${formData.fileHighlight}"`);
				formData.selectTag = "pre";
			}

			// Plugin line-hihlight
			formData.textLines = formData.textLines.replace(/\s+/gm, '');

			if (formData.textLines)
			{
				attributes.push(`data-line="${formData.textLines}"`);
				
				// ToDo: Old script needed?
				/*if (formData.firstLine)
				{
					var textLinesArray = formData.textLines.split(",");
					var textlines = [];

					for (var i = 0; i < textLinesArray.length; i++)
					{
						if (!isNaN(textLinesArray[i]))
						{
							textlines.push(parseInt(textLinesArray[i]) + parseInt(formData.firstLine) - 1);
						}
					}

					if (textlines.length)
					{
						formData.textLines = "; highlight:[" + textlines.join(",") + "]";
					}
				}
				else
				{
					formData.textLines = "; highlight:[" + formData.textLines + "]";
				}*/
			}

			if (formData.firstLine)
			{
				attributes.push(`data-line-offset="${formData.firstLine}"`);
			}

			if (formData.ariaLabel)
			{
				// attributes.push(`aria-label="${formData.ariaLabel}"`);
				formData.ariaLabel = `aria-label="${formData.ariaLabel}"`;
			}
			if (formData.cssClasses)
			{
				classes.push(formData.cssClasses);
			}
			if (classes.length)
			{
				attributes.push(`class="${classes.join(' ')}"`);
			}
			
			if (attributes.length)
			{
				attributes = ` ${attributes.join(' ')}`;
			}
			else
			{
				attributes = ""; 
			}

			if (formData.selectTag === "pre-code")
			{
				tagOn = `<pre${attributes}><code>`;
				tagOff = '</code></pre>';
			}
			else if (formData.selectTag === "code")
			{
				tagOn = `<code${attributes}>`;
				tagOff = '</code>';				
			}
			else if (formData.selectTag === "pre")
			{
				tagOn = `<pre${attributes}>`;
				tagOff = '</pre>';						
			}

			tag = `<div class="codeContainer"${formData.ariaLabel}>`
				+ tagOn
				+ formData.codeInput
				+ tagOff
				+ '</div><p>&nbsp;</p>';

			/** Use the API, if editor supports it **/
			if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor))
			{
				window.parent.Joomla.editors.instances[editor].replaceSelection(tag)
			}
			else
			{
				window.parent.jInsertEditorText(tag, editor);
			}

			if (window.parent.Joomla.Modal)
			{
				// Joomla 4
				window.parent.Joomla.Modal.getCurrent().close();
			}
			else
			{
				// Joomla 3
				window.parent.jModalClose();
			}
		});
	});
})();