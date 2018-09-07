<?php

class rex_effect_insert_text extends rex_effect_abstract
{
    /**
     * Generate image
     */
    public function execute()
    {
        $this->media->asImage();

        // -------------------------------------- CONFIG
        $fontFile = rex_path::media($this->params['font_file']);
        if (!file_exists($fontFile) || !is_file($fontFile)) {
            return;
        }

        $gdImage = $this->media->getImage();

        // Keep transparency (for GIF, PNG & WebP)
        $this->keepTransparent($gdImage);

        // Text
        $text = 'Hello World';

        if (($this->params['text_source'] === 'input') && isset($this->params['text'])) {
            // Take text from input field
            $text = (string) $this->params['text'];
        } else if (isset($this->params['text_source'])) {
            // Take text from meta field in mediapool
            $text = static::getMeta($this->params['text_source']);
        }

        // Font size
        $fontSize = 24;
        if (isset($this->params['font_size'])) {
            $fontSize = (int) $this->params['font_size'];
        }

        // Color
        $color = [0, 0, 0];
        if (isset($this->params['color_r'])) {
            $color[0] = (int) $this->params['color_r'];
        }

        if (isset($this->params['color_g'])) {
            $color[1] = (int) $this->params['color_g'];
        }

        if (isset($this->params['color_b'])) {
            $color[2] = (int) $this->params['color_b'];
        }

        // Transparency
        $alpha = 0;
        if (isset($this->params['alpha'])) {
            $alpha = (int) $this->params['alpha'];
        }

        // Padding
        $padding = [0, 0];
        if (isset($this->params['padding_x'])) {
            $padding[0] = (int) $this->params['padding_x'];
        }

        if (isset($this->params['padding_y'])) {
            $padding[1] = (int) $this->params['padding_y'];
        }

        $position = ['right', 'bottom'];
        // Horizontal align: left/center/right
        if (isset($this->params['hpos'])) {
            $position[0] = (string) $this->params['hpos'];
        }

        // Vertical align:   top/center/bottom
        if (isset($this->params['vpos'])) {
            $position[1] = (string) $this->params['vpos'];
        }

        // Antialiasing
        $antialiasing = 0;
        if (isset($this->params['antialiasing'])) {
            $antialiasing = (int) $this->params['antialiasing'];
        }

        // -------------------------------------- /CONFIG

        $scale = 1;
        if ($antialiasing > 1) {
            $scale = $antialiasing;
        }

        $box = imagettfbbox($fontSize * $scale, 0, $fontFile, $text);
        $boxWidth = abs($box[6] - $box[2]);

        // Determine cap height
        $box = imagettfbbox($fontSize, 0, $fontFile, 'X É');
        $capHeight = abs($box[7] - $box[1]);

        // Determine descender height
        $box = imagettfbbox($fontSize, 0, $fontFile, 'X Égjpqy');
        $boxHeight = abs($box[7] - $box[1]);

        $fixHeight = $boxHeight - $capHeight;

        $imageWidth = $this->media->getWidth();
        $imageHeight = $this->media->getHeight();

        switch ($position[0]) {
            case 'left':
                $dstX = 0;
                break;
            case 'center':
                $dstX = (int) (($imageWidth - $boxWidth / $scale) / 2);
                break;
            case 'right':
            default:
                $dstX = $imageWidth - $boxWidth;
        }

        switch ($position[1]) {
            case 'top':
                $dstY = 0;
                break;
            case 'middle':
                $dstY = (int) (($imageHeight - $boxHeight) / 2);
                break;
            case 'bottom':
            default:
                $dstY = $imageHeight - $boxHeight;
        }

        // Set blending mode
        imagealphablending($gdImage, true);

        if ($antialiasing > 0) {
            // Create temp image
            $gdTemp = imagecreatetruecolor($boxWidth * $scale, $boxHeight * $scale);

            // Fill transparent
            imagefill($gdTemp, 0, 0, imagecolortransparent($gdTemp));

            // Write text
            imagettftext(
                $gdTemp,
                $fontSize * $scale,
                0,
                0,
                ($boxHeight - $fixHeight) * $scale,
                imagecolorallocatealpha($gdTemp, $color[0], $color[1], $color[2], $alpha),
                $fontFile,
                $text
            );

            // Copy scaled image
            imagecopyresampled(
                $gdImage,
                $gdTemp,
                $dstX + $padding[0],
                $dstY + $padding[1],
                0,
                0,
                $boxWidth,
                $boxHeight,
                $boxWidth * $scale,
                $boxHeight * $scale
            );

            // Free memory
            imagedestroy($gdTemp);

        } else {
            // Write text
            imagettftext(
                $gdImage,
                $fontSize,
                0,
                $dstX + $padding[0],
                $dstY + $padding[1] + $boxHeight - $fixHeight,
                imagecolorallocatealpha($gdImage, $color[0], $color[1], $color[2], $alpha),
                $fontFile,
                $text
            );
        }

        $this->media->setImage($gdImage);
    }

    /**
     * Get value from meta field in mediapool
     * @param string $field Name of field
     * @return string Value of field
     */
    public function getMeta($field = 'title')
    {
        $mediaName = $this->media->getMediaFilename();

        $media = rex_media::get($mediaName);

        return $media->getValue($field);
    }

    /**
     * Get list of meta fields in mediapool plus 'input'
     * @return array
     */
    public function getMetaFields() {

        if (rex_addon::get('metainfo')->isAvailable()) {
            $sql = rex_sql::factory();

            $sql->setQuery('SELECT name FROM ' . rex::getTablePrefix() . 'metainfo_field WHERE `name` LIKE "med_%" ORDER BY priority');

            $result = $sql->getArray();

            $list = array_merge(['input', 'title'], array_column($result, 'name'));

            return $list;
        }

        return ['input'];
    }

    /**
     * Generate form input fields in Redaxo backend
     * @return array
     */
    public function getParams()
    {
        $params =  [
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_text'),
                'name' => 'text',
                'type' => 'string',
                'default' => 'Text',
                'attributes' => ['required' => 'required'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_source'),
                'name' => 'text_source',
                'type' => 'select',
                'options' => static::getMetaFields(),
                'default' => 'custom',
                'attributes' => ['class' => 'selectpicker form-control'],
                'suffix' => '<small class="form-text text-muted">'.rex_i18n::msg('media_manager_effect_insert_text_hint').'</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_font_size'),
                'name' => 'font_size',
                'type' => 'int',
                'default' => 48,
                'attributes' => [
                    'required' => 'required',
                    'pattern' => '[0-9]+',
                ],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_font_file'),
                'name' => 'font_file',
                'type' => 'media',
                'default' => 'oswald-bold.ttf',
                'suffix' => '<small class="form-text text-muted">.ttf, .otf</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_color_r'),
                'name' => 'color_r',
                'type' => 'int',
                'default' => 0,
                'attributes' => ['pattern' => '[01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5]'],
                'suffix' => '<small class="form-text text-muted">0 - 255</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_color_g'),
                'name' => 'color_g',
                'type' => 'int',
                'default' => 255,
                'attributes' => ['pattern' => '[01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5]'],
                'suffix' => '<small class="form-text text-muted">0 - 255</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_color_b'),
                'name' => 'color_b',
                'type' => 'int',
                'default' => 255,
                'attributes' => ['pattern' => '[01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5]'],
                'suffix' => '<small class="form-text text-muted">0 - 255</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_alpha'),
                'name' => 'alpha',
                'type' => 'int',
                'default' => 70,
                'attributes' => ['pattern' => '[0-9]|[0-9][0-9]|1[0-2][0-7]'],
                'suffix' => '<small class="form-text text-muted">0 - 127</small>',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_hpos'),
                'name' => 'hpos',
                'type' => 'select',
                'options' => ['left', 'center', 'right'],
                'default' => 'center',
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_vpos'),
                'name' => 'vpos',
                'type' => 'select',
                'options' => ['top', 'middle', 'bottom'],
                'default' => 'top',
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_padding_x'),
                'name' => 'padding_x',
                'type' => 'int',
                'default' => 0,
                'attributes' => ['pattern' => '-?[0-9]+'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_padding_y'),
                'name' => 'padding_y',
                'type' => 'int',
                'default' => 30,
                'attributes' => ['pattern' => '-?[0-9]+'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_antialiasing'),
                'name' => 'antialiasing',
                'type' => 'select',
                'options' => range(0, 6),
                'default' => 0,
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
        ];

        return $params;
    }

    /**
     * Get name of plugin
     * @return string
     */
    public function getName()
    {
        return rex_i18n::msg('media_manager_effect_insert_text_name');
    }
}
