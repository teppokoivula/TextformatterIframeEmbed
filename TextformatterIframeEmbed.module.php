<?php namespace ProcessWire;

/**
 * ProcessWire Iframe Embed Textformatter
 *
 * Looks for iframe embeds (syntax: iframe/https://www...) and automatically converts them to HTML <iframe> elements.
 *
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license Mozilla Public License v2.0 https://mozilla.org/MPL/2.0/
 */
class TextformatterIframeEmbed extends Textformatter implements Module, ConfigurableModule {

	/**
	 * Get module info
	 *
	 * @return array
	 */
	public static function getModuleInfo() {
		return [
			'title' => 'Iframe Embed Text Formatter',
			'version' => '0.1.0',
			'summary' => 'Converts iframe URLs prefixed with "iframe/" within paragraph tags into HTML iframe elements.',
			'author' => 'Teppo Koivula',
		];
	}

    /**
     * Default configuration for this module
     *
     * @return array
     */
    public static function getDefaultData() {
        return [
            'embed_tag' => 'iframe/',
            'iframe_tag' => '<iframe class="TextformatterIframeEmbed" src="{url}"></iframe>',
		];
    }

    /**
     * Populate the default config data
     */
    public function __construct() {
        foreach (self::getDefaultData() as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Module configuration
     *
     * @param array $data
     * @return InputfieldWrapper
     */
    public function getModuleConfigInputfields(array $data) {

		// container for fields
        $fields = $this->wire(new InputfieldWrapper());

        // merge default config settings (custom values overwrite defaults)
        $defaults = self::getDefaultData();
        $data = array_merge($defaults, $data);

		// embed tag
		$embed_tag = $this->wire(new InputfieldText());
		$embed_tag->name = 'embed_tag';
		$embed_tag->label = $this->_('Embed tag');
		$embed_tag->description = $this->_('Embed tag for identifying embeddable iframe URLs within content.');
		$embed_tag->notes = sprintf($this->_('Default: `%s`, for matching strings such as `%1$shttps://www.domain.tld/`'), $defaults[$embed_tag->name]);
		$embed_tag->icon = 'terminal';
		$embed_tag->value = $data[$embed_tag->name];
		$fields->add($embed_tag);

		// iframe tag
		$iframe_tag = $this->wire(new InputfieldTextarea());
		$iframe_tag->name = 'iframe_tag';
		$iframe_tag->label = $this->_('Iframe tag');
		$iframe_tag->description = $this->_('Tag used for iframe elements. Use {url} as a placeholder for the URL.');
		$iframe_tag->notes = sprintf($this->_('Default: `%1$s`'), $defaults[$iframe_tag->name]);
		$iframe_tag->icon = 'code';
		$iframe_tag->value = $data[$iframe_tag->name];
		$fields->add($iframe_tag);

		return $fields;
	}

	/**
	 * Text formatting function as used by the Textformatter interface
	 *
	 * @param string $str
	 */
	public function format(&$str) {

		// get embed tag
		$embed_tag = preg_quote($this->embed_tag, '/');

		// bail out early if there are no iframes
		if (stripos($str, $this->embed_tag) === false) {
			return;
		}

		// capture audio file URLs with regex and replace found matches with <audio> elements
		if (preg_match_all('/\<p\>\s*' . $embed_tag . '(https?:\/\/.*?|\/site\/assets\/files\/.*?)\s*\<\/p\>/i', $str, $matches)) {
			foreach ($matches[0] as $key => $line) {
				$url = $this->wire('sanitizer')->url($matches[1][$key], [
					'allowIDN' => true,
					'allowSchemes' => ['http', 'https'],
					'requireScheme' => false,
				]);
				if ($url) {
					$tag = str_replace([
						'{url}',
					], [
						$url,
					], $this->iframe_tag);
					$str = str_replace($line, $tag, $str);
				}
			}
		}
	}

}
