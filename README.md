Textformatter Iframe Embed ProcessWire module
---------------------------------------------

Once enabled, this textformatter looks for iframe embed URLs and converts those to HTML `<iframe>` elements. Works best with RTE (CKEditor, TinyMCE) inputs.

Note that URLs need to be the only content within a paragraph tag. You can use an absolute URL or a relative URL, but in the case of relative URLs the path must point to a file within `/site/assets/files/`:

```HTML
<!-- valid -->
<p>iframe/https://www.domain.tld/path/</p>
<p>iframe//site/assets/files/1/example.pdf</p>

<!-- invalid -->
<p>iframe/Dude, where's my https://www.domain.tld/car/</p>
<p>iframe/site/assets/files/1/example.pdf</p>
<p>iframe//some/other/path/example.pdf</p>
```

Embed tag (default: `iframe/`) and iframe tag can be customized via module configuration settings. Default iframe tag is `<iframe class="TextformatterIframeEmbed" src="{url}"></iframe>`; `{url}` is used as a placeholder for the provided source URL.
